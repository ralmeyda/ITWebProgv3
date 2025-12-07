<?php
// admin/admin_functions.php

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/functions.php';

function requireAdmin()
{
    if (empty($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'admin') {
        header('Location: ../login.php');
        exit;
    }
}
function uploadProductImage(array $file): array
{
    $targetDir = dirname(__DIR__) . '/uploads/';

    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $originalName = basename($file['name']);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($ext, $allowed)) {
        return [
            'success' => false,
            'message' => 'Invalid file type. Allowed: JPG, JPEG, PNG, GIF.'
        ];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'message' => 'Upload error code: ' . $file['error']
        ];
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        return [
            'success' => false,
            'message' => 'File too large. Max 5MB.'
        ];
    }

    $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
    $targetFile = $targetDir . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
        return [
            'success' => false,
            'message' => 'Failed to move uploaded file.'
        ];
    }

    // Path stored in DB
    $relativePath = 'uploads/' . $fileName;

    return [
        'success' => true,
        'path'    => $relativePath,
        'message' => 'Uploaded successfully.'
    ];
}

/**
 * Admin: get all products with category info.
 */
function getAllProductsAdmin(): array
{
    global $pdo;

    // Return all products (active and inactive) so admin can manage/restore
    $sql = "SELECT p.*, c.category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            ORDER BY p.product_id DESC";

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Add product (admin).
 * Returns: ['success' => bool, 'message' => string]
 */
function addProduct(int $categoryId, string $name, string $description, float $price, int $stock, string $imageUrl = ''): array
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO products (category_id, product_name, description, price, stock_quantity, image_url, is_active)
            VALUES (:cat, :name, :desc, :price, :stock, :img, 1)
        ");
        $stmt->execute([
            ':cat'   => $categoryId,
            ':name'  => $name,
            ':desc'  => $description,
            ':price' => $price,
            ':stock' => $stock,
            ':img'   => $imageUrl
        ]);

        return ['success' => true, 'message' => 'Product added'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Update stock quantity (admin AJAX).
 */
function updateProduct(int $productId, int $categoryId, string $name, string $description, float $price, int $stock, ?string $imageUrl = null): array
{
    global $pdo;

    try {
        if ($imageUrl) {
            // Update WITH new image
            $stmt = $pdo->prepare("
                UPDATE products
                SET category_id   = :cat,
                    product_name  = :name,
                    description   = :desc,
                    price         = :price,
                    stock_quantity= :stock,
                    image_url     = :img
                WHERE product_id  = :id
            ");
            $stmt->execute([
                ':cat'   => $categoryId,
                ':name'  => $name,
                ':desc'  => $description,
                ':price' => $price,
                ':stock' => $stock,
                ':img'   => $imageUrl,
                ':id'    => $productId
            ]);
        } else {
            // Update WITHOUT image
            $stmt = $pdo->prepare("
                UPDATE products
                SET category_id   = :cat,
                    product_name  = :name,
                    description   = :desc,
                    price         = :price,
                    stock_quantity= :stock
                WHERE product_id  = :id
            ");
            $stmt->execute([
                ':cat'   => $categoryId,
                ':name'  => $name,
                ':desc'  => $description,
                ':price' => $price,
                ':stock' => $stock,
                ':id'    => $productId
            ]);
        }

        return ['success' => true];

    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Soft delete product: mark as inactive.
 */
function deleteProduct(int $productId): array
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("UPDATE products SET is_active = 0 WHERE product_id = :id");
        $stmt->execute([':id' => $productId]);

        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Restore product (if you want a "restore" feature).
 */
function restoreProduct(int $productId): array
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("UPDATE products SET is_active = 1 WHERE product_id = :id");
        $stmt->execute([':id' => $productId]);

        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Dashboard stats (basic version).
 */
function getDashboardStats(): array
{
    global $pdo;

    // Total active products
    $totalProducts = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn();

    // Total non-admin users
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type != 'admin'")->fetchColumn();

    // Total orders
    $totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

    // Total revenue (sum of total_amount for accepted orders)
    $totalRevenue = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'accepted'")
                        ->fetchColumn();

    // Low stock items (<5)
    $lowStock = $pdo->query("SELECT COUNT(*) FROM products WHERE stock_quantity < 5 AND is_active = 1")
                    ->fetchColumn();

    return [
        'total_products' => (int)$totalProducts,
        'total_users'    => (int)$totalUsers,
        'total_orders'   => (int)$totalOrders,
        'total_revenue'  => (float)$totalRevenue,
        'low_stock'      => (int)$lowStock,
    ];
}
