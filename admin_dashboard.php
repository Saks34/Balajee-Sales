<?php
include 'db.php'; // Include database connection

// Fetch product count
$productCountQuery = "SELECT COUNT(*) AS total FROM products";
$productResult = $conn->query($productCountQuery);
$productCount = $productResult->fetch_assoc()['total'];

// Fetch user count
$userCountQuery = "SELECT COUNT(*) AS total FROM users WHERE role='user'";
$userResult = $conn->query($userCountQuery);
$userCount = $userResult->fetch_assoc()['total'];

// Fetch pending orders count
$orderCountQuery = "SELECT COUNT(*) AS total FROM orders WHERE status='pending'";
$orderResult = $conn->query($orderCountQuery);
$orderCount = $orderResult->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'navbar.php'; // Include navbar for admin ?>
    <div class="container mt-5">
        <h2>Admin Dashboard</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Products</h5>
                        <p class="card-text">Total Products: <?php echo $productCount; ?></p>
                        <a href="all_products.php" class="btn btn-primary">View Products</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Users</h5>
                        <p class="card-text">Total Users: <?php echo $userCount; ?></p>
                        <a href="user_list.php" class="btn btn-primary">View Users</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Orders</h5>
                        <p class="card-text">Pending Orders: <?php echo $orderCount; ?></p>
                        <a href="orders.php" class="btn btn-primary">View Orders</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
