<?php
session_start();

// Include PHPMailer and database connection
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust path based on your project structure
require 'db.php'; // Your database connection file

// Verify session data
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details from session
$customerName = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

// Fetch user email from the database
$userQuery = "SELECT email FROM users WHERE user_id = '$user_id'";
$userResult = mysqli_query($conn, $userQuery);

if (!$userResult || mysqli_num_rows($userResult) == 0) {
    echo "<script>
        alert('Unable to fetch user details.');
        window.location.href = 'index.php';
    </script>";
    exit();
}

$userData = mysqli_fetch_assoc($userResult);
$email = $userData['email'];

// Fetch cart data
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    echo "<script>
        alert('Your cart is empty.');
        window.location.href = 'index.php';
    </script>";
    exit();
}

// Calculate total price
$total_price = 0;
foreach ($cart as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $postalCode = mysqli_real_escape_string($conn, $_POST['postal_code']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);

    // Generate a unique order ID
    $orderID = uniqid('ORD_');

    // Prepare order details
    $orderDetails = [];
    foreach ($cart as $item) {
        $orderDetails[] = [
            'name' => $item['name'],
            'quantity' => $item['quantity'],
            'price' => $item['price']
        ];
    }
    $orderDetailsJson = json_encode($orderDetails);

    // Insert the order into the database
    $query = "INSERT INTO orders (order_id, customerEmail, customerName, phone, address, postal_code, city, details, total_price, user_id) 
    VALUES ('$orderID', '$email', '$customerName', '$phone', '$address', '$postalCode', '$city', '$orderDetailsJson', '$total_price', $user_id)";

    if (mysqli_query($conn, $query)) {
        // Update stock in the database and track ordered items
        foreach ($cart as $item) {
            $productName = $item['name'];
    $quantity = $item['quantity'];

    // Query to fetch the product ID from the database based on product name
    $productQuery = "SELECT product_id FROM products WHERE product_name = '$productName' LIMIT 1";
    $productResult = mysqli_query($conn, $productQuery);

    if ($productResult && mysqli_num_rows($productResult) > 0) {
        $productData = mysqli_fetch_assoc($productResult);
        $productID = $productData['product_id']; // Get the product ID from the database

        // Update stock in the database
        $stockUpdateQuery = "UPDATE products SET stock = stock - $quantity WHERE product_id = '$productID'";
        if (!mysqli_query($conn, $stockUpdateQuery)) {
            echo "Error updating stock: " . mysqli_error($conn);
        }
    } else {
        echo "Product not found: $productName";
    }
            $quantity = $item['quantity'];
            $productName = $item['name'];
        
            // Update stock
            $stockUpdateQuery = "UPDATE products SET stock = stock - $quantity WHERE product_id = '$productID'";
            mysqli_query($conn, $stockUpdateQuery);
        
            // Update or insert into ordered_items
            $orderedItemQuery = "SELECT * FROM ordered_items WHERE product_id = '$productID'";
            $orderedItemResult = mysqli_query($conn, $orderedItemQuery);
        
            if (mysqli_num_rows($orderedItemResult) > 0) {
                // Update existing record
                $updateOrderedItemQuery = "UPDATE ordered_items SET total_orders = total_orders + $quantity WHERE product_id = '$productID'";
                mysqli_query($conn, $updateOrderedItemQuery);
            } else {
                // Insert new record
                $insertOrderedItemQuery = "INSERT INTO ordered_items (product_id, product_name, total_orders) 
                                           VALUES ('$productID', '$productName', $quantity)";
                mysqli_query($conn, $insertOrderedItemQuery);
            }
        }
        

        // Order placed successfully
        $_SESSION['cart'] = []; // Clear the cart

        // Send order confirmation email
        try {
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'balajeesales09@gmail.com'; // Your Gmail address
            $mail->Password = 'erzx ijfz emmw izzy'; // Your Gmail App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('balajeesales09@gmail.com', 'Balajee Sales');
            $mail->addAddress($email, $customerName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Order Confirmation - Order #$orderID";
            $mail->Body = "
                <h1>Order Confirmation</h1>
                <p>Hi $customerName,</p>
                <p>Thank you for shopping with us! Your order ID is <b>$orderID</b>.</p>
                <p>Order Details:</p>
                <ul>";
            foreach ($orderDetails as $detail) {
                $mail->Body .= "<li>{$detail['name']} - {$detail['quantity']} x ₹" . number_format($detail['price'], 2) . "</li>";
            }
            $mail->Body .= "</ul>
                <p>Delivery Address:</p>
                <p>$address, $city, $postalCode</p>
                <p>Total Price: ₹" . number_format($total_price, 2) . "</p>
                <br>
                <p>Best Regards,</p>
                <p>Clothing Shop Team</p>";

            // Send the email
            $mail->send();
            echo "<script>
                alert('Order placed successfully! Confirmation email sent.');
                window.location.href = 'customer_order.php';
            </script>";
            exit();
        } catch (Exception $e) {
            echo "<script>
                alert('Order placed, but confirmation email could not be sent. Mailer Error: {$mail->ErrorInfo}');
                window.location.href = 'index.php';
            </script>";
            exit();
        }
    } else {
        echo "<script>
            alert('Error placing the order: " . mysqli_error($conn) . "');
            window.location.href = 'checkout.php';
        </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Balajee Sales</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
        }

        header {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 1rem 0;
        }

        main {
            max-width: 600px;
            margin: 2rem auto;
            background: #fff;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1, h2 {
            text-align: center;
        }

        p {
            font-size: 1rem;
            line-height: 1.5;
        }

        input, button {
            width: 100%;
            padding: 0.75rem;
            margin-top: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #28a745;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        button:hover {
            background-color: #218838;
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
    <header>
        <h1>Checkout Page</h1>
    </header>
    <main>
        <h2>Confirm Your Order</h2>
        <p><strong>Order Summary:</strong></p>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price (INR)</th>
                    <th>Total (INR)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalPrice = 0;
                foreach ($cart as $item) {
                    $totalItemPrice = $item['quantity'] * $item['price'];
                    $totalPrice += $totalItemPrice;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>₹<?php echo number_format($item['price'], 2); ?></td>
                    <td>₹<?php echo number_format($totalItemPrice, 2); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <p class="total-price">Total Price: ₹<?php echo number_format($totalPrice, 2); ?></p>

        <form method="POST" action="">
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="text" name="address" placeholder="Delivery Address" required>
            <input type="text" name="postal_code" placeholder="Postal Code" required>
            <input type="text" name="city" placeholder="City" required>
            <button type="submit">Confirm Order</button>
        </form>
    </main>
   
    <footer class="footer text-center">
        © 2024 Balajee Sales. All rights reserved.
    </footer>
</body>
</html>
