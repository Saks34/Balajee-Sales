<?php
session_start();
include 'db.php';

// Add product to cart
if (isset($_GET['add'])) {
    $product_id = $_GET['add'];

    // Fetch product details
    $query = "SELECT product_id, product_name, price FROM products WHERE product_id = '$product_id'";
    $result = mysqli_query($conn, $query);
    $product = mysqli_fetch_assoc($result);

    if ($product) {
        // Initialize cart if not already set
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Add product to cart or update quantity
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$product_id] = [
                'name' => $product['product_name'],
                'price' => $product['price'],
                'quantity' => 1
            ];
        }
    }
    header("Location: cart.php");
    exit;
}

// Increment product quantity
if (isset($_GET['increment'])) {
    $product_id = $_GET['increment'];
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += 1;
    }
    header("Location: cart.php");
    exit;
}

// Decrement product quantity
if (isset($_GET['decrement'])) {
    $product_id = $_GET['decrement'];
    if (isset($_SESSION['cart'][$product_id]) && $_SESSION['cart'][$product_id]['quantity'] > 1) {
        $_SESSION['cart'][$product_id]['quantity'] -= 1;
    }
    header("Location: cart.php");
    exit;
}

// Remove product from cart
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    header("Location: cart.php");
    exit;
}

// Display cart
$cart = $_SESSION['cart'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <title>Your Cart</title>
</head>
<body>
<?php include 'navbar.php'; ?>
    

    <div class="container mt-5">
        <h1 class="mb-4">Your Cart</h1>
        <?php if (!empty($cart)): ?>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_price = 0;
                    foreach ($cart as $id => $item):
                        $subtotal = $item['price'] * $item['quantity'];
                        $total_price += $subtotal;
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <a href="cart.php?decrement=<?php echo $id; ?>" class="btn btn-warning btn-sm" <?php echo $item['quantity'] <= 1 ? 'disabled' : ''; ?>>
                                    <i class="bi bi-dash"></i>
                                </a>
                                <?php echo $item['quantity']; ?>
                                <a href="cart.php?increment=<?php echo $id; ?>" class="btn btn-success btn-sm">
                                    <i class="bi bi-plus"></i>
                                </a>
                            </td>
                            <td>₹<?php echo number_format($subtotal, 2); ?></td>
                            <td>
                                <a href="cart.php?remove=<?php echo $id; ?>" class="btn btn-danger btn-sm">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="text-end">
                <p><strong>Total Price:</strong> ₹<?php echo number_format($total_price, 2); ?></p>
                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
            </div>
        <?php else: ?>
            <p>Your cart is empty.</p>
            <a href="index.php" class="btn btn-primary">Go Shopping</a>
        <?php endif; ?>
    </div>
    <footer class="footer text-center">
        © 2024 Balajee Sales. All rights reserved.
    </footer>
</body>
</html>
