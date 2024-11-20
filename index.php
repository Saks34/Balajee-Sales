<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balajee Sales</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
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
    
    <main>
        <h2>Featured Products</h2>
        <div class="product-grid">
            <?php
            include 'db.php';
            $query = "SELECT image_path AS image, product_name AS name, price, product_id FROM products";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '
                        <div class="product">
                            <img src="' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">
                            <h3>' . htmlspecialchars($row['name']) . '</h3>
                            <span>₹' . htmlspecialchars($row['price']) . '</span>
                            <a href="cart.php?add=' . htmlspecialchars($row['product_id']) . '" class="btn">Add to Cart</a>
                        </div>
                    ';
                }
            } else {
                echo "<p>No products found.</p>";
            }
            mysqli_close($conn);
            ?>
        </div>
    </main>
    <footer class="footer text-center">
        © 2024 Balajee Sales. All rights reserved.
    </footer>
</body>
</html>
