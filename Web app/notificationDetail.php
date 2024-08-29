<?php
session_start();
require_once 'db.php';

// Provjera da li je korisnik prijavljen
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


$notificationId = isset($_GET['notificationId']) ? intval($_GET['notificationId']) : 0;
$userId = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

// Označi obavijest kao pročitanu
$query = "UPDATE notifications SET is_read = 1 WHERE id = ? AND userId = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("ii", $notificationId, $userId);
$stmt->execute();
$stmt->close();

// Dohvati detalje obavijesti
$query = "SELECT * FROM notifications WHERE id = ? AND userId = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("ii", $notificationId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$notification = $result->fetch_assoc();
$stmt->close();

// Provjeri ako obavijest ne postoji
if (!$notification) {
    header('Location: notifications.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Detail</title>
    <link rel="stylesheet" href="notificationDetail.css">
</head>
<body>

<div class="header">
        <div class="left-section">
            <img class="menu" alt="menu" src="Images/menu.png" onclick="toggleMenu()" loading="lazy">
            <h1><span class="normal-weight">WorkOrders</span><span class="bold-weight">Manager</span></h1>
        </div>
        <div class="profile-menu">
            <img class="logo" alt="logo" src="Images/user.png" loading="lazy">
            <div class="dropdown-content">
                <a href="#"><?php echo htmlspecialchars($_SESSION['firstName'] . ' ' . $_SESSION['lastName']); ?></a>
                <a href="profile.php">Profile</a>
                <a href="#" onclick="document.getElementById('logout-form').submit();">Log Out</a>
                <form id="logout-form" method="post" style="display: none;">
                    <input type="hidden" name="logout">
                </form>
                <form id="change-account-form" method="post" style="display: none;">
                    <input type="hidden" name="change_account">
                </form>
            </div>
        </div>
    </div>

    <div id="side-menu" class="side-menu">
        <a href="#" class="closebtn" onclick="closeMenu()">&times;</a>
        <a href="worker.php">Home</a>
        <a href="ordersWorkerList.php">Work Orders</a>
        <a href="teamsListWorker.php">Teams</a>
        <a href="notifications.php">Notifications</a>
        <a href="#" onclick="document.getElementById('logout-form').submit();">Log Out</a>
    </div>

    <script>
        function toggleMenu() {
            var menu = document.getElementById("side-menu");
            if (menu.style.width === "250px") {
                menu.style.width = "0";
            } else {
                menu.style.width = "250px";
            }
        }

        function closeMenu() {
            document.getElementById("side-menu").style.width = "0";
        }

            // Zatvaranje izbornika kad se klikne na stavku unutar izbornika
    document.querySelectorAll('.side-menu a').forEach(item => {
        item.addEventListener('click', () => {
            closeMenu();
        });
    });

        document.addEventListener('DOMContentLoaded', (event) => {
        const workOrders = document.querySelectorAll('.work-order');
        workOrders.forEach(order => {
            order.addEventListener('click', () => {
                const orderId = order.getAttribute('data-id');
                window.location.href = `fullWorkOrderWorker.php?id=${orderId}`;
            });
        });
    });
    </script>


    <h2>Notification details:</h2>
    <p><strong>Notification:</strong> <?php echo htmlspecialchars($notification['message']); ?></p>
    <p><strong>Date:</strong> <?php echo htmlspecialchars($notification['created_at']); ?></p>

    <button onclick="window.history.back();" id="back-button">Back</button>


    
</body>
</html>
