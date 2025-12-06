<?php
require_once '../config.php';
require_once 'admin_functions.php';
require_once '../functions.php';

requireAdmin();

$categories  = getCategories();
$message     = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId     = intval($_POST['category_id'] ?? 0);
    $productName    = trim($_POST['product_name'] ?? '');
    $description    = trim($_POST['description'] ?? '');
    $price          = floatval($_POST['price'] ?? 0);
    $stockQuantity  = intval($_POST['stock_quantity'] ?? 0);

    $imageUrl = '';

    // Handle image upload
    if (!empty($_FILES['product_image']['name'])) {
        $upload = uploadProductImage($_FILES['product_image']);

        if ($upload['success']) {
            $imageUrl = $upload['path'];   // uploads/x.jpg
        } else {
            $message = $upload['message'];
            $messageType = 'error';
        }
    } else {
        $message = "Please upload a product image.";
        $messageType = 'error';
    }

    // If no errors, add product
    if (empty($message)) {
        $result = addProduct($categoryId, $productName, $description, $price, $stockQuantity, $imageUrl);

        if ($result['success']) {
            header('Location: manage_products.php?added=1');
            exit;
        } else {
            $message     = $result['message'];
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Product - Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        body { background: #f5f5f5; }
        .admin-container {
            max-width: 800px;
            margin: 100px auto 50px;
            padding: 20px;
        }
        .form-card {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-card h1 {
            margin-top: 0;
            color: #333;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .btn-primary {
            background: #e35f26;
            color: white;
            padding: 14px 30px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: #c54d1f;
        }
        .btn-secondary {
            background: #666;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
        }
        .btn-secondary:hover {
            background: #555;
        }
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        small {
            color: #666;
            font-size: 13px;
        }
    </style>

</head>

<body>
    <header>
        <a href="dashboard.php" class="logo">ADMIN</a>
        <nav class="navbar">
            <a href="dashboard.php">Dashboard</a>
            <a href="manage_products.php">Manage Products</a>
            <a href="../logout_process.php" style="color:#ff4444;">Logout</a>
        </nav>
    </header>

    <div class="admin-container">
        <div class="form-card">
            <h1><i class="ri-add-line"></i> Add New Product</h1>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType === 'error' ? 'error' : 'success'; ?>">
                    <?php echo clean($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <div class="form-group">
                    <label>Category *</label>
                    <select name="category_id" required>
                        <option value="">Select Category</option>

                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['category_id']; ?>">
                                <?php echo clean($cat['category_name']); ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="form-group">
                    <label>Product Name *</label>
                    <input type="text" name="product_name" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"></textarea>
                </div>

                <div class="form-group">
                    <label>Price (PHP/kg) *</label>
                    <input type="number" step="0.01" name="price" required>
                </div>

                <div class="form-group">
                    <label>Stock (kg) *</label>
                    <input type="number" name="stock_quantity" min="0" required>
                </div>

                <div class="form-group">
                    <label>Product Image *</label>
                    <input type="file" name="product_image" accept="image/*" required>
                </div>
                <button type="submit" class="btn-primary">Save Product</button>
            </form>
        </div>
    </div>
</body>
</html>
