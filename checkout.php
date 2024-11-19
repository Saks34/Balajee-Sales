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
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $postalCode = mysqli_real_escape_string($conn, $_POST['postal_code']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    

    // Generate a unique order ID
    $orderID = uniqid('ORD_');

    // Prepare order details for the database
    $orderDetails = json_encode($cart);

    // Insert the order into the database
    $query = "INSERT INTO orders (order_id, customerEmail, customerName, phone, address, postal_code, city, details, total_price, user_id) 
    VALUES ('$orderID', '$email', '$customerName', '$phone', '$address', '$postalCode', '$city', '$orderDetails', '$total_price', $user_id)";



    if (mysqli_query($conn, $query)) {
        // Order placed successfully
        echo "Order placed successfully!";
        $_SESSION['cart'] = []; // Clear the cart

        // Send order confirmation email
        try {
            // Validate the email address
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
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
                    <p>Order Details: $orderDetails</p>
                    <p>Delivery Address:</p>
                    <p>$address, $city, $postalCode</p>
                    <p>Total Price: $$total_price</p>
                    <p>We will process your order soon. Stay tuned!</p>
                    <br>
                    <p>Best Regards,</p>
                    <p>Clothing Shop Team</p>
                ";
        
                // Send the email
                $mail->send();
                echo "<script>
                    alert('Order placed successfully! Confirmation email sent.');
                    window.location.href = 'customer_order.php';
                </script>";
                exit();
            } else {
                echo "<script>
                    alert('Order placed, but confirmation email could not be sent: Invalid email address.');
                    window.location.href = 'index.php';
                </script>";
                exit();
            }
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

        footer {
            text-align: center;
            padding: 1rem 0;
            background-color: #333;
            color: #fff;
            margin-top: 2rem;
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
        <ul>
            <?php foreach ($cart as $item): ?>
                <li>
                    <?php echo htmlspecialchars($item['name']); ?> - 
                    <?php echo $item['quantity']; ?> x $<?php echo number_format($item['price'], 2); ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <p><strong>Total Price:</strong> $<?php echo number_format($total_price, 2); ?></p>
        
        <form method="POST">
    <p><strong>Delivery Details:</strong></p>
    <input type="text" name="address" placeholder="Address" required>
    <input type="text" name="city" placeholder="City" required>
    <input type="text" name="postal_code" placeholder="Postal Code" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="tel" name="phone" placeholder="Phone Number" pattern="[0-9]{10}" title="Enter a valid 10-digit phone number" required>
    <button type="submit">Confirm Order</button>
</form>

    </main>

    <footer>
        <p>&copy; 2024 Clothing Shop</p>
    </footer>
</body>
</html>
