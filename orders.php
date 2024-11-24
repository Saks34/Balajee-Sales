<?php
include 'db.php';

$sql = "SELECT * FROM orders";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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

        .table th, .table td {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4">Orders</h2>
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>User ID</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $order['order_id']; ?></td>
                        <td><?php echo $order['user_id']; ?></td>
                        <td>
                            <?php 
                                // Display status as a badge with color based on the status
                                $status = strtolower(trim($order['status']));
                                switch ($status) {
                                    case 'pending':
                                        echo '<span class="badge bg-secondary">Pending</span>';
                                        break;
                                    case 'processing':
                                        echo '<span class="badge bg-warning">Processing</span>';
                                        break;
                                    case 'shipped':
                                        echo '<span class="badge bg-primary">Shipped</span>';
                                        break;
                                    case 'delivered':
                                        echo '<span class="badge bg-success">Delivered</span>';
                                        break;
                                    case 'cancelled':
                                        echo '<span class="badge bg-danger">Cancelled</span>';
                                        break;
                                    default:
                                        echo '<span class="badge bg-info">' . ucfirst($order['status']) . '</span>';
                                        break;
                                }
                            ?>
                        </td>
                        <td>
                            <a href="admin_orderdetails.php?id=<?php echo $order['order_id']; ?>" class="btn btn-info btn-sm">Read More</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <footer class="footer text-center">
        © 2024 Balajee Sales. All rights reserved.
    </footer>
</body>
</html>
