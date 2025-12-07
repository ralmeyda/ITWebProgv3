<?php
// admin/restore_product.php
require_once '../config.php';
require_once 'admin_functions.php';
require_once '../functions.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = intval($_POST['product_id'] ?? 0);

    if ($productId <= 0) {
        header('Location: manage_products.php?error=Invalid+product+ID');
        exit;
    }

    $result = restoreProduct($productId);

    if ($result['success']) {
        header('Location: manage_products.php?restored=1');
    } else {
        header('Location: manage_products.php?error=' . urlencode($result['message']));
    }
    exit;
} else {
    header('Location: manage_products.php');
    exit;
}
require_once '../config.php';
require_once 'admin_functions.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = intval($_POST['product_id'] ?? 0);
    
    if ($productId > 0) {
        $stmt = $conn->prepare("UPDATE products SET is_active = TRUE WHERE product_id = ?");
        $stmt->bind_param("i", $productId);
        
        if ($stmt->execute()) {
            header('Location: manage_products.php?restored=1');
        } else {
            header('Location: manage_products.php?error=restore_failed');
        }
    }
}
exit;
?>
