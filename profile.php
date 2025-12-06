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

$stmt = $conn->prepare("SELECT phone, address FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userRow = $result->fetch_assoc();
$phone = $userRow['phone'] ?? '';
$address = $userRow['address'] ?? '';
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
            <p><strong>First Name:</strong> <span id="displayFirst"><?php echo htmlspecialchars($firstName); ?></span></p>
            <p><strong>Last Name:</strong> <span id="displayLast"><?php echo htmlspecialchars($lastName); ?></span></p>
            <p><strong>Phone Number:</strong> <span id="displayPhone"><?php echo htmlspecialchars($phone); ?></span></p>
            <p><strong>Address:</strong> <span id="displayAddress"><?php echo nl2br(htmlspecialchars($address)); ?></span></p>
            <p><strong>Email Address:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
            <div style="margin-top: 20px;">
                <button id="editProfileBtn" class="btn">Edit Profile</button>
                <a href="logout_process.php" style="background-color: #e74c3c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-left:70px;">Logout</a>
            </div>
        </div>

        <div id="editProfile" style="display:none; margin-top:20px;">
            <h3>Edit Profile</h3>
            <div id="profileMessage" style="display:none; padding:8px; border-radius:4px; margin-bottom:10px;"></div>
            <form id="profileForm">
                <input type="text" id="firstNameInput" placeholder="First Name" required value="<?= htmlspecialchars($firstName); ?>">
                <input type="text" id="lastNameInput" placeholder="Last Name" required value="<?= htmlspecialchars($lastName); ?>">
                <input type="tel" id="phoneInput" placeholder="Phone Number" required value="<?= htmlspecialchars($phone); ?>">
                <textarea id="addressInput" placeholder="Address" required style="min-height:80px;"><?= htmlspecialchars($address); ?></textarea>
                <button type="submit" class="btn btn-accept">Save Changes</button>
                <button type="button" id="cancelEdit" class="btn" style="margin-left:8px;">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const hamburger = document.getElementById('hamburger');
            const nav = document.getElementById('navbar');
            hamburger.addEventListener('click', () => {
                nav.classList.toggle('active');
            });
            const editBtn = document.getElementById('editProfileBtn');
            const editDiv = document.getElementById('editProfile');
            const profileDiv = document.getElementById('userProfile');
            const cancelBtn = document.getElementById('cancelEdit');
            const profileForm = document.getElementById('profileForm');
            const messageDiv = document.getElementById('profileMessage');

            editBtn.addEventListener('click', () => {
                editDiv.style.display = 'block';
                profileDiv.style.display = 'none';
                window.scrollTo({top: editDiv.offsetTop - 60, behavior: 'smooth'});
            });

            cancelBtn.addEventListener('click', () => {
                editDiv.style.display = 'none';
                profileDiv.style.display = 'block';
            });

            profileForm.addEventListener('submit', function(e) {
                e.preventDefault();
                messageDiv.style.display = 'block';
                messageDiv.style.background = '#d1ecf1';
                messageDiv.style.color = '#0c5460';
                messageDiv.textContent = 'Saving...';

                const fd = new FormData();
                fd.append('firstName', document.getElementById('firstNameInput').value);
                fd.append('lastName', document.getElementById('lastNameInput').value);
                fd.append('phone', document.getElementById('phoneInput').value);
                fd.append('address', document.getElementById('addressInput').value);

                fetch('profile_update.php', { method: 'POST', body: fd })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            messageDiv.style.background = '#d4edda';
                            messageDiv.style.color = '#155724';
                            messageDiv.textContent = data.message;
                            // update display fields
                            document.getElementById('displayFirst').textContent = data.firstName;
                            document.getElementById('displayLast').textContent = data.lastName;
                            document.getElementById('displayPhone').textContent = data.phone;
                            document.getElementById('displayAddress').innerHTML = data.address.replace(/\n/g, '<br>');
                            // hide edit
                            setTimeout(() => {
                                messageDiv.style.display = 'none';
                                editDiv.style.display = 'none';
                                profileDiv.style.display = 'block';
                            }, 1000);
                        } else {
                            messageDiv.style.background = '#f8d7da';
                            messageDiv.style.color = '#721c24';
                            messageDiv.textContent = data.message || 'Failed to update profile';
                        }
                    })
                    .catch(err => {
                        messageDiv.style.background = '#f8d7da';
                        messageDiv.style.color = '#721c24';
                        messageDiv.textContent = 'Network error';
                        console.error(err);
                    });
            });
        });
    </script>
    <?php include 'footer.php'; ?>
</body>
</html>