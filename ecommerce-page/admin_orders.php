<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) {
  header("Location: admin_login.php");
  exit();
}

require_once 'db_config.php'; // Should contain database connection and functions

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search and filter
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

// Base query
$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM orders WHERE 1=1";
$count_sql = "SELECT COUNT(*) FROM orders WHERE 1=1";

// Add search condition
if (!empty($search)) {
  $sql .= " AND (customer_name LIKE '%$search%' OR address LIKE '%$search%' OR id = '$search')";
  $count_sql .= " AND (customer_name LIKE '%$search%' OR address LIKE '%$search%' OR id = '$search')";
}

// Add status filter
if (!empty($status_filter)) {
  $sql .= " AND status = '$status_filter'";
  $count_sql .= " AND status = '$status_filter'";
}

// Complete query with ordering and pagination
$sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Get total count for pagination
$total_result = $conn->query($count_sql);
$total_row = $total_result->fetch_row();
$total = $total_row[0];
$total_pages = ceil($total / $limit);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Orders Dashboard</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <header>
    <h1><i class="fas fa-boxes"></i> Admin Orders Dashboard</h1>
    <div>
      <a href="admin_logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </header>

  <div class="card">
    <form method="GET" class="form-inline">
      <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <div style="flex: 1;">
          <input type="text" name="search" class="form-control" placeholder="Search orders..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div>
          <select name="status" class="form-control">
            <option value="">All Statuses</option>
            <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="processing" <?= $status_filter === 'processing' ? 'selected' : '' ?>>Processing</option>
            <option value="shipped" <?= $status_filter === 'shipped' ? 'selected' : '' ?>>Shipped</option>
            <option value="delivered" <?= $status_filter === 'delivered' ? 'selected' : '' ?>>Delivered</option>
            <option value="cancelled" <?= $status_filter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
          </select>
        </div>
        <div>
          <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
          <a href="admin_orders.php" class="btn"><i class="fas fa-sync-alt"></i> Reset</a>
        </div>
      </div>
    </form>
  </div>

  <div class="card">
    <div style="overflow-x: auto;">
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Address</th>
            <th>Items</th>
            <th>Total</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): 
              $items = json_decode($row['order_items'], true);
              $total = 0;
              if ($items) {
                foreach ($items as $item) {
                  $total += $item['price'] * $item['qty'];
                }
              }
            ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td>
                  <?php if ($items): ?>
                    <ul style="margin: 0; padding-left: 1.2rem;">
                      <?php foreach ($items as $item): ?>
                        <li><?= htmlspecialchars($item['name']) ?> (<?= $item['qty'] ?> × $<?= number_format($item['price'], 2) ?>)</li>
                      <?php endforeach; ?>
                    </ul>
                  <?php else: ?>
                    <span class="text-muted">No items</span>
                  <?php endif; ?>
                </td>
                <td>$<?= number_format($total, 2) ?></td>
                <td><?= date('M j, Y g:i A', strtotime($row['created_at'])) ?></td>
                <td>
                  <span class="status-badge status-<?= $row['status'] ?>">
                    <?= ucfirst($row['status']) ?>
                  </span>
                </td>
                <td>
                  <div style="display: flex; gap: 0.5rem;">
                    <a href="view_order.php?id=<?= $row['id'] ?>" class="btn btn-primary" style="padding: 0.3rem 0.6rem;"><i class="fas fa-eye"></i></a>
                    <a href="edit_order.php?id=<?= $row['id'] ?>" class="btn" style="padding: 0.3rem 0.6rem;"><i class="fas fa-edit"></i></a>
                    <form action="delete_order.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');" style="display: inline;">
                      <input type="hidden" name="id" value="<?= $row['id'] ?>">
                      <button type="submit" class="btn btn-danger" style="padding: 0.3rem 0.6rem;"><i class="fas fa-trash"></i></button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" style="text-align: center;">No orders found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($total_pages > 1): ?>
      <div class="pagination">
        <?php if ($page > 1): ?>
          <a href="?page=1&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>">&laquo;</a>
          <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>">&lsaquo;</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>" <?= $i == $page ? 'class="active"' : '' ?>><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
          <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>">&rsaquo;</a>
          <a href="?page=<?= $total_pages ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>">&raquo;</a>
        <?php endif; ?>
      </div>
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
