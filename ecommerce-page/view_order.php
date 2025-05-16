<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) {
  header("Location: admin_login.php");
  exit();
}

require_once 'db_config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$order = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM orders WHERE id=$id");
    $order = $result->fetch_assoc();
    
    // Calculate total
    $items = json_decode($order['order_items'], true);
    $total = 0;
    if ($items) {
        foreach ($items as $item) {
            $total += $item['price'] * $item['qty'];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>View Order</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <header>
    <h1><i class="fas fa-file-invoice"></i> Order Details</h1>
    <div>
      <a href="admin_orders.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Orders</a>
    </div>
  </header>

  <div class="card">
    <?php if ($order): ?>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
        <div>
          <h3><i class="fas fa-user"></i> Customer Information</h3>
          <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($order['email'] ?? 'N/A') ?></p>
          <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone'] ?? 'N/A') ?></p>
        </div>
        
        <div>
          <h3><i class="fas fa-map-marker-alt"></i> Shipping Information</h3>
          <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
          <p><strong>City:</strong> <?= htmlspecialchars($order['city'] ?? 'N/A') ?></p>
          <p><strong>Zip Code:</strong> <?= htmlspecialchars($order['zip_code'] ?? 'N/A') ?></p>
          <p><strong>Country:</strong> <?= htmlspecialchars($order['country'] ?? 'N/A') ?></p>
        </div>
      </div>
      
      <div style="margin-bottom: 2rem;">
        <h3><i class="fas fa-info-circle"></i> Order Information</h3>
        <p><strong>Order ID:</strong> #<?= $order['id'] ?></p>
        <p><strong>Date:</strong> <?= date('F j, Y \a\t g:i A', strtotime($order['created_at'])) ?></p>
        <p><strong>Status:</strong> 
          <span class="status-badge status-<?= $order['status'] ?>">
            <?= ucfirst($order['status']) ?>
          </span>
        </p>
        <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method'] ?? 'N/A') ?></p>
      </div>
      
      <h3><i class="fas fa-shopping-cart"></i> Order Items</h3>
      <table class="table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($items): ?>
            <?php foreach ($items as $item): ?>
              <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td>$<?= number_format($item['price'], 2) ?></td>
                <td><?= $item['qty'] ?></td>
                <td>$<?= number_format($item['price'] * $item['qty'], 2) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" style="text-align: center;">No items found</td>
            </tr>
          <?php endif; ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
            <td><strong>$<?= number_format($total, 2) ?></strong></td>
          </tr>
        </tfoot>
      </table>
      
      <div style="margin-top: 2rem; display: flex; justify-content: space-between;">
        <form action="update_order_status.php" method="POST">
          <input type="hidden" name="id" value="<?= $order['id'] ?>">
          <div style="display: flex; gap: 1rem; align-items: center;">
            <select name="status" class="form-control" style="width: auto;">
              <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
              <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
              <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
              <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
              <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Status</button>
          </div>
        </form>
        
        <form action="delete_order.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');">
          <input type="hidden" name="id" value="<?= $order['id'] ?>">
          <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Delete Order</button>
        </form>
      </div>
    <?php else: ?>
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> Order not found.
      </div>
      <a href="admin_orders.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Orders</a>
    <?php endif; ?>
  </div>
  
  <style>
    .status-badge {
      padding: 0.25rem 0.5rem;
      border-radius: 1rem;
      font-size: 0.8rem;
      font-weight: 500;
    }
    
    .status-pending {
      background-color: #fff3cd;
      color: #856404;
    }
    
    .status-processing {
      background-color: #cce5ff;
      color: #004085;
    }
    
    .status-shipped {
      background-color: #d4edda;
      color: #155724;
    }
    
    .status-delivered {
      background-color: #d1ecf1;
      color: #0c5460;
    }
    
    .status-cancelled {
      background-color: #f8d7da;
      color: #721c24;
    }
  </style>
</body>
</html>