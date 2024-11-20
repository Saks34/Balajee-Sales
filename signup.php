<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the values from the form input fields
    $name = $_POST['username']; // This should be used for the username
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if ($password === $cpassword) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert the new user into the database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password); // Bind $name, $email, and $hashed_password
        
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            header("Location: login.php");
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Passwords do not match!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        /* General body and container styles */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.container-fluid {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.modal-content {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    padding: 40px;
    width: 400px;
}

.ele {
    width: 100%;
}

.form-header {
    text-align: center;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dot {
    font-size: 1.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #17a2b8;
    color: #fff;
    cursor: pointer;
    transform: translateY(7px) rotate(180deg);
    text-decoration: none
}


.modal-title {
    font-size: 1.5rem;
    font-weight: bold;
}

.form-body {
    margin-top: 20px;
}

.form-body .modal-body {
    display: flex;
    flex-direction: column;
}

input[type="text"],
input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-top: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 1rem;
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus {
    border-color: #17a2b8;
    outline: none;
}

.text-danger {
    color: #e74c3c;
    font-size: 0.8em;
}

.form-check {
    display: flex;
    justify-content: space-between;
    margin-top: 15px;
}

.custom-control-label {
    font-size: 0.9em;
}

.d-flex {
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn {
    padding: 10px 20px;
    font-size: 1rem;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-primary {
    background-color: #007bff;
    color: #fff;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.link-highlight {
    color: #17a2b8;
    text-decoration: none;
}

.link-highlight:hover {
    text-decoration: underline;
}

small {
    font-size: 0.8em;
    display: block;
    text-align: center;
    margin-top: 20px;
}

.pt-10 {
    padding-top: 10px;
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
    <div class="container-fluid px-0 py-0">
        <div class="modal-content d-flex flex-column align-items-center justify-content-center vh-100">
            <div class="ele tab-content border border-1 border-grey p-4 rounded-4 shadow-lg w-50">
                <div class="tab-pane active">
                    <div class="form-header" style="opacity: 100%;">
                        <div class="modal-header">
                            <a class="dot btn btn-info text-dark rounded-circle p-end-2 mb-5 align-items-center" title="Sign Up" href="login.php">➣</a>
                            <div class="d-flex align-items-center justify-content-center w-100 mt-5 pt-2">
                                <h5 class="modal-title text-center">Welcome</h5>
                            </div>
                            <a class="dot btn btn-info text-dark rounded-circle px-2.01 mb-5 align-items-center" title="Exit" href="index.php">✖</a>
                        </div>
                    </div>
                    <div class="form-body">
                        <div class="modal-body">
                            <form action="signup.php" method="POST">
                                <div class="mt-3">
                                    <label class="prelabel px-2" for="name">Username:</label>
                                    <input type="text" class="form-control rounded-5" id="username" name="username" placeholder="Your Name" required>
                                    <div class="text-danger">
                                        <!-- Show error message if username is invalid -->
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="prelabel px-2" for="email">Email:</label>
                                    <input type="email" class="form-control rounded-5" id="email" name="email" placeholder="name@email.com" required>
                                    <div class="text-danger">
                                        <!-- Show error message if email is invalid -->
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="text-size-1 px-2" for="password">Password:</label>
                                    <input type="password" class="form-control rounded-5" id="password" name="password" placeholder="Password" required>
                                    <div class="text-danger">
                                        <!-- Show error message if password is invalid -->
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="text-size-1 px-2" for="cpassword">Confirm Password:</label>
                                    <input type="password" class="form-control rounded-5" id="cpassword" name="cpassword" placeholder="Confirm Password" required>
                                    <div class="text-danger">
                                        <!-- Show error message if confirm password is invalid -->
                                    </div>
                                </div>
                                <div class="form-check custom-control custom-checkbox mt-2">
                                    <div class="float-left">
                                        <input type="checkbox" class="custom-control-input" name="remember" value="1" id="remember" checked>
                                        <label class="custom-control-label" for="remember">Remember me</label>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-center">
                                    <button class="btn btn-primary btn-block mt-3 px-3" type="submit" name="submit" id="submit">Sign Up</button>
                                </div>
                            </form>
                        </div>
                        <div class="text-center align-items-center px-2 mt-1" style="font-size:0.9em;">
                            <p>Already have an account?
                                <a class="link-highlight register-tab-link" href="login.php">Login</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer text-center">
        © 2024 Balajee Sales. All rights reserved.
    </footer>
</body>
</html>
