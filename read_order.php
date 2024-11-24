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

// Cancel order functionality (if status is not 'Delivered' or 'Canceled')
if (isset($_POST['cancel_order']) && $order['status'] !== 'Delivered' && $order['status'] !== 'Cancelled') {
    $stmt = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    
    // Redirect to orders page after cancellation
    header("Location: customer_order.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
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
        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .cancel-btn {
            background-color: #dc3545;
            color: white;
            border: none;
        }
        .cancel-btn:hover {
            background-color: #c82333;
        }
        .irreversible-notice {
            margin-top: 20px;
            font-size: 14px;
            color: #dc3545;
            font-weight: bold;
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
                    if ($order['status'] === 'Delivered') {
                        echo '<span class="badge bg-success">Delivered</span>';
                    } elseif ($order['status'] === 'Pending') {
                        echo '<span class="badge bg-warning text-dark">Pending</span>';
                    } elseif ($order['status'] === 'Cancelled') {
                        echo '<span class="badge bg-danger">Canceled</span>';
                    } else {
                        echo '<span class="badge bg-secondary">' . htmlspecialchars($order['status']) . '</span>';
                    }
                    ?>
                </p>
                <p><strong>Order Date:</strong> <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></p>
                <p><strong>Total Price:</strong> ₹<?php echo number_format($order['total_price'], 2); ?></p>

                <!-- Cancel Order Button (only if order is not delivered or canceled) -->
                <?php if ($order['status'] !== 'Delivered' && $order['status'] !== 'Cancelled'): ?>
                    <form method="POST">
                        <button type="submit" name="cancel_order" class="btn btn-danger mt-3">Cancel Order</button>
                    </form>
                    <!-- Irreversible Cancel Notice -->
                    <p class="irreversible-notice">Please note: Canceling the order is irreversible.</p>
                <?php endif; ?>
            </div>

            <!-- Right Column: Ordered Products -->
            <div class="col-md-6">
                <h4>Ordered Products</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Decode the JSON data for products
                        $products = json_decode($order['details'], true);

                        // Check if the products data is valid
                        if (is_array($products) && count($products) > 0):
                            foreach ($products as $product):
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                                <td>₹<?php echo number_format($product['price'], 2); ?></td>
                            </tr>
                        <?php
                            endforeach;
                        else:
                        ?>
                            <tr>
                                <td colspan="3" class="text-center text-danger">No products found for this order.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <a href="customer_order.php" class="btn btn-secondary mt-3">Back to My Orders</a>
    </div>

    <footer class="footer text-center">
        © 2024 Balajee Sales. All rights reserved.
    </footer>
</body>
</html>
