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
<body>

<h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>

<form action="profile.php" method="POST">
    <label for="email">Email:</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly disabled>
    <br><br>

    <!-- Password Change Form -->
    <label for="old_password">Enter your current password:</label>
    <input type="password" name="old_password" required>
    <br><br>

    <label for="new_password">Enter your new password:</label>
    <input type="password" name="new_password" required>
    <br><br>
    <input type="submit" value="Change Password">
</form>

</body>
</html>
