<?php
require_once 'config.php';
require_once 'functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    if (empty($username) || empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    $result = registerUser($username, $email, $password, $firstName, $lastName, $phone);
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
