<?php
require_once '../config.php';
require_once '../functions.php';
require_once 'admin_functions.php';

requireAdmin();

try {
    $cols = [
        'paid' => $pdo->query("SHOW COLUMNS FROM orders LIKE 'paid'")->fetch(),
        'picked_up' => $pdo->query("SHOW COLUMNS FROM orders LIKE 'picked_up'")->fetch(),
        'paid_at' => $pdo->query("SHOW COLUMNS FROM orders LIKE 'paid_at'")->fetch(),
        'picked_up_at' => $pdo->query("SHOW COLUMNS FROM orders LIKE 'picked_up_at'")->fetch(),
    ];
    $alterSql = [];
    if (!$cols['paid']) $alterSql[] = "ADD COLUMN paid TINYINT(1) NOT NULL DEFAULT 0";
    if (!$cols['picked_up']) $alterSql[] = "ADD COLUMN picked_up TINYINT(1) NOT NULL DEFAULT 0";
    if (!$cols['paid_at']) $alterSql[] = "ADD COLUMN paid_at DATETIME NULL DEFAULT NULL";
    if (!$cols['picked_up_at']) $alterSql[] = "ADD COLUMN picked_up_at DATETIME NULL DEFAULT NULL";
    if (!empty($alterSql)) {
        $pdo->exec('ALTER TABLE orders ' . implode(', ', $alterSql));
    }
} catch (Exception $e) {
    error_log('MIGRATE ORDERS COLUMNS ERROR: ' . $e->getMessage());
}

// Handle accept/decline and toggles (paid/picked)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['action'])) {
    $orderId = (int)$_POST['order_id'];
    $action  = $_POST['action'];

    // Allowed actions
    $allowed = ['accepted','declined','mark_paid','mark_unpaid','mark_picked','mark_unpicked'];
    if (!in_array($action, $allowed, true)) {
    } elseif ($action === 'accepted') {
        try {
            // Start transaction to validate and update stock atomically
            $pdo->beginTransaction();

            // Fetch order items with current stock
            $itStmt = $pdo->prepare(
                "SELECT oi.product_id, oi.quantity, p.stock_quantity, p.product_name
                 FROM order_items oi
                 JOIN products p ON p.product_id = oi.product_id
                 WHERE oi.order_id = ?"
            );
            $itStmt->execute([$orderId]);
            $orderItems = $itStmt->fetchAll(PDO::FETCH_ASSOC);

            // Validate stock availability
            foreach ($orderItems as $it) {
                if ((int)$it['stock_quantity'] < (int)$it['quantity']) {
                    $pdo->rollBack();
                    $message = "Cannot accept Order #{$orderId}: insufficient stock for '" . clean($it['product_name']) . "' (available: " . (int)$it['stock_quantity'] . ", required: " . (int)$it['quantity'] . ").";
                    // Do not change status — admin must decide how to proceed
                    goto _admin_orders_done;
                }
            }

            // Decrement stock for each item
            $decStmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
            foreach ($orderItems as $it) {
                $decStmt->execute([(int)$it['quantity'], (int)$it['product_id']]);
            }

            // Update order status to accepted
            $uStmt = $pdo->prepare("UPDATE orders SET status = ?, notified = 0 WHERE order_id = ?");
            $uStmt->execute(['accepted', $orderId]);

            $pdo->commit();
            $message = "Order #{$orderId} has been accepted and stock updated.";
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log('ADMIN ACCEPT ERROR: ' . $e->getMessage());
            $message = "Failed to accept order #{$orderId}. See logs.";
        }
    } elseif ($action === 'declined') {
        // Simply mark declined; stock was not changed at purchase time
        $stmt = $pdo->prepare("UPDATE orders SET status = ?, notified = 0 WHERE order_id = ?");
        $stmt->execute(['declined', $orderId]);
        $message = "Order #{$orderId} has been declined.";
    } elseif ($action === 'mark_paid' || $action === 'mark_unpaid' || $action === 'mark_picked' || $action === 'mark_unpicked') {
        // Toggle paid / picked_up fields. Only allow toggling for accepted orders.
        $orderRow = $pdo->prepare("SELECT status FROM orders WHERE order_id = ?");
        $orderRow->execute([$orderId]);
        $ord = $orderRow->fetch(PDO::FETCH_ASSOC);
        if (!$ord) {
            $message = "Order not found.";
        } elseif ($ord['status'] !== 'accepted') {
            $message = "Only accepted orders can be marked paid/picked.";
        } else {
            if ($action === 'mark_paid') {
                $u = $pdo->prepare("UPDATE orders SET paid = 1, paid_at = NOW() WHERE order_id = ?");
                $u->execute([$orderId]);
                $message = "Order #{$orderId} marked as paid.";
            } elseif ($action === 'mark_unpaid') {
                $u = $pdo->prepare("UPDATE orders SET paid = 0, paid_at = NULL WHERE order_id = ?");
                $u->execute([$orderId]);
                $message = "Order #{$orderId} marked as unpaid.";
            } elseif ($action === 'mark_picked') {
                $u = $pdo->prepare("UPDATE orders SET picked_up = 1, picked_up_at = NOW() WHERE order_id = ?");
                $u->execute([$orderId]);
                $message = "Order #{$orderId} marked as picked up.";
            } elseif ($action === 'mark_unpicked') {
                $u = $pdo->prepare("UPDATE orders SET picked_up = 0, picked_up_at = NULL WHERE order_id = ?");
                $u->execute([$orderId]);
                $message = "Order #{$orderId} marked as not picked up.";
            }
        }
    }
}

_admin_orders_done:;

// Fetch orders (non-admin users)
$stmt = $pdo->query(" 
    SELECT o.*, u.username, u.first_name, u.last_name, u.address, u.phone
    FROM orders o
    JOIN users u ON u.user_id = o.user_id
    WHERE u.user_type != 'admin'
    ORDER BY o.order_id DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper to get items per order
$itemStmt = $pdo->prepare("
    SELECT oi.*, p.product_name
    FROM order_items oi
    JOIN products p ON p.product_id = oi.product_id
    WHERE oi.order_id = ?
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Orders</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left;}
        th { background: #f7f7f7; }
        .btn { padding: 5px 12px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-accept { background: #4CAF50; color: white; }
        .btn-decline { background: #f44336; color: white; }
        .status { font-weight: bold; }
        .accepted { color: green; }
        .declined { color: red; }
    </style>
</head>
<body>
<header>
    <a href="dashboard.php" class="logo">ADMIN</a>
    <nav class="navbar">
        <a href="dashboard.php">Dashboard</a>
        <a href="add_product.php">Add Product</a>
        <a href="manage_products.php">Manage Products</a>
        <a href="../logout_process.php" style="color:#ff4444;">Logout</a>
    </nav>
</header>

<div class="admin-container" style="margin-top: 80px; padding: 20px;">
    <h1>Customer Orders</h1>

    <?php if (!empty($message)): ?>
        <p style="color:green;"><?= clean($message); ?></p>
    <?php endif; ?>

    

    <?php if (empty($orders)): ?>
        <p>No orders yet.</p>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Address</th>
                <th>Contact</th>
                <th>Items</th>
                <th>Total</th>
                <th>Ordered At</th>
                <th>Status</th>
                <th>Paid</th>
                <th>Picked Up</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <?php
                $itemStmt->execute([$order['order_id']]);
                $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <tr>
                    <td><?= (int)$order['order_id']; ?></td>
                    <td><?= clean($order['first_name'] . ' ' . $order['last_name']); ?></td>
                    <td><?= clean($order['address']); ?></td>
                    <td><?= clean($order['phone']); ?></td>
                    <td>
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $it): ?>
                                <?= clean($it['product_name']); ?> x <?= (int)$it['quantity']; ?>
                                (PHP<?= number_format($it['price'], 2); ?>)<br>
                            <?php endforeach; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>PHP<?= number_format($order['total_amount'], 2); ?></td>
                    <td><?= date('Y-m-d H:i:s', strtotime($order['created_at'] ?? 'now')); ?></td>
                    <td class="status <?= clean($order['status']); ?>"><?= ucfirst(clean($order['status'])); ?></td>
                    <td>
                        <?php if ($order['status'] === 'accepted'): ?>
                            <div style="display:flex;flex-direction:column;gap:6px;">
                                <div>
                                    <?= $order['paid'] ? '<span style="color:green; font-weight:600;">Yes</span>' : '<span style="color:#888;">No</span>'; ?>
                                </div>
                                <div>
                                    <?php if ($order['paid']): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="order_id" value="<?= (int)$order['order_id']; ?>">
                                            <button type="submit" name="action" value="mark_unpaid" class="btn">Mark Unpaid</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="order_id" value="<?= (int)$order['order_id']; ?>">
                                            <button type="submit" name="action" value="mark_paid" class="btn btn-accept">Mark Paid</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($order['paid_at'])): ?>
                                    <small style="color:#666;"><?= date('Y-m-d H:i', strtotime($order['paid_at'])); ?></small>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($order['status'] === 'accepted'): ?>
                            <div style="display:flex;flex-direction:column;gap:6px;">
                                <div>
                                    <?= $order['picked_up'] ? '<span style="color:green; font-weight:600;">Yes</span>' : '<span style="color:#888;">No</span>'; ?>
                                </div>
                                <div>
                                    <?php if ($order['picked_up']): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="order_id" value="<?= (int)$order['order_id']; ?>">
                                            <button type="submit" name="action" value="mark_unpicked" class="btn">Mark Not Picked</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="order_id" value="<?= (int)$order['order_id']; ?>">
                                            <button type="submit" name="action" value="mark_picked" class="btn btn-accept">Mark Picked</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($order['picked_up_at'])): ?>
                                    <small style="color:#666;"><?= date('Y-m-d H:i', strtotime($order['picked_up_at'])); ?></small>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($order['status'] === 'pending'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?= (int)$order['order_id']; ?>">
                                <button type="submit" name="action" value="accepted" class="btn btn-accept">Accept</button>
                                <button type="submit" name="action" value="declined" class="btn btn-decline">Decline</button>
                            </form>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
