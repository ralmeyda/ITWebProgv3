<?php
require_once '../config.php';

echo "<h2>Database Connection Test</h2>";

if ($conn->ping()) {
    echo "<p style='color: green;'>✅ Connected to database: " . DB_NAME . "</p>";
    
    // Test categories
    $result = $conn->query("SELECT * FROM categories");
    echo "<p>Categories found: " . $result->num_rows . "</p>";
    
    while($row = $result->fetch_assoc()) {
        echo "<li>" . $row['category_name'] . "</li>";
    }
    
    // Test admin user
    $result = $conn->query("SELECT username, user_type FROM users WHERE user_type = 'admin'");
    echo "<p>Admin users: " . $result->num_rows . "</p>";
    
    while($row = $result->fetch_assoc()) {
        echo "<li>Username: " . $row['username'] . " (Type: " . $row['user_type'] . ")</li>";
    }
} else {
    echo "<p style='color: red;'>❌ Connection failed</p>";
}
?>
