<?php
// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

// Redirect to login if user is not logged in and is not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if order ID is passed via GET
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    // Fetch the order details securely using prepared statements
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    // Check if the order exists
    if (!$order) {
        die("Order not found.");
    }

} else {
    die("Order ID not provided.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Order Details</h2>

        <div class="row">
            <!-- Left Column: Order Summary -->
            <div class="col-md-6">
                <h4>Order Information</h4>
                <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
                <p><strong>Status:</strong> 
                    <?php
                    switch ($order['status']) {
                        case 'Delivered':
                            echo '<span class="badge bg-success">Delivered</span>';
                            break;
                        case 'Shipped':
                            echo '<span class="badge bg-primary">Shipped</span>';
                            break;
                        case 'Processing':
                            echo '<span class="badge bg-warning">Processing</span>';
                            break;
                        case 'Pending':
                            echo '<span class="badge bg-secondary">Pending</span>';
                            break;
                        case 'Cancelled':
                            echo '<span class="badge bg-danger">Cancelled</span>';
                            break;
                        default:
                            echo '<span class="badge bg-info">' . htmlspecialchars($order['status']) . '</span>';
                            break;
                    }
                    ?>
                </p>
                <p><strong>User ID:</strong> <?php echo htmlspecialchars($order['user_id']); ?></p>
                <p><strong>Order Date:</strong> <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                <p><strong>City:</strong> <?php echo htmlspecialchars($order['city']); ?></p>
                <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($order['postal_code']); ?></p>
                <p><strong>Total Price:</strong> ₹<?php echo number_format($order['total_price'], 2); ?></p>

                <!-- Status Update Form -->
                <form action="update_order_status.php" method="post" class="mt-3">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                    <div class="mb-3">
                        <label for="status" class="form-label">Change Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="Pending" <?php echo ($order['status'] === 'Pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="Processing" <?php echo ($order['status'] === 'Processing' ? 'selected' : ''); ?>>Processing</option>
                            <option value="Shipped" <?php echo ($order['status'] === 'Shipped' ? 'selected' : ''); ?>>Shipped</option>
                            <option value="Delivered" <?php echo ($order['status'] === 'Delivered' ? 'selected' : ''); ?>>Delivered</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </form>
            </div>

            <!-- Right Column: Ordered Items -->
            <div class="col-md-6">
                <h4>Ordered Items</h4>
                <?php
                // Decode the JSON data for products
                $products = json_decode($order['details'], true);

                // Check if the products data is valid
                if (is_array($products) && count($products) > 0):
                ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Price (per unit)</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                                    <td>₹<?php echo number_format($product['price'], 2); ?></td>
                                    <td>₹<?php echo number_format($product['quantity'] * $product['price'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No items found for this order.</p>
                <?php endif; ?>
            </div>
        </div>

        <a href="orders.php" class="btn btn-secondary mt-3">Back to Orders</a>
    </div>

    <footer class="footer text-center">
        © 2024 Balajee Sales. All rights reserved.
    </footer>
</body>
</html>
