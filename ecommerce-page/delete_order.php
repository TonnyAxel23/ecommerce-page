<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) {
  header("Location: admin_login.php");
  exit();
}

require_once 'db_config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Check if order exists first
    $check = $conn->query("SELECT id FROM orders WHERE id = $id");
    if ($check->num_rows > 0) {
        // Delete order
        $conn->query("DELETE FROM orders WHERE id = $id");
        
        // Log the deletion
        $admin_id = $_SESSION['admin_id'] ?? 'unknown';
        $log_message = "Order #$id deleted by admin $admin_id";
        $conn->query("INSERT INTO admin_logs (action, admin_id) VALUES ('$log_message', '$admin_id')");
        
        $_SESSION['message'] = "Order #$id has been deleted successfully.";
    } else {
        $_SESSION['error'] = "Order not found.";
    }
    
    header("Location: admin_orders.php");
    exit();
} else {
    // If someone tries to access directly via GET
    header("Location: admin_orders.php");
    exit();
}
?>