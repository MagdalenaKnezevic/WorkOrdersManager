<?php
session_start();
require_once 'db.php';

// Provjera da li je korisnik prijavljen
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Logout logika
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$userId = isset($_SESSION['id']) ? $_SESSION['id'] : 0;
$firstName = isset($_SESSION['firstName']) ? $_SESSION['firstName'] : 'First';
$lastName = isset($_SESSION['lastName']) ? $_SESSION['lastName'] : 'Last';

// Dohvati obavijesti za trenutnog korisnika
$query = "SELECT * FROM notifications WHERE userId = ? ORDER BY created_at DESC";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);
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
    <title>Notifications</title>
    <link rel="stylesheet" href="notifications.css">
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
                <a href="#"><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></a>
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
        <h2>Notifications:</h2>
        <?php if (count($notifications) > 0): ?>
            <ul class="notification-list">
                <?php foreach ($notifications as $notification): ?>
                    <li class="<?php echo $notification['is_read'] ? '' : 'unread'; ?>">
                        <a href="notificationDetail.php?notificationId=<?php echo $notification['id']; ?>">
                            <strong><?php echo htmlspecialchars($notification['message']); ?></strong>
                            <br>
                            <small><?php echo htmlspecialchars($notification['created_at']); ?></small>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>There are no notifications.</p>
        <?php endif; ?>
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
    </script>
</body>
</html>
