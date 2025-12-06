<?php
require_once 'config.php';

// ============================================
// USER AUTHENTICATION FUNCTIONS
// ============================================

/**
 * Register a new user
 * @param string $username
 * @param string $email
 * @param string $password (plain text - will be hashed)
 * @param string $firstName
 * @param string $lastName
 * @param string $phone
 * @return array ['success' => bool, 'message' => string]
 */

function clean($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function registerUser($username, $email, $password, $firstName, $lastName, $phone) {
    global $conn;
    
    // Validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'All fields are required'];
    }
    
    // Check if username already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        return ['success' => false, 'message' => 'Username already exists'];
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    // Hash password (IMPORTANT)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, first_name, last_name, phone) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $email, $hashedPassword, $firstName, $lastName, $phone);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Registration successful'];
    } else {
        return ['success' => false, 'message' => 'Registration failed: ' . $conn->error];
    }
}

/**
 * Login user
 * @param string $username
 * @param string $password (plain text - will be verified against hash)
 * @return array ['success' => bool, 'message' => string, 'user' => array]
 */
function loginUser($username, $password) {
    global $conn;
    
    // Get user from database
    $stmt = $conn->prepare("SELECT user_id, username, email, password, first_name, last_name, user_type FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['success' => false, 'message' => 'Invalid username or password'];
    }
    
    $user = $result->fetch_assoc();
    
    // Verify password
    if (password_verify($password, $user['password'])) {
        // Update last login
        $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
        $updateStmt->bind_param("i", $user['user_id']);
        $updateStmt->execute();
        
        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['user_type'] = $user['user_type'];
        
        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'user_type' => $user['user_type']
            ]
        ];
    } else {
        return ['success' => false, 'message' => 'Invalid username or password'];
    }
}

/**
 * Logout user
 */
function logoutUser() {
    session_unset();
    session_destroy();
    return ['success' => true, 'message' => 'Logged out successfully'];
}

// xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
// PRODUCT FUNCTIONS
// xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

/**
 * Get all active products
 * @param int $categoryId (optional) Filter by category
 * @return array of products
 */
function createOrder($userId, $cartItems) {
    global $pdo;

    if (empty($cartItems)) {
        return false;
    }

    // Compute total price
    $totalPrice = 0;
    foreach ($cartItems as $item) {
        $totalPrice += $item['price'] * $item['quantity'];
    }

    // Insert order
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, products, total_price, status, source, notified)
        VALUES (:user_id, :products, :total_price, 'pending', 'shop', 0)
    ");
    $stmt->execute([
        ':user_id'     => $userId,
        ':products'    => json_encode($cartItems),
        ':total_price' => $totalPrice
    ]);

    return $pdo->lastInsertId();
}
function getProducts($categoryId = null) {
    global $conn;
    
    $sql = "SELECT p.*, c.category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE p.is_active = TRUE 
              AND p.stock_quantity > 0"; // ✅ hide zero-stock items
    
    if ($categoryId) {
        $sql .= " AND p.category_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $categoryId);
    } else {
        $sql .= " ORDER BY p.created_at DESC";
        $stmt = $conn->prepare($sql);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    return $products;
}

/**
 * Get single product by ID
 */
function getProductById($productId) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id WHERE p.product_id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
// CART FUNCTIONS
// vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv

/**
 * Add item to cart
 */
function addToCart($userId, $productId, $quantity = 1) {
    global $conn;
    
    // Check if product exists and is active
    $stmt = $conn->prepare("SELECT product_id, stock_quantity FROM products WHERE product_id = ? AND is_active = TRUE");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['success' => false, 'message' => 'Product not found'];
    }
    
    $product = $result->fetch_assoc();
    if ($product['stock_quantity'] < $quantity) {
        return ['success' => false, 'message' => 'Insufficient stock'];
    }
    
    // Check IF item already in cart
    $stmt = $conn->prepare("SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update quantity
        $cartItem = $result->fetch_assoc();
        $newQuantity = $cartItem['quantity'] + $quantity;
        
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
        $stmt->bind_param("ii", $newQuantity, $cartItem['cart_id']);
        $stmt->execute();
        
        return ['success' => true, 'message' => 'Cart updated'];
    } else {
        // Insert new item
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $userId, $productId, $quantity);
        $stmt->execute();
        
        return ['success' => true, 'message' => 'Item added to cart'];
    }
}

/**
 * Get user cart items
 */


/** 
 * CART COUNT 
*/
function getCartCount($userId) {
    global $conn;

    $query = "SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row['total_items'] ?? 0;
}

function getCartItems($userId) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT c.cart_id, c.quantity, c.added_at,
               p.product_id, p.product_name, p.price, p.image_url, p.stock_quantity
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.user_id = ? AND p.is_active = TRUE
        ORDER BY c.added_at DESC
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cartItems = [];
    $total = 0;
    
    while ($row = $result->fetch_assoc()) {
        $subtotal = $row['price'] * $row['quantity'];
        $row['subtotal'] = $subtotal;
        $total += $subtotal;
        $cartItems[] = $row;
    }
    
    return ['items' => $cartItems, 'total' => $total];
}

/**
 * Update cart item quantity
 */
function updateCartQuantity($cartId, $userId, $quantity) {
    global $conn;
    
    if ($quantity <= 0) {
        return removeFromCart($cartId, $userId);
    }
    
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ? AND user_id = ?");
    $stmt->bind_param("iii", $quantity, $cartId, $userId);
    $stmt->execute();
    
    return ['success' => true, 'message' => 'Cart updated'];
}

/**
 * Remove item from cart
 */
function removeFromCart($cartId, $userId) {
    global $conn;
    
    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cartId, $userId);
    $stmt->execute();
    
    return ['success' => true, 'message' => 'Item removed'];
}

/**
 * Clear user cart
 */
function clearCart($userId) {
    global $conn;
    
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    
    return ['success' => true, 'message' => 'Cart cleared'];
}

// ============================================
// CATEGORY FUNCTIONS
// ============================================

/**
 * Get all categories
 */
function getCategories() {
    global $conn;
    
    $result = $conn->query("SELECT * FROM categories ORDER BY category_name");
    
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    
    return $categories;
}
?>
