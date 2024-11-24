<?php
// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch orders securely using prepared statements
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #333;
            color: #fff;
            padding: 10px 0;
        }
    </style>
</head>
<body>
    <!-- Include the navigation bar -->
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">My Orders</h2>

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Total Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                            <td><?php echo date('d M Y, h:i A', strtotime($row['order_date'])); ?></td>
                            <td>
    <?php if ($row['status'] === 'Delivered'): ?>
        <span class="badge bg-success">Delivered</span>
    <?php elseif ($row['status'] === 'Pending'): ?>
        <span class="badge bg-warning text-dark">Pending</span>
    <?php elseif ($row['status'] === 'Cancelled'): ?>
        <span class="badge bg-danger">Cancelled</span>
    <?php elseif ($row['status'] === 'Shipped'): ?>
        <span class="badge bg-info text-dark">Shipped</span>
    <?php else: ?>
        <span class="badge bg-secondary"><?php echo htmlspecialchars($row['status']); ?></span>
    <?php endif; ?>
</td>

                            <td>₹<?php echo number_format($row['total_price'], 2); ?></td>
                            <td>
                                <a href="read_order.php?id=<?php echo urlencode($row['order_id']); ?>" class="btn btn-primary btn-sm">View Details</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="alert alert-warning">You have no orders yet.</p>
        <?php endif; ?>

        <a href="products.php" class="btn btn-secondary mt-3">Continue Shopping</a>
    </div>


    <footer class="footer text-center">
        © 2024 Balajee Sales. All rights reserved.
    </footer>
</body>
</html>
