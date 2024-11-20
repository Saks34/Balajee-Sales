<?php
session_start();
include('db.php'); // Include the database connection

// Check if form is submitted

    
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];  
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['LAST_ACTIVITY'] = time(); 
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Incorrect password.";
        }
    } else {
        $error_message = "No user found with that email.";
    }
    $stmt->close();
}

    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
    <style>
        .dot{
            text-decoration: none
        }
    </style>
</head>
<body>
    <div class="container-fluid px-0 py-0">
        <div class="modal-content d-flex flex-column align-items-center justify-content-center vh-100">
            <div class="ele tab-content border border-1 border-grey p-4 rounded-4 shadow-lg w-20">
                <div class="tab-pane active">
                    <div class="form-header" style="opacity: 100%;">
                        <div class="modal-header">
                            <a class="dot btn btn-info text-dark rounded-circle p-end-2 mb-5 align-items-center" title="Sign Up" href="signup.php">➣</a>
                            <div class="d-flex align-items-center justify-content-center w-100 mt-5 pt-2">
                                <h5 class="modal-title text-center">&nbsp;&nbsp;Welcome back!!!</h5>
                            </div>
                            <a class="dot btn btn-info text-dark rounded-circle px-2.01 mb-5 align-items-center" title="Exit" href="index.php">✖</a>
                        </div>
                    </div>
                    <div class="form-body">
                        <div class="modal-body">
                            <form action="login.php" method="POST">
                                <div class="mt-3">
                                    <label class="prelabel px-2" for="email">&nbsp;Username or Email:</label>
                                    <input type="email" class="form-control rounded-5" id="email" name="email" placeholder="name@email.com" required/>
                                </div>
                                <div class="mt-3">
                                    <label class="text-size-1 px-2" for="password">&nbsp;Password:</label>
                                    <input type="password" class="form-control rounded-5" id="password" name="password" placeholder="Password" required/>
                                </div>
                                <?php if (!empty($error_message)): ?>
                                    <div class="text-danger mt-2">
                                        <p style="font-size:0.8em;"><?= $error_message; ?></p>
                                    </div>
                                <?php endif; ?>
                                <div class="form-check custom-control custom-checkbox mt-2">
                                    <div class="float-left">
                                        <input type="checkbox" class="custom-control-input" name="remember" value="1" id="remember" checked=""/>
                                        <label class="custom-control-label" for="remember">&nbsp;Remember me</label>
                                    </div>
                                    <div class="float-right mt-2" style="font-size: 0.9em;">
                                        <p><a href="forgot_password.php" class="link-highlight text-forgot">Forgot password?</a></p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-center">
                                    <button class="btn btn-primary btn-block mt-3" type="submit">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <small>
                        <div class="text-center align-items-center mt-2">
                            Don't have an account?
                            <a class="link-highlight" href="signup.php">Register</a>
                        </div>
                    </small>
                </div>
            </div>
        </div>
    </div>
    <style>
       
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

input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-top: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 1rem;
}

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

.forgot-tab-link {
    color: #17a2b8;
    text-decoration: none;
}

.forgot-tab-link:hover {
    text-decoration: underline;
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
    <footer class="footer text-center">
        © 2024 Balajee Sales. All rights reserved.
    </footer>

</body>
</html>
