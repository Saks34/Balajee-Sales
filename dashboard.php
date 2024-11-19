<?php
session_start();

// Include database connection
include('db.php');

// Session timeout (30 minutes)
$timeout_duration = 30 * 60;

// Check if the session has expired
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration)) {
    // Session timed out
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=true");
    exit();
}

// Update last activity time
$_SESSION['LAST_ACTIVITY'] = time();

// If session doesn't exist, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data from the database
$sql = "SELECT * FROM users WHERE user_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User exists, redirect to index.php
    header("Location: index.php");
    exit();
} else {
    echo "User not found!";
}

$stmt->close();
?>
