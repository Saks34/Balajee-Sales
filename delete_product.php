<?php
include 'db.php'; // Include database connection

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $sql = "DELETE FROM products WHERE product_id = $product_id";
    
    if ($conn->query($sql)) {
        echo "Product deleted successfully!";
        header("Location: all_products.php");
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Product ID not found.";
}
?>
