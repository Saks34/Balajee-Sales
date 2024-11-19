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

// Ensure `order_id` is present and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid order ID.";
    exit();
}

$order_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Fetch order details securely using prepared statements
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order_result = $stmt->get_result();

// Check if the order exists
if ($order_result->num_rows === 0) {
    echo "Order not found or access denied.";
    exit();
}

$order = $order_result->fetch_assoc();

// Fetch order items
$item_stmt = $conn->prepare("
    SELECT oi.*, p.product_name, p.product_price 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?
");
$item_stmt->bind_param("i", $order_id);
$item_stmt->execute();
$items_result = $item_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Include the navigation bar -->
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Order Details</h2>

        <!-- Order Summary -->
        <div class="mb-4">
            <h4>Order Summary</h4>
            <ul class="list-group">
                <li class="list-group-item"><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?></li>
                <li class="list-group-item"><strong>Order Date:</strong> <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></li>
                <li class="list-group-item">
                    <strong>Status:</strong> 
                    <?php if ($order['status'] === 'Delivered'): ?>
                        <span class="badge bg-success">Delivered</span>
                    <?php elseif ($order['status'] === 'Pending'): ?>
                        <span class="badge bg-warning">Pending</span>
                    <?php else: ?>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($order['status']); ?></span>
                    <?php endif; ?>
                </li>
                <li class="list-group-item"><strong>Total Price:</strong> ₹<?php echo number_format($order['total_price'], 2); ?></li>
            </ul>
        </div>

        <!-- Order Items -->
        <div>
            <h4>Ordered Items</h4>
            <?php if ($items_result->num_rows > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $items_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td>₹<?php echo number_format($item['product_price'], 2); ?></td>
                                <td>₹<?php echo number_format($item['quantity'] * $item['product_price'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="alert alert-warning">No items found for this order.</p>
            <?php endif; ?>
        </div>

        <a href="my_orders.php" class="btn btn-secondary mt-3">Back to My Orders</a>
    </div>

    <footer class="text-center bg-dark text-light py-3 mt-5">
        <p>&copy; 2024 Balajee Sales. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
