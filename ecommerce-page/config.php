<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ecommerce_db');

// Admin credentials (in production, store these more securely)
define('ADMIN_USER', 'admin');
define('ADMIN_PASSWORD_HASH', password_hash('admin123', PASSWORD_DEFAULT)); // Change this!

// Create tables if they don't exist
function initializeDatabase($conn) {
    // Products table
    $conn->query("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255),
        stock INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Orders table
    $conn->query("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(100) NOT NULL,
        email VARCHAR(100),
        phone VARCHAR(20),
        address TEXT NOT NULL,
        city VARCHAR(50),
        zip_code VARCHAR(20),
        country VARCHAR(50),
        payment_method VARCHAR(20),
        order_items TEXT NOT NULL,
        status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Admin logs table
    $conn->query("CREATE TABLE IF NOT EXISTS admin_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        action TEXT NOT NULL,
        admin_id VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Insert sample products if empty
    $result = $conn->query("SELECT COUNT(*) FROM products");
    if ($result->fetch_row()[0] == 0) {
        $conn->query("INSERT INTO products (name, description, price, image, stock) VALUES 
            ('T-Shirt', 'Comfortable cotton t-shirt', 20.00, 'tshirt.jpg', 100),
            ('Cap', 'Stylish baseball cap', 10.00, 'cap.jpg', 50)");
    }
}

// Initialize database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
$conn->select_db(DB_NAME);

// Initialize tables
initializeDatabase($conn);
$conn->close();
?>
