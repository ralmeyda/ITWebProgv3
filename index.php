<?php
require_once 'config.php';
require_once 'functions.php';

// Get all active products
$products = getProducts();

// Get cart count if logged in
$cartCount = 0;
if (isLoggedIn()) {
    $cartCount = getCartCount(getCurrentUserId());
}

// Order status announcements (accepted / declined)
$orderAnnouncements = [];
if (isLoggedIn()) {
    $stmt = $pdo->prepare("
        SELECT order_id, status, created_at
        FROM orders
        WHERE user_id = ?
          AND status IN ('accepted','declined')
          AND notified = 0
        ORDER BY created_at DESC
    ");
    $stmt->execute([getCurrentUserId()]);
    $orderAnnouncements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($orderAnnouncements)) {
        $ids = array_column($orderAnnouncements, 'order_id');
        $in  = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $pdo->prepare("UPDATE orders SET notified = 1 WHERE order_id IN ($in)");
        $stmt->execute($ids);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Products</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <style>
        .nav-welcome { color: #333; padding: 0 10px; font-size: 15px; }
        .product-description { font-size: 14px; color: #555; margin: 5px 0; }
    </style>
</head>
<body>
<header>


    <a href="home.php" class="logo">Thoto & Nene Fresh Live Tilapia and Bangus</a>
    <div class="hamburger" id="hamburger">
        <span></span><span></span><span></span>
    </div>

    <nav class="navbar" id="navbar">
        <a href="home.php">Home</a>
        <a href="index.php">Products</a>
        <a href="about.php">About Us</a>

        <?php if (isLoggedIn()): ?>
            <span class="nav-welcome">Welcome, <strong><?= clean(getCurrentUsername()); ?></strong></span>
            <a href="profile.php" class="profile-link">Profile</a>
            <a href="logout_process.php" style="color:#ff4444;">Logout</a>
        <?php else: ?>
            <a href="login.php" class="login-link">Login</a>
            <a href="register.php" class="register-link">Register</a>
        <?php endif; ?>
    </nav>

    <div id="cart-icon">
        <i class="ri-shopping-bag-line"></i>
        <span class="cart-item-count"><?= $cartCount > 0 ? (int)$cartCount : ''; ?></span>
    </div>
</header>

<div class="cart">
    <h2 class="cart-title">Your Cart</h2>
    <div class="cart-content"></div>
    <div class="total">
        <div class="total-title">Total</div>
        <div class="total-price">PHP0</div>
    </div>
    <button class="btn-buy">Buy Now</button>
    <i class="ri-close-line" id="cart-close"></i>
</div>
<div id="cart-notification">Product added to cart!</div>

<section class="shop">
    <h1 class="section-title">Shop Products</h1>
    <div class="product-content">
        <?php if (empty($products)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px;">
                <i class="ri-shopping-bag-line" style="font-size: 80px; color: #ddd;"></i>
                <h2 style="color: #666; margin-top: 20px;">No products available yet</h2>
                <p style="color: #999;">Admin is adding products. Please check back later!</p>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="product-box" data-product-id="<?= (int)$product['product_id']; ?>">
                    <div class="img-box">
                        <img src="<?= clean($product['image_url'] ?: 'images/placeholder.jpg'); ?>"
                             alt="<?= clean($product['product_name']); ?>">
                    </div>
                    <h2 class="product-title"><?= clean($product['product_name']); ?></h2>
                    <p class="product-description"><?= nl2br(clean($product['description'])); ?></p>
                    <div class="price-and-cart">
                        <p style="font-weight:600;">PHP<?= number_format($product['price'], 0); ?></p>
                        <span class="price" style="display:none;"><?= $product['price']; ?></span>
                        <button class="add-cart" title="Add to cart">
                            <i class="ri-shopping-bag-line"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
<script>
window.APP = {
    isLoggedIn: <?= json_encode(isLoggedIn()); ?>,
    userId: <?= json_encode(isLoggedIn() ? getCurrentUserId() : null); ?>
};
</script>
<script src="script.js?v=<?= time(); ?>"></script>
<script>
    const hamburger = document.getElementById('hamburger');
    const nav = document.getElementById('navbar');

<?php include 'footer.php'; ?>
</body>
</html>
    hamburger.addEventListener('click', () => nav.classList.toggle('active'));
});
</script>
</body>
</html>
