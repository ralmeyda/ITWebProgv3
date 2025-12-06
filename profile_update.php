<?php
require_once 'config.php';
require_once 'functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$userId = getCurrentUserId();
$firstName = trim($_POST['firstName'] ?? '');
$lastName = trim($_POST['lastName'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');

if (empty($firstName) || empty($lastName) || empty($phone) || empty($address)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Update user record
$stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, phone = ?, address = ? WHERE user_id = ?");
$stmt->bind_param('ssssi', $firstName, $lastName, $phone, $address, $userId);

if ($stmt->execute()) {
    // Update session values
    $_SESSION['first_name'] = $firstName;
    $_SESSION['last_name'] = $lastName;
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully', 'firstName' => $firstName, 'lastName' => $lastName, 'phone' => $phone, 'address' => $address]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update profile: ' . $conn->error]);
}
