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

// Dohvati timove koje je napravio trenutni korisnik
$query = "SELECT * FROM teams WHERE director = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$teams = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teams List</title>
    <link rel="stylesheet" href="teamsList.css">
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
        <a href="director.php">Home</a>
        <a href="ordersDirectorList.php">Work Orders</a>
        <a href="teamsList.php">Teams</a>
        <a href="#" onclick="document.getElementById('logout-form').submit();">Log Out</a>
    </div>

    <div class="container">
        <div class="add-team">
            <a href="createTeam.php"><img src="Images/plus.png" alt="Add team"> Add new team</a>
        </div>

        <h2>Existing teams:</h2>
        <?php if (count($teams) > 0): ?>
            <div class="teams-list">
                <?php foreach ($teams as $team): ?>
                    <div class="team">
                        <a href="teamDetails.php?teamId=<?php echo $team['id']; ?>">
                            <img src="Images/team.png" alt="Team icon">
                            <span><?php echo htmlspecialchars($team['nameOfTeams']); ?></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Trenutno nema timova za prikaz.</p>
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
