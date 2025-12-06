<?php
require_once '../config.php';
require_once 'admin_functions.php';
require_once '../functions.php';

requireAdmin();

$productId = intval($_GET['id'] ?? 0);
$product = getProductById($productId);

if (!$product) {
    header('Location: manage_products.php');
    exit;
}

$categories = getCategories();
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = intval($_POST['category_id']);
    $productName = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stockQuantity = intval($_POST['stock_quantity']);
    
    $imageUrl = null;
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = uploadProductImage($_FILES['product_image']);
        if ($uploadResult['success']) {
            $imageUrl = 'admin/' . $uploadResult['filepath'];
        } else {
            $message = $uploadResult['message'];
            $messageType = 'error';
        }
    }
    
    if (empty($message)) {
        $result = updateProduct($productId, $categoryId, $productName, $description, $price, $stockQuantity, $imageUrl);
        if ($result['success']) {
            header('Location: manage_products.php?updated=1');
            exit;
        } else {
            $message = $result['message'];
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Product - Admin</title>
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
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .current-image {
            max-width: 200px;
            margin-top: 10px;
            border-radius: 4px;
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
            <a href="../logout_process.php" style="color: #ff4444;">Logout</a>
        </nav>
    </header>

    <div class="admin-container">
        <div class="form-card">
            <h1><i class="ri-edit-line"></i> Edit Product</h1>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo clean($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="category_id">Category *</label>
                    <select name="category_id" id="category_id" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['category_id']; ?>"
                                    <?php echo ($category['category_id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                <?php echo clean($category['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="product_name">Product Name *</label>
                    <input type="text" name="product_name" id="product_name" required 
                           value="<?php echo clean($product['product_name']); ?>">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description"><?php echo clean($product['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="price">Price (PHP) *</label>
                    <input type="number" name="price" id="price" step="0.01" min="0" required 
                           value="<?php echo $product['price']; ?>">
                </div>

                <div class="form-group">
                    <label for="stock_quantity">Stock Quantity *</label>
                    <input type="number" name="stock_quantity" id="stock_quantity" min="0" required 
                           value="<?php echo $product['stock_quantity']; ?>">
                </div>
                <button type="submit" class="btn-primary">
                    <i class="ri-save-line"></i> Update Product
                </button>
                
                <a href="manage_products.php" class="btn-secondary">
                    <i class="ri-arrow-left-line"></i> Back to Products
                </a>
            </form>
        </div>
    </div>
</body>
</html>
