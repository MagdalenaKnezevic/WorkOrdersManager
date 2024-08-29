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


$teamId = isset($_GET['teamId']) ? $_GET['teamId'] : 0;
$firstName = isset($_SESSION['firstName']) ? $_SESSION['firstName'] : 'First';
$lastName = isset($_SESSION['lastName']) ? $_SESSION['lastName'] : 'Last';
$customer = $firstName . ' ' . $lastName;

// Dohvati informacije o timu
$query = "SELECT * FROM teams WHERE id = ?";
$stmt = $con->prepare($query);
if (!$stmt) {
    die('Prepare failed: ' . $con->error);
}
$stmt->bind_param("i", $teamId);
$stmt->execute();
$result = $stmt->get_result();
$team = $result->fetch_assoc();
$stmt->close();

// Dohvati radnike u timu
$query = "SELECT w.id, w.firstName, w.lastName FROM teamsworkers tw
          JOIN users w ON tw.workerId = w.id
          WHERE tw.teamId = ?";
$stmt = $con->prepare($query);
if (!$stmt) {
    die('Prepare failed: ' . $con->error);
}
$stmt->bind_param("i", $teamId);
$stmt->execute();
$result = $stmt->get_result();
$workers = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Dohvati broj nepročitanih obavijesti
$query = "SELECT COUNT(*) as unread_count FROM notifications WHERE userId = ? AND is_read = 0";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$notificationCount = $result->fetch_assoc()['unread_count'];
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Details</title>
    <link rel="stylesheet" href="teamDetails.css">
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
        <a href="notifications.php">Notifications <span id="notification-count"><?php echo $notificationCount; ?></span></a>
        <a href="#" onclick="document.getElementById('logout-form').submit();">Log Out</a>
    </div>

    <div class="container">
        <h2>Team Details:</h2>
        <?php if ($team): ?>
            <div class="team-details">
                <h3>Name: <?php echo htmlspecialchars($team['nameOfTeams']); ?></h3>
                <p>Description: <?php echo htmlspecialchars($team['description']); ?></p>

                <h4>Workers in team:</h4>
                <ul>
                    <?php foreach ($workers as $worker): ?>
                        <li><?php echo htmlspecialchars($worker['firstName'] . ' ' . $worker['lastName']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <p>There are no teams found.</p>
        <?php endif; ?>
    </div>

    <button onclick="window.history.back();" id="back-button">Back</button>


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
    </script>
</body>
</html>
