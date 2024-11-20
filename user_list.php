<?php
// Database connection
include 'db.php';

// Fetching user list from the database using mysqli
$query = "SELECT user_id, username, email FROM users";
$stmt = $conn->query($query);

// Fetch all the users as an associative array
$users = $stmt->fetch_all(MYSQLI_ASSOC);

// Query to get the pending and delivered orders count for each user
$orderQuery = "SELECT user_id, 
                      SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_orders,
                      SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) AS delivered_orders
               FROM orders
               GROUP BY user_id";
$orderStmt = $conn->query($orderQuery);
$orderCounts = [];
while ($row = $orderStmt->fetch_assoc()) {
    $orderCounts[$row['user_id']] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - User List</title>
    <!-- Adding Bootstrap 4 CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css"> <!-- Link to your custom CSS file -->
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
    <div class="container mt-5">
        <h2 class="mb-4">User List</h2>

        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Pending Orders</th>
                    <th>Delivered Orders</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <?php echo isset($orderCounts[$user['user_id']]) ? $orderCounts[$user['user_id']]['pending_orders'] : 0; ?>
                        </td>
                        <td>
                            <?php echo isset($orderCounts[$user['user_id']]) ? $orderCounts[$user['user_id']]['delivered_orders'] : 0; ?>
                        </td>
                        <td>
                            <!-- Edit and Delete actions -->
                            <a href="edit_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-warning btn-sm">Edit</a> 
                            <a href="delete_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Adding Bootstrap 4 JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <footer class="footer text-center">
        © 2024 Balajee Sales. All rights reserved.
    </footer>
</body>
</html>
