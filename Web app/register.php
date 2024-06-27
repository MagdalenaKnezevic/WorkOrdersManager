<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if(isset($_POST['submit'])) {
    // Getting all values from the HTML form
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hashing the password
    $phoneNumber = $_POST['phoneNumber'];
    $company = $_POST['company'];
    $position = $_POST['position'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $role = $_POST['role'];

    // Database details
    $host = "localhost";
    $username = "root";
    $dbPassword = ""; // Use a different variable name to avoid confusion with the password from the form
    $dbname = "work_orders_manager";

    // Creating a connection
    $con = new mysqli($host, $username, $dbPassword, $dbname);

    // To ensure that the connection is made
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

    // Using prepared statements to create a data entry query
    $stmt = $con->prepare("INSERT INTO users (firstName, lastName, email, password, phoneNumber, company, position, city, country, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $firstName, $lastName, $email, $password, $phoneNumber, $company, $position, $city, $country, $role);

    // Executing the statement
    if ($stmt->execute()) {
        echo "Entries added!";
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Closing the statement and connection
    $stmt->close();
    $con->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
<header>
    <p id="header">Work Orders Manager</p>
    <img class="logo" alt="logo" src="Images/splash.png" loading="lazy">
</header>
<div class="form">
    <form action="register.php" method="post">
        <p>
            <label for="firstName">First Name</label>
            <input type="text" name="firstName" id="firstName" required>
        </p>
        <p>
            <label for="lastName">Last Name</label>
            <input type="text" name="lastName" id="lastName" required>
        </p>
        <p>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </p>
        <p>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </p>
        <p>
            <label for="phoneNumber">Phone Number</label>
            <input type="text" name="phoneNumber" id="phoneNumber">
        </p>
        <p>
            <label for="company">Company</label>
            <input type="text" name="company" id="company">
        </p>
        <p>
            <label for="position">Position</label>
            <input type="text" name="position" id="position">
        </p>
        <p>
            <label for="city">City</label>
            <input type="text" name="city" id="city">
        </p>
        <p>
            <label for="country">Country</label>
            <input type="text" name="country" id="country">
        </p>
        <p>
            <label for="role">Role</label>
        </p>
        <p class="role-selection">
            <input type="radio" name="role" value="worker" id="worker" required> 
            <label for="worker" class="inline-label">Worker</label>
            <input type="radio" name="role" value="director" id="director" required> 
            <label for="director" class="inline-label">Director</label>
        </p>
        <p class="submit-button">
            <input type="submit" name="submit" id="submit" value="Submit">
        </p>
        <p>
            <a href="login.php">Already have an account? Log In here!</a>
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
</body>
</html>
