<?php
// admin/update_stock.php
require_once '../config.php';
require_once 'admin_functions.php';
require_once '../functions.php';

requireAdmin();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$productId = intval($data['product_id'] ?? 0);
$stock     = intval($data['stock'] ?? -1);

if ($productId <= 0 || $stock < 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$result = updateProductStock($productId, $stock);
echo json_encode($result);
exit;
