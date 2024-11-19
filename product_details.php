<?php
include 'db.php'; // Include database connection

$product_id = $_GET['product_id'] ?? 0;

// Get product details
$sql = "SELECT * FROM products WHERE product_id = " . (int)$product_id;
$product_result = $conn->query($sql);

// Check if product exists
if (!$product_result || $product_result->num_rows == 0) {
    die("Product not found.");
}

$product = $product_result->fetch_assoc();

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $feedback = $_POST['feedback'] ?? '';

    if ($feedback) {
        $stmt = $conn->prepare("INSERT INTO product_feedback (product_id, feedback) VALUES (?, ?)");
        $stmt->bind_param("is", $product_id, $feedback);
        $stmt->execute();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> - Product Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <main class="container mt-4">
        <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
        <div class="row">
            <div class="col-md-6">
                <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="img-fluid">
            </div>
            <div class="col-md-6">
                <h3>$<?php echo htmlspecialchars($product['price']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                <a href="cart.php?action=add&product_id=<?php echo $product['product_id']; ?>" class="btn btn-success">Add to Cart</a>
            </div>
        </div>

    </main>

</body>
</html>
