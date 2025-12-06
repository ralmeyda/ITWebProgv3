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
        <div class="hero-image" style="height:650px; width:100%; background-color:#cccccc; display:flex; justify-content:center; align-items:center; font-size:30px; font-weight:bold; color:#333;border-bottom:3px solid rgb(255, 40, 40);">
        </div>
        <h1 style="text-align:center; font-weight:600px; padding-top:35px;"> About Us  </h1>
        <section class="container">
            <p style="margin-right:260px; padding:25px; padding-left:15%;text-align:center;font-size:25px;"> Thoto & Nene Fresh Live Tilapia and Bangus began as a small family effort that grew out of necessity and opportunity. Before running their stall, the owners—Mr. Thoto and Mrs. Nene—worked different jobs to support their household. Mr. Thoto often helped in transporting fish from suppliers, while Mrs. Nene assisted family members who were already selling in the wet market. Over time, they realized that there was a steady demand for fresh tilapia and bangus in their community, especially among households that preferred freshly harvested fish.
In 2014, the couple decided to put up their own stall after a relative offered them a small space in Bacoor Public Market. They started with only a few tubs of live tilapia, manually transporting them every morning. </br> </br>As more customers returned because of the quality and freshness of their fish, they gradually expanded their offerings to include bangus and boneless bangus.
With growing customer trust, their stall became known in the market for providing fresh, affordable fish. This led them to offer phone-in orders and simple delivery services, which helped meet the needs of regular customers who couldn’t visit the market daily. Despite their success, their operations remained traditional—relying on calls, texts, and handwritten notes to manage orders.
Now, with increasing competition and the shift toward digital buying behaviors, the owners are ready to adapt. Their interest in having a website comes from the desire to organize their orders better, reach more customers, and continue improving their family business.
</p>
        <h2 style="text-align:center; font-weight:bold; padding-top:35px;">Mission </h2>
        <p style="margin-right:260px; padding:25px; padding-left:15%;text-align:center;font-size:25px;"> To provide customers with fresh, high-quality fish while supporting sustainable local fishing practices. </p>
        <h2 style="text-align:center; font-weight:bold; padding-top:35px;"> Vision </h2>
        <p style="margin-right:260px; padding:25px; padding-left:15%;text-align:center;font-size:25px;"> To deliver freshness and satisfaction to every household through affordable and trustworthy seafood products. </p>
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
        </div></br>
        <div class="location" style="text-align:center; padding-bottom:40px;">
            <h2 style="font-weight:bold"> Market Location </h2></br>
            <img src="img/map.png" alt="Market Location">
            <p style="font-weight:bold;">  Address: Bacoor Public Market, Bacoor, Cavite </br>Stall 101</p>
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
