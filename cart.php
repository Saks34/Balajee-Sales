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
    <title>Your Cart</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f9f9f9;
}

header {
    background-color: #333;
    color: white;
    padding: 1rem;
    text-align: center;
}

header a {
    color: white;
    text-decoration: none;
    background: #28a745;
    padding: 0.5rem 1rem;
    border-radius: 5px;
}

header a:hover {
    background: #218838;
}

main {
    padding: 2rem;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 2rem;
}

table th, table td {
    border: 1px solid #ddd;
    padding: 1rem;
    text-align: center;
}

table th {
    background: #333;
    color: white;
}

table tr:nth-child(even) {
    background: #f2f2f2;
}

.total {
    text-align: right;
    margin-top: 1rem;
}

.btn {
    display: inline-block;
    padding: 0.5rem 1rem;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}

.btn:hover {
    background-color: #0056b3;
}

    </style>
</head>
<body>
    <header>
        <h1>Your Cart</h1>
        <a href="index.php" class="btn">Continue Shopping</a>
    </header>

    <main>
        <?php if (!empty($cart)): ?>
            <table>
                <thead>
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
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($subtotal, 2); ?></td>
                            <td>
                                <a href="cart.php?remove=<?php echo $id; ?>" class="btn">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="total">
                <p><strong>Total Price:</strong> $<?php echo number_format($total_price, 2); ?></p>
                <a href="checkout.php" class="btn">Proceed to Checkout</a>
            </div>
        <?php else: ?>
            <p>Your cart is empty.</p>
            <a href="index.php" class="btn">Go Shopping</a>
        <?php endif; ?>
    </main>

</body>
</html>
