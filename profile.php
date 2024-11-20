<?php
session_start();
include('db.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$query = "SELECT username, email, password FROM users WHERE user_id = ?";
$stmt =$conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Password display option (prompt for old password)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['old_password'])) {
    // Verify old password
    $old_password = $_POST['old_password'];

    if (password_verify($old_password, $user['password'])) {
        // If the old password is correct, allow the user to change it
        $new_password = $_POST['new_password'];
        $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);

        // Update the password in the database
        $update_query = "UPDATE users SET password = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("si", $new_password_hash, $user_id);
        $update_stmt->execute();

        echo "Password updated successfully!";
    } else {
        echo "The old password is incorrect!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
</head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<Style>
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
<body>
<?php include 'navbar.php'; ?>

<h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>

<div class="container mt-5">
    <form action="profile.php" method="POST" class="p-4 border rounded shadow-sm bg-light">
        <h2 class="text-center mb-4">Change Password</h2>

        <!-- Email Display -->
        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" class="form-control" name="email" 
                   value="<?php echo htmlspecialchars($user['email']); ?>" readonly disabled>
        </div>

        <!-- Current Password -->
        <div class="mb-3">
            <label for="old_password" class="form-label">Enter your current password:</label>
            <input type="password" class="form-control" name="old_password" required>
        </div>

        <!-- New Password -->
        <div class="mb-3">
            <label for="new_password" class="form-label">Enter your new password:</label>
            <input type="password" class="form-control" name="new_password" required>
        </div>

        <!-- Submit Button -->
        <div class="d-grid w-20">
            <input type="submit" class="btn btn-primary" value="Change Password">
        </div>
    </form>
</div>
<footer class="footer text-center">
        © 2024 Balajee Sales. All rights reserved.
    </footer>
</body>
</html>
