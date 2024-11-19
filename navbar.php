<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';
// Fetch role if user is logged in and role is not set in the session
if (isset($_SESSION['user_id']) && !isset($_SESSION['role'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT role FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($role);
    if ($stmt->fetch()) {
        $_SESSION['role'] = $role; // Store the role in the session
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar Example</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-size: 24px;
            font-weight: bold;
        }
        .nav-link {
            font-size: 16px;
        }
        .nav-link:hover {
            background-color: #007bff;
            border-radius: 5px;
            color: #fff !important;
        }
        .dropdown-menu {
            background-color: #343a40;
        }
        .dropdown-item {
            color: #ddd;
        }
        .dropdown-item:hover {
            background-color: #007bff;
            color: white !important;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Balajee Sales</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="bi bi-house-door-fill"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="products.php?category=All"><i class="bi bi-bag-fill"></i> Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cart.php"><i class="bi bi-cart-fill"></i> Cart</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="orders.php">Manage Orders</a></li>
                        <li class="nav-item"><a class="nav-link" href="all_products.php">Manage Products</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="customer_order.php">My Orders</a></li>
                    <?php endif; ?>
                    <!-- User dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-fill"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php"><i class="bi bi-person-fill"></i> Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="signup.php"><i class="bi bi-person-plus-fill"></i> Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Add Bootstrap JS -->

</body>
</html>
