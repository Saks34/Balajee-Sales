<?php
// Start session for potential user verification (if needed)
session_start();

// Include required files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // PHPMailer library
require 'db.php'; // Database connection

// Check if order ID is provided
if (isset($_GET['id'])) {
    $order_id = intval($_GET['id']); // Ensure ID is an integer

    // Fetch customer details and email
    $query = "
    SELECT customerEmail, 
           customerName, 
           details,
           user_id
    FROM orders 
    WHERE order_id = $order_id
";

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $customerEmail = $row['customerEmail'];
        $customerName = $row['customerName'];
        $orderDetails = $row['details'];

        // Update order status to 'delivered'
        $updateQuery = "UPDATE orders SET status = 'delivered' WHERE order_id = $order_id";
        if ($conn->query($updateQuery)) {
            echo "Order marked as delivered!";

            // Send email notification
            try {
                if (filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                    $mail = new PHPMailer(true);

                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'balajeesales09@gmail.com'; // Your Gmail address
                    $mail->Password = 'erzx ijfz emmw izzy'; // Gmail App password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('balajeesales09@gmail.com', 'Balajee Sales');
                    $mail->addAddress($customerEmail, $customerName);

                    // Email content
                    $mail->isHTML(true);
                    $mail->Subject = "Order Delivered - Order #$order_id";
                    $mail->Body = "
                        <h1>Order Delivered</h1>
                        <p>Hi $customerName,</p>
                        <p>Your order with ID <b>$order_id</b> has been successfully delivered.</p>
                        <p>Order Details:</p>
                        <pre>$orderDetails</pre>
                        <br>
                        <p>Thank you for shopping with us!</p>
                        <p><strong>Balajee Sales Team</strong></p>
                    ";

                    // Send the email
                    $mail->send();
                    echo " Email notification sent to the customer.";
                } else {
                    echo " Order marked as delivered, but email could not be sent: Invalid email address.";
                }
            } catch (Exception $e) {
                echo " Order marked as delivered, but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

            header("Location: orders.php");
            exit();
        } else {
            echo "Error updating order status: " . $conn->error;
        }
    } else {
        echo "Customer details not found for the order.";
    }
} else {
    echo "Order ID not provided.";
}
?>
