<?php
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get user data from session
$userId = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? null;
$email = $_SESSION['email'] ?? null;
$firstName = $_SESSION['first_name'] ?? null;
$lastName = $_SESSION['last_name'] ?? null;

// Fetch additional user data from database including phone
$stmt = $conn->prepare("SELECT phone FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userRow = $result->fetch_assoc();
$phone = $userRow['phone'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile - CYCRIDE</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
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
            <a href="profile.php" class="profile-link">Profile</a>
        </nav>

        <div id="cart-icon">
            <i class="ri-shopping-bag-line"></i>
            <span class="cart-item-count"></span>
        </div>
    </header>

    <div class="form-container">
        <h2>User Profile</h2>
        <div id="userProfile">
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($firstName); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($lastName); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($phone); ?></p>
            <p><strong>Email Address:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
            <div style="margin-top: 20px;">
                <a href="logout_process.php" style="background-color: #e74c3c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Logout</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
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