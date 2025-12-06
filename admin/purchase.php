<?php
require_once '../config.php';
require_once '../functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$cartItems = $data['cart'] ?? [];

if (empty($cartItems)) {
    echo json_encode(['success' => false, 'message' => 'Your cart is empty.']);
    exit;
}

$userId = getCurrentUserId();
$totalAmount = 0;

// Validate stock & compute total
foreach ($cartItems as $item) {
    $productId = (int)$item['product_id'];
    $qty       = (int)$item['quantity'];
    $price     = (float)$item['price'];

    $stmt = $pdo->prepare("SELECT stock_quantity, product_name FROM products WHERE product_id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
        exit;
    }

    if ($product['stock_quantity'] < $qty) {
        echo json_encode([
            'success' => false,
            'message' => 'Insufficient stock for ' . $product['product_name'] .
                         '. Available: ' . $product['stock_quantity']
        ]);
        exit;
    }

    $totalAmount += $price * $qty;
}

try {
    $pdo->beginTransaction();

    // Create order
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, total_amount, status, source, notified)
        VALUES (?, ?, 'pending', 'shop', 0)
    ");
    $stmt->execute([$userId, $totalAmount]);
    $orderId = $pdo->lastInsertId();

    // Insert items. Stock adjustment is deferred until admin accepts the order.
    $itemStmt = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($cartItems as $item) {
        $productId = (int)$item['product_id'];
        $qty       = (int)$item['quantity'];
        $price     = (float)$item['price'];

        $itemStmt->execute([$orderId, $productId, $qty, $price]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your purchase!',
        'order_id' => $orderId
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log('ORDER ERROR: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to place order.']);
}
