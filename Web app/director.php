<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Logout logic
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Change account logic
if (isset($_POST['change_account'])) {
    // Add logic to change account details
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Orders Manager</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
        }
        .header h1 {
            margin: 0;
        }
        .profile-menu {
            position: relative;
            display: inline-block;
        }
        .profile-menu button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px;
            cursor: pointer;
        }
        .profile-menu button:focus {
            outline: none;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 150px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {
            background-color: #ddd;
        }
        .profile-menu:hover .dropdown-content {
            display: block;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Work Orders Manager</h1>
        <div class="profile-menu">
            <button>Profile</button>
            <div class="dropdown-content">
                <a href="#" onclick="document.getElementById('logout-form').submit();">Log Out</a>
                <a href="#">Change Account</a>
                <form id="logout-form" method="post" style="display: none;">
                    <input type="hidden" name="logout">
                </form>
                <form id="change-account-form" method="post" style="display: none;">
                    <input type="hidden" name="change_account">
                </form>
            </div>
        </div>
    </div>
</body>
</html>
