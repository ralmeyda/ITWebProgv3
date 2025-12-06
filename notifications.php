<?php
require_once 'config.php';
require_once 'functions.php';
requireLogin();

$userId = getCurrentUserId();
$result = $conn->query("SELECT * FROM notifications WHERE user_id=$userId ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
        <a href="notifications.php">
    <i class="ri-notification-3-line"></i>
    </a>
<h2>Your Notifications</h2>
<ul>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($note = $result->fetch_assoc()): ?>
            <li style="margin-bottom:10px;">
                <strong><?php echo date("M d, Y h:i A", strtotime($note['created_at'])); ?></strong><br>
                <?php echo clean($note['message']); ?>
            </li>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No notifications yet.</p>
    <?php endif; ?>
</ul>
    <?php include 'footer.php'; ?>
</body>
</html>
