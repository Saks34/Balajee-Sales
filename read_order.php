<?php
include 'db.php';

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $sql = "SELECT * FROM orders WHERE order_id = $order_id";
    $result = $conn->query($sql);
    $order = $result->fetch_assoc();
} else {
    echo "Order ID not found.";
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
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2>Order Details</h2>
        <p>Order ID: <?php echo $order['order_id']; ?></p>
        <p>Status: <?php echo $order['status']; ?></p>
        <p>User ID: <?php echo $order['user_id']; ?></p>
        <p>Order Date: <?php echo $order['order_date']; ?></p>
        <p>Address: <?php echo $order['address']; ?></p>
        <p>City: <?php echo $order['city']; ?></p>
        <p>Postal Code: <?php echo $order['postal_code']; ?></p>
        <p>Total Price: ₹<?php echo $order['total_price']; ?></p>
        <a href="orders.php" class="btn btn-primary">Back to Orders</a>
    </div>
</body>
</html>
