<?php
require_once '../config.php';
require_once 'admin_functions.php';
require_once '../functions.php';

requireAdmin();

$products = getAllProductsAdmin();

// Alert Messages - Success messages
$successMessage = '';
if (isset($_GET['added'])) {
    $successMessage = 'Product added successfully!';
} elseif (isset($_GET['updated'])) {
    $successMessage = 'Product updated successfully!';
} elseif (isset($_GET['deleted'])) {
    $successMessage = 'Product Deleted successfully!';
} elseif (isset($_GET['error'])) {
    $successMessage = 'Operation failed. Please try again.';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Products - Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .stock-control {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-stock {
            width: 28px;
            height: 28px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #f5f5f5;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-stock:hover {
            background: #e0e0e0;
        }
        body { background: #f5f5f5; }
        .admin-container {
            max-width: 1400px;
            margin: 100px auto 50px;
            padding: 20px;
        }
        .admin-container h1 {
            margin-bottom: 20px;
            color: #333;
        }
        .products-table {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f5f5f5;
            font-weight: 600;
            color: #333;
        }
        tr:hover {
            background: #f9f9f9;
        }
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-active {
            background: #d4edda;
            color: #155724;
        }
        .badge-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .badge-low-stock {
            background: #fff3cd;
            color: #856404;
        }
        .action-btns {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn-edit {
            background: #3498db;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }
        .btn-edit:hover {
            background: #2980b9;
        }
        .btn-restore {
            background: #2ecc71;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }
        .btn-restore:hover {
            background: #27ae60;
        }
        .btn-delete {
            background: #e74c3c;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }
        .btn-delete:hover {
            background: #c0392b;
        }
        .btn-add {
            background: #e35f26;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
        .btn-add:hover {
            background: #c54d1f;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        .inactive-row {
            background: #fff3cd !important;
        }
        .inactive-row:hover {
            background: #ffe69c !important;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            function sanitizeInt(val, fallback = 0) {
                const n = parseInt(val, 10);
                return (isNaN(n) || n < 0) ? fallback : n;
            }

            function sanitizePrice(val, fallback = 0.00) {
                const f = parseFloat(val);
                return (isNaN(f) || f < 0) ? fallback : Math.round(f * 100) / 100;
            }

            // Enter key triggers Save All
            document.querySelectorAll('.stock-input, .price-input').forEach(input => {
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const saveAllBtn = document.getElementById('save-all');
                        if (saveAllBtn) saveAllBtn.click();
                    }
                });
            });

            // Save All changes handler
            document.getElementById('save-all').addEventListener('click', async function () {
                const btn = this;
                const rows = document.querySelectorAll('tbody tr');
                const products = [];

                rows.forEach(row => {
                    const stockInput = row.querySelector('.stock-input');
                    const priceInput = row.querySelector('.price-input');
                    if (!stockInput || !priceInput) return;
                    const id = stockInput.dataset.id;
                    const stock = sanitizeInt(stockInput.value, 0);
                    const price = sanitizePrice(priceInput.value, 0.00);
                    // normalize inputs
                    stockInput.value = stock;
                    priceInput.value = price.toFixed(2);
                    products.push({ product_id: parseInt(id, 10), stock: stock, price: price });
                });

                if (products.length === 0) return;

                btn.disabled = true;
                const originalText = btn.textContent;
                btn.textContent = 'Saving...';
                const summary = document.getElementById('update-summary');

                try {
                    const resp = await fetch('update_product.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ products: products })
                    });
                    const data = await resp.json();

                    if (data.success) {
                        summary.style.color = '#155724';
                        summary.textContent = 'All products updated ✓';
                        products.forEach(p => {
                            const msgEl = document.getElementById('msg-' + p.product_id);
                            if (msgEl) {
                                msgEl.style.display = 'block';
                                msgEl.style.color = '#155724';
                                msgEl.textContent = 'Updated ✓';
                                setTimeout(() => { msgEl.style.display = 'none'; }, 2500);
                            }
                        });
                    } else {
                        summary.style.color = '#721c24';
                        summary.textContent = 'Update failed: ' + (data.message || '');
                    }
                } catch (err) {
                    console.error(err);
                    summary.style.color = '#721c24';
                    summary.textContent = 'Network or server error';
                } finally {
                    btn.disabled = false;
                    btn.textContent = originalText;
                    setTimeout(() => { summary.textContent = ''; }, 3000);
                }
            });

        });
    </script>

</head>
<body>
    <header>
        <a href="dashboard.php" class="logo">ADMIN</a>
        <nav class="navbar">
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">View Store</a>
            <a href="../logout_process.php" style="color: #ff4444;">Logout</a>
        </nav>
    </header>

    <div class="admin-container">
        <h1><i class="ri-list-check"></i> Manage Products</h1>
        
        <?php if ($successMessage): ?>
            <div class="alert-success"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <a href="add_product.php" class="btn-add">
            <i class="ri-add-line"></i> Add New Product
        </a>
        <button id="save-all" class="btn-add" style="margin-left:12px; background:#2b8aeb;">Save All Changes</button>
        <div id="update-summary" style="display:inline-block; margin-left:12px; vertical-align: middle;"></div>

        <div class="products-table">
            <?php if (empty($products)): ?>
                <p style="text-align: center; padding: 40px; color: #666;">
                    No products found. <a href="add_product.php">Add your first product</a>
                </p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr <?php echo !$product['is_active'] ? 'class="inactive-row"' : ''; ?>>
                                <td><?php echo $product['product_id']; ?></td>
                                <td>
                                    <?php if ($product['image_url'] && file_exists('../' . $product['image_url'])): ?>
                                        <img src="../<?php echo clean($product['image_url']); ?>" 
                                             alt="Product" class="product-img">
                                    <?php else: ?>
                                        <div style="width:60px; height:60px; background:#ddd; border-radius:4px; display:flex; align-items:center; justify-content:center;">
                                            <i class="ri-image-line" style="color:#999;"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo clean($product['product_name']); ?></td>
                                <td><?php echo clean($product['category_name']); ?></td>
                                <td>
                                    <input type="number" min="0" step="0.01" value="<?php echo number_format($product['price'], 2, '.', ''); ?>"
                                        id="price-<?php echo $product['product_id']; ?>"
                                        data-id="<?php echo $product['product_id']; ?>"
                                        class="price-input" style="width:100px; text-align:center;">
                                </td>
                                <td>
                                    <input type="number" min="0" value="<?php echo $product['stock_quantity']; ?>" 
                                        id="stock-<?php echo $product['product_id']; ?>" 
                                        data-id="<?php echo $product['product_id']; ?>" 
                                        class="stock-input" style="width:60px; text-align:center;">

                                    <?php if ($product['stock_quantity'] < 5 && $product['stock_quantity'] > 0): ?>
                                        <span class="badge badge-low-stock">Low</span>
                                    <?php elseif ($product['stock_quantity'] == 0): ?>
                                        <span class="badge badge-inactive">Out</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                                    <div class="action-btns">
                                                        <?php if ($product['is_active']): ?>
                                                            <!-- Edit Product -->
                                                            <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn-edit">Edit
                                                            </a>
                                                            <form method="POST" action="delete_product.php" style="display: inline; margin: 0;"
                                                                  onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                                <button type="submit" class="btn-delete">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        <?php else: ?>
                                                        <?php endif; ?>
                                                    </div>
                                    <div class="update-msg" id="msg-<?php echo $product['product_id']; ?>" style="display:none; margin-top:6px; font-size:13px;"></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
