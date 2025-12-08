<?php
require_once 'config.php';
require_once 'functions.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title> CYCRIDE - Home </title>
        <link rel="stylesheet" href="style.css">
        <link
            href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css"
            rel="stylesheet"
        />
    </head>
    <body>
        <header>
        <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <a href="home.php" class="logo">CYCRIDE</a>
        <nav class="navbar" id="navbar">
            <a href="home.php">Home</a>
            <a href="index.php">Products</a>
            <a href="about.php">About Us</a>
            <?php if (isLoggedIn()): ?>
                <span id="welcome-msg" class="nav-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="profile.php" class="profile-link">Profile</a>
                <a href="logout_process.php" class="logout-link">Logout</a>
            <?php else: ?>
                <a href="login.php" class="login-link">Login</a>
                <a href="register.php" class="register-link">Register</a>
            <?php endif; ?>
        </nav>
        
        <div id="cart-icon">
            <i class="ri-shopping-bag-line"></i>
            <span class="cart-item-count"></span>
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
        <div class="hero-image" style="height:650px; width:100%; background-color:#cccccc; display:flex; justify-content:center; align-items:center; font-size:30px; font-weight:bold; color:#333;border-bottom:3px solid rgb(255, 40, 40);">
        </div>        
        <h1 style="text-align:center; font-weight:600px; padding-top:45px;"> Welcome to <strong>CYCRIDE</strong> your one stop shop ride </h1>
        <h2 style="text-align:center; font-weight:600px; padding-top:45px;"> Products that we offer </h2>
        <section class="brand-logos">
            <img src="img/treklogo.jpg" alt="Trek">
            <img src="img/sworkslogo.jpg" alt="S-Works">
            <img src="img/cervelologo.png" alt="Cervelo">
            <img src="img/giantlogo.jpg" alt="Giant">
        </section>
        <div class="buy-now-wrapper">
        <a href="index.php" class="buy-now-btn">Buy Now</a>
        </div>
        <script>
        window.APP = {
            isLoggedIn: <?= json_encode(isLoggedIn()); ?>,
            userId: <?= json_encode(isLoggedIn() ? getCurrentUserId() : null); ?>
        };
        </script>
        <script src="script.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const hamburger = document.getElementById('hamburger');
            const nav = document.getElementById('navbar');

            hamburger.addEventListener('click', () => {
                nav.classList.toggle('active');
            });
        });
    </script>

        <?php include 'footer.php'; ?>
    </body>
</html>
