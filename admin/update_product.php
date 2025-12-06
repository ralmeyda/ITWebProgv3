<?php
require_once '../config.php';
require_once 'admin_functions.php';
require_once '../functions.php';

requireAdmin();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['products']) && is_array($data['products'])) {
    // Bulk update
    $products = $data['products'];
    if (empty($products)) {
        echo json_encode(['success' => false, 'message' => 'No products provided']);
        exit;
    }

    try {
        global $pdo;
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("UPDATE products SET stock_quantity = :stock, price = :price WHERE product_id = :id");

        foreach ($products as $p) {
            $productId = intval($p['product_id'] ?? 0);
            $stock = isset($p['stock']) ? intval($p['stock']) : 0;
            $price = isset($p['price']) ? floatval($p['price']) : 0.00;

            if ($productId <= 0) continue;
            $stmt->execute([':stock' => $stock, ':price' => $price, ':id' => $productId]);
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Products updated']);
        exit;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log('BULK UPDATE ERROR: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to update products']);
        exit;
    }

} else {
    // Single update fallback
    $productId = intval($data['product_id'] ?? 0);
    $stock = isset($data['stock']) ? intval($data['stock']) : null;
    $price = isset($data['price']) ? floatval($data['price']) : null;

    if ($productId <= 0 || $stock === null || $price === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        exit;
    }

    try {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE products SET stock_quantity = :stock, price = :price WHERE product_id = :id");
        $stmt->execute([':stock' => $stock, ':price' => $price, ':id' => $productId]);

        echo json_encode(['success' => true, 'message' => 'Product updated']);
        exit;
    } catch (Exception $e) {
        error_log('UPDATE PRODUCT ERROR: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to update product']);
        exit;
    }
}
