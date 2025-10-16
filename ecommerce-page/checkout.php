<?php
require_once 'config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $cartData = $_POST['cart_data'] ?? '';
    
    $cart = json_decode($cartData, true);
    
    if (empty($name) || empty($address) || empty($cartData) || !$cart) {
        $message = "Please fill all required fields and add items to cart.";
    } else {
        // Calculate total
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['qty'];
        }
        
        $stmt = $conn->prepare("INSERT INTO orders (customer_name, email, phone, address, order_items, total_amount) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssd", $name, $email, $phone, $address, $cartData, $total);
        
        if ($stmt->execute()) {
            $order_id = $conn->insert_id;
            $message = "success";
            echo "<script>localStorage.removeItem('cart');</script>";
        } else {
            $message = "Error placing order. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .checkout-container {
      max-width: 800px;
      margin: 30px auto;
    }
    .order-summary {
      background: #f8f9fa;
      border-radius: 10px;
      padding: 20px;
    }
  </style>
</head>
<body class="bg-light">
  <div class="container checkout-container">
    <div class="row">
      <div class="col-md-7 mb-4">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Customer Information</h4>
          </div>
          <div class="card-body">
            <?php if ($message === "success"): ?>
              <div class="alert alert-success">
                <h4 class="alert-heading">Order Placed Successfully!</h4>
                <p>Thank you for your purchase. Your order ID is #<?= $order_id ?>.</p>
                <hr>
                <p class="mb-0">We'll process your order shortly.</p>
              </div>
            <?php elseif (!empty($message)): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if ($message !== "success"): ?>
              <form method="POST" id="checkoutForm">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Full Name*</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email">
                  </div>
                </div>
                <div class="mb-3">
                  <label for="phone" class="form-label">Phone Number</label>
                  <input type="tel" class="form-control" id="phone" name="phone">
                </div>
                <div class="mb-3">
                  <label for="address" class="form-label">Shipping Address*</label>
                  <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                </div>
                <input type="hidden" name="cart_data" id="cartData">
                <button type="submit" class="btn btn-primary btn-lg w-100">Place Order</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
      <div class="col-md-5">
        <div class="card order-summary">
          <div class="card-header">
            <h4 class="mb-0">Order Summary</h4>
          </div>
          <div class="card-body">
            <ul class="list-group mb-3" id="orderSummary">
              <!-- Items will be populated by JavaScript -->
            </ul>
            <div class="d-flex justify-content-between fw-bold fs-5">
              <span>Total:</span>
              <span id="orderTotal">$0.00</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const cart = JSON.parse(localStorage.getItem('cart')) || [];
      const orderSummary = document.getElementById('orderSummary');
      const orderTotal = document.getElementById('orderTotal');
      const cartData = document.getElementById('cartData');
      
      let total = 0;
      
      if (cart.length === 0 && <?= $message !== "success" ? 'true' : 'false' ?>) {
        orderSummary.innerHTML = '<li class="list-group-item text-center">Your cart is empty</li>';
        window.location.href = 'index.php';
        return;
      }
      
      cart.forEach(item => {
        const itemTotal = item.price * item.qty;
        total += itemTotal;
        
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between';
        li.innerHTML = `
          <div>
            <h6 class="my-0">${item.name}</h6>
            <small class="text-muted">${item.qty} × $${item.price.toFixed(2)}</small>
          </div>
          <span class="text-muted">$${itemTotal.toFixed(2)}</span>
        `;
        orderSummary.appendChild(li);
      });
      
      orderTotal.textContent = `$${total.toFixed(2)}`;
      cartData.value = JSON.stringify(cart);
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
