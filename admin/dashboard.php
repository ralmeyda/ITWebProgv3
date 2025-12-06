
<?php
require_once '../functions.php';
require_once 'admin_functions.php'; // ✅ make sure the file path is correct

// ✅ Optionally include authentication check
// requireAdmin();

// ✅ Fetch dashboard stats
$stats = getDashboardStats();

// ✅ You can set $username if you have session data
$username = $_SESSION['username'] ?? 'Admin';


?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; }
        .admin-container {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 20px;
        }
        .admin-header {
            background: #333;
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .admin-header h1 {
            margin: 0 0 20px 0;
            font-size: 32px;
        }
        .admin-nav {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .admin-nav a {
            background: #e35f26;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .admin-nav a:hover {
            background: #c54d1f;
            transform: translateY(-2px);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .stat-card i {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .stat-card h3 {
            font-size: 36px;
            margin: 10px 0;
            font-weight: 700;
        }
        .stat-card p {
            color: #666;
            font-size: 14px;
            margin: 0;
        }
        .quick-actions {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .quick-actions h2 {
            margin-top: 0;
            color: #333;
        }
        .quick-actions ul {
            line-height: 2;
            color: #666;
        }
        .logout-btn {
            background: #ff4444;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>
    <header>
        <a href="dashboard.php" class="logo">ADMIN</a>
        <nav class="navbar">
            <span style="color: #333;">Welcome, <strong><?php echo clean($username); ?></strong></span>
            <a href="../logout_process.php" class="logout-btn">Logout</a>
        </nav>
    </header>

    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="ri-dashboard-line"></i> Admin Dashboard</h1>
            <div class="admin-nav">
                <a href="add_product.php">
                    <i class="ri-add-line"></i> Add Product
                </a>
                <a href="manage_products.php">
                    <i class="ri-list-check"></i> Manage Products
                </a>
                <a href="products.php">
                    <i class="ri-store-2-line"></i> View Store
                </a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="ri-shopping-bag-line" style="color: #e35f26;"></i>
                <h3><?php echo $stats['total_products']; ?></h3>
                <p>Active Products</p>
            </div>
            <div class="stat-card">
                <i class="ri-user-line" style="color: #3498db;"></i>
                <h3><?php echo $stats['total_users']; ?></h3>
                <p>Total Customers</p>
            </div>
            <a href="admin_orders.php" class="stat-card-link">
                <div class="stat-card">
                    <i class="ri-file-list-line" style="color: #2ecc71;"></i>
                    <h3><?php echo $stats['total_orders']; ?></h3>
                    <p>Orders</p>
                </div>
            </a>
            <div class="stat-card">
                <i class="ri-money-dollar-circle-line" style="color: #f39c12;"></i>
                <h3>₱<?php echo number_format($stats['total_revenue'], 2); ?></h3>
                <p>Total Revenue</p>
            </div>
            <div class="stat-card">
                <i class="ri-alert-line" style="color: #e74c3c;"></i>
                <h3><?php echo $stats['low_stock']; ?></h3>
                <p>Low Stock Items</p>
            </div>
    </div>  
</body>
</html>
