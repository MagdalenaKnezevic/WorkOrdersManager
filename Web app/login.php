<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user data from database
    $stmt = $con->prepare("SELECT id, email, password, role, firstName, lastName FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $email, $hashed_password, $role, $firstName, $lastName);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Store data in session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;
            $_SESSION['firstName'] = $firstName;
            $_SESSION['lastName'] = $lastName;

            // Redirect user to appropriate page
            if ($role === 'worker') {
                header('Location: worker.php');
            } elseif ($role === 'director') {
                header('Location: director.php');
            }
            exit;
        } else {
            // Password is not valid
            $login_err = "Invalid email or password.";
        }
    } else {
        // No user found with that email
        $login_err = "Invalid email or password.";
    }
    $stmt->close();
}
$con->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" href="login.css">
    <style>
        .password-container {
            position: relative;
        }
        .password-container input[type="checkbox"] {
            position: absolute;
            top: 0;
            right: 0;
            cursor: pointer;
        }
    </style>
</head>
<body>
<header>
    <p id="header">Work Orders Manager</p>
    <img class="logo" alt="logo" src="Images/splash.png" loading="lazy">
</header>
<div class="form">
    <form action="login.php" method="post">
        <p>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </p>
        <p>
            <label for="password">Password</label>
            <div class="password-container">
                <input type="password" name="password" id="password" required>
                
            </div>
            <input type="checkbox" id="showPassword">
        </p>
        <?php 
        if(isset($login_err)) {
            echo '<p style="color:red;">'. $login_err .'</p>';
        }
        ?>
        <p>
            <input type="submit" value="Log In">
        </p>
        <p>
            <a href="register.php">You don't have an account? Register here!</a>
        </p>
    </form>
</div>

<!--Footer-->
<div class="footer">
    <div class="content">
        <p>WORK ORDERS MANAGER</p>
        <div class="social-links" role="navigation" aria-label="Social media links">
            <img class="facebook-icon" alt="Facebook icon" src="Images/Facebook.png" loading="lazy"/>
            <img class="facebook-icon" alt="Instagram icon" src="Images/Instagram.png" loading="lazy"/>
            <img class="facebook-icon" alt="Twitter icon" src="Images/Twitter.png" loading="lazy"/>
            <img class="facebook-icon" alt="LinkedIn icon" src="Images/LinkedIn.png" loading="lazy"/>
        </div>
    </div>
    <div class="credits" aria-label="Footer credits">
        <div class="divider"></div>
        <div class="row">
            <p>Made by <a href="https://github.com/MagdalenaKnezevic">Magdalena Knezevic</a></p>
            <div class="footer-links" role="navigation" aria-label="Footer navigation">
                <div class="home">Privacy Policy</div>
                <div class="home">Terms of Service</div>
                <div class="home">Cookies Settings</div>
            </div>
        </div>
    </div>
</div>

<script>
    const passwordInput = document.getElementById('password');
    const showPasswordCheckbox = document.getElementById('showPassword');

    showPasswordCheckbox.addEventListener('change', () => {
        if (showPasswordCheckbox.checked) {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }
    });
</script>
</body>
</html>
