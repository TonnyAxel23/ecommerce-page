<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop - Product Catalog</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="index.php">ShopEase</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link active" href="index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Products</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">About</a>
          </li>
        </ul>
        <div class="d-flex">
          <a href="checkout.php" class="btn btn-outline-light position-relative">
            <i class="bi bi-cart"></i>
            <span class="cart-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">0</span>
          </a>
          <a href="admin_login.php" class="btn btn-outline-light ms-2">
            <i class="bi bi-person"></i> Admin
          </a>
        </div>
      </div>
    </div>
  </nav>

  <div class="container my-5">
    <h1 class="text-center mb-5">Product Catalog</h1>
    
    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
      <div class="col">
        <div class="card h-100 product-card">
          <img src="assets/images/tshirt.jpg" class="card-img-top" alt="T-Shirt">
          <div class="card-body">
            <h5 class="card-title">T-Shirt</h5>
            <p class="card-text">Comfortable cotton t-shirt for everyday wear.</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="h5 text-primary">$20.00</span>
              <button class="btn btn-primary add-to-cart" 
                data-id="1" 
                data-name="T-Shirt" 
                data-price="20.00"
                data-image="assets/images/tshirt.jpg">
                <i class="bi bi-cart-plus"></i> Add to Cart
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col">
        <div class="card h-100 product-card">
          <img src="assets/images/cap.jpg" class="card-img-top" alt="Cap">
          <div class="card-body">
            <h5 class="card-title">Cap</h5>
            <p class="card-text">Stylish cap for sun protection.</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="h5 text-primary">$10.00</span>
              <button class="btn btn-primary add-to-cart" 
                data-id="2" 
                data-name="Cap" 
                data-price="10.00"
                data-image="assets/images/cap.jpg">
                <i class="bi bi-cart-plus"></i> Add to Cart
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Add more products as needed -->
    </div>
  </div>

  <!-- Cart Sidebar -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">Your Cart</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <ul class="list-group mb-3" id="cart">
        <!-- Cart items will be populated by JavaScript -->
      </ul>
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>Total:</h5>
        <h5 id="cartTotal">$0.00</h5>
      </div>
      <a href="checkout.php" class="btn btn-primary w-100">Proceed to Checkout</a>
    </div>
  </div>

  <script src="cart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
