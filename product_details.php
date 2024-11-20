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
    <style>
        body {
            background-color: #f8f9fa;
        }
        .product-image {
            max-height: 400px;
            object-fit: contain;
            border: 1px solid #ddd;
            border-radius: 10px;
        }
        .btn-custom {
            padding: 10px 20px;
            font-size: 16px;
        }
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
        <div class="row align-items-center">
            <!-- Product Image -->
            <div class="col-md-6 text-center">
                <img src="<?php echo htmlspecialchars($product['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                     class="img-fluid product-image">
            </div>
            <!-- Product Details -->
            <div class="col-md-6">
                <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
                <h4 class="text-success">₹<?php echo number_format($product['price'], 2); ?></h4>
                <p class="mt-3"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                <a href="cart.php?add=<?php echo $product['product_id']; ?>" 
                   class="btn btn-success btn-custom">
                    Add to Cart
                </a>
            </div>
        </div>
    </div>
    <footer class="footer text-center">
        © 2024 Balajee Sales. All rights reserved.
    </footer>
</body>
</html>
