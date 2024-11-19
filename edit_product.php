<?php
include 'db.php'; // Include database connection

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $sql = "SELECT * FROM products WHERE product_id = $product_id";
    $result = $conn->query($sql);
    $product = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $category = $_POST['category'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $image_path = $_POST['image_path'];
        $description = $_POST['description'];
        $brand = $_POST['brand'];

        $updateSql = "UPDATE products SET name = '$name', category = '$category', price = '$price', stock = '$stock', image_path = '$image_path', description = '$description', brand = '$brand' WHERE product_id = $product_id";

        if ($conn->query($updateSql)) {
            echo "Product updated successfully!";
        } else {
            echo "Error: " . $conn->error;
        }
    }
} else {
    echo "Product ID not found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2>Edit Product</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo $product['product_name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <input type="text" class="form-control" name="category" value="<?php echo $product['category']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" class="form-control" name="price" value="<?php echo $product['price']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" class="form-control" name="stock" value="<?php echo $product['stock']; ?>" required>
            </div>
            <div class="mb-3">
        <label for="image" class="form-label">Image</label>
        <input type="file" class="form-control" name="image" id="image" accept="image/*" required>
    </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="4"><?php echo $product['description']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="brand" class="form-label">Brand</label>
                <input type="text" class="form-control" name="brand" value="<?php echo $product['brand']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>
    </div>
</body>
</html>
