<?php
require_once 'config.php';
require_once 'functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
    <header>
        <a href="home.php" class="logo">CYCRIDE</a>
        
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
    
    <div class="form-container" style="margin-top: 120px;">
        <h2>Login</h2>
        <div id="login-message" style="display:none; padding: 10px; margin-bottom: 15px; border-radius: 5px;"></div>
        <form id="loginForm">
            <input type="text" id="loginUsername" placeholder="Username" required>
            <input type="password" id="loginPassword" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php" style="color:orange;">Register here</a></p>
    </div>
    
    <script>
        document.getElementById("loginForm").addEventListener("submit", function(e) {
            e.preventDefault();
            
            const messageDiv = document.getElementById('login-message');
            const formData = new FormData();
            formData.append('username', document.getElementById("loginUsername").value);
            formData.append('password', document.getElementById("loginPassword").value);
            
            // Show loading
            messageDiv.style.display = 'block';
            messageDiv.style.background = '#d1ecf1';
            messageDiv.style.color = '#0c5460';
            messageDiv.textContent = 'Logging in...';
            
            fetch('login_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageDiv.style.background = '#d4edda';
                    messageDiv.style.color = '#155724';
                    messageDiv.textContent = data.message;
                    
                    // Store minimal data in localStorage for JS access
                    localStorage.setItem("username", data.user.username);
                    localStorage.setItem("userId", data.user.user_id);
                    
                    // Redirect based on user type
                    setTimeout(() => {
                        if (data.user.user_type === 'admin') {
                            window.location.href = "admin/dashboard.php";
                        } else {
                            window.location.href = "index.php";
                        }
                    }, 500);
                } else {
                    messageDiv.style.background = '#f8d7da';
                    messageDiv.style.color = '#721c24';
                    messageDiv.textContent = data.message;
                }
            })
            .catch(error => {
                messageDiv.style.background = '#f8d7da';
                messageDiv.style.color = '#721c24';
                messageDiv.textContent = "Login failed. Please try again.";
                console.error('Error:', error);
            });
        });
        
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
