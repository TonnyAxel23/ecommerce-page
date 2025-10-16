<?php
session_start();

// Use environment variables or config file for credentials
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Basic brute force protection
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }

    if ($_SESSION['login_attempts'] >= 3) {
        $error = "Too many login attempts. Please try again later.";
    } elseif ($username === ADMIN_USER && password_verify($password, ADMIN_PASSWORD_HASH)) {
        $_SESSION["admin_logged_in"] = true;
        $_SESSION["login_attempts"] = 0;
        header("Location: admin_orders.php");
        exit();
    } else {
        $_SESSION['login_attempts']++;
        $error = "Invalid credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .login-container {
      max-width: 400px;
      margin: 100px auto;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .form-floating label {
      padding: 1rem 1.75rem;
    }
  </style>
</head>
<body class="bg-light">
  <div class="container">
    <div class="login-container bg-white">
      <h2 class="text-center mb-4">🔐 Admin Login</h2>
      <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form method="POST">
        <div class="form-floating mb-3">
          <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
          <label for="username">Username</label>
        </div>
        <div class="form-floating mb-3">
          <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
          <label for="password">Password</label>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
      </form>
    </div>
  </div>
</body>

</html>
