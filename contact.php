<?php
require_once 'config.php';
require_once 'functions.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="style.css">
        <link
            href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css"
            rel="stylesheet"
        />
        <title> Contact Us </title>
        <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      margin: 0;
    }
    .contact-form {
      max-width: 500px;
      margin: auto;
      background: #ffffff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .contact-form h2 {
      margin-bottom: 20px;
      color: #333333;
    }
    .form-group {
      margin-bottom: 15px;
    }
    label {
      display: block;
      font-weight: bold;
      margin-bottom: 5px;
    }
    input, textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #cccccc;
      border-radius: 6px;
      resize: none;
    }
    button {
      padding: 10px 20px;
      background-color: #007bff;
      border: none;
      color: #ffffff;
      border-radius: 6px;
      cursor: pointer;
    }
    button:hover {
      background-color: #0056b3;
    }
  </style>
    </head>
    <body>
        <header>
        <a href="home.php" class="logo">Thoto & Nene Fresh Live Tilapia and Bangus</a>
        
        <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <nav class="navbar" id="navbar">
            <a href="home.php">Home</a>
            <a href="index.php">Products</a>
            <a href="about.php">About Us</a>
            <a href="contact.php">Contact Us</a>
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
        <div class="hero-image" style="border-bottom:3px solid rgb(255, 40, 40);">
        </div>
        <section class="container">
            <div class="contact-form">
            <h1 style="text-align:center;"> Contact Us </h1></br>
    <h2>You can call our hotline or reach out to our email address and we will get back to you!</h2>
    <label>Email Address: support@loremipsum.com</label>
    <label>Phone Number: 0961-324-3594</label></br>
  </div>
        </section>
        <div class="cart">
            <h2 class="cart-title">Your Cart</h2>
            <div class="cart-content">
            </div>
            <div class="total">
                <div class="total-title">Total</div>
                <div class="total-price">0</div>
            </div>
            <i class="ri-close-line" id="cart-close"></i>
        </div>
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
