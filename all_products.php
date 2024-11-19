<?php
include 'db.php'; // Include database connection

$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2>All Products</h2>
        
        <!-- Add Product Button -->
        <div class="d-flex justify-content-end mb-3">
            <a href="add_product.php" class="btn btn-primary">Add Product</a>
        </div>

        <!-- Product Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($product = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $product['product_name']; ?></td>
                        <td><?php echo $product['category']; ?></td>
                        <td>$<?php echo $product['price']; ?></td>
                        <td><?php echo $product['stock']; ?></td>
                        <td>
                            <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="text-decoration-none">Edit</a> | 
                            <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" class="text-danger text-decoration-none" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
