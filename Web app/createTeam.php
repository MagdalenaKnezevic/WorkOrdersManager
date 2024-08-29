<?php
session_start();
require_once 'db.php';

// Provjera da li je korisnik prijavljen
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$userId = isset($_SESSION['id']) ? $_SESSION['id'] : 0;
$firstName = isset($_SESSION['firstName']) ? $_SESSION['firstName'] : 'First';
$lastName = isset($_SESSION['lastName']) ? $_SESSION['lastName'] : 'Last';

// Dodavanje tima u bazu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_team'])) {
    $teamName = $_POST['team_name'];
    $teamDescription = $_POST['team_description'];
    $workerIds = isset($_POST['worker_ids']) ? explode(',', $_POST['worker_ids']) : [];

    // Insert into teams
    $query = "INSERT INTO teams (nameOfTeams, description, director) VALUES (?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ssi", $teamName, $teamDescription, $userId);
    $stmt->execute();
    $teamId = $stmt->insert_id;
    $stmt->close();

    // Insert into teamsWorkers and create notifications
    foreach ($workerIds as $workerId) {
        // Insert into teamsWorkers
        $query = "INSERT INTO teamsWorkers (teamId, workerId) VALUES (?, ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ii", $teamId, $workerId);
        $stmt->execute();
        $stmt->close();

        // Create notification for the worker
        $message = "Novi tim: $teamName";
        $query = "INSERT INTO notifications (userId, message, created_at) VALUES (?, ?, NOW())";
        $stmt = $con->prepare($query);
        $stmt->bind_param("is", $workerId, $message);
        $stmt->execute();
        $stmt->close();
    }

    header('Location: teamsList.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Team</title>
    <link rel="stylesheet" href="createTeam.css">
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
        <h2>Create Team</h2>
        <form method="post" action="createTeam.php">
            <label for="team_name">Name:</label>
            <input type="text" id="team_name" name="team_name" required>

            <label for="team_description">Description:</label>
            <textarea id="team_description" name="team_description" required></textarea>

            <label for="selected_workers">Workers:</label>
            <div id="selected-workers" class="selected-workers">
                <!-- Odabrani radnici će biti prikazani ovdje -->
            </div>

            <button type="button" onclick="toggleWorkerForm()">Press to add workers</button>
            
            <div id="worker-form" style="display:none;">
                <h3>Add workers:</h3>
                <input type="text" id="worker_email" placeholder="Enter worker's email">
                <button type="button" onclick="searchWorker()">Search</button>

                <div id="worker-search-results"></div>
            </div>

            <input type="hidden" name="worker_ids" id="worker_ids">

            <button type="submit" name="create_team">Create Team</button>
        </form>
    </div>

    <div class="button-container">
        <button onclick="window.history.back();" id="back-button">Back</button>
    </div>

    <script>
        let selectedWorkers = [];

        function toggleMenu() {
            var menu = document.getElementById("side-menu");
            if (menu.style.width === "250px") {
                menu.style.width = "0";
            } else {
                menu.style.width = "250px";
            }
        }

        function closeMenu() {
            document.getElementById("side-menu" ).style.width = "0";
        }

        function toggleWorkerForm() {
            var workerForm = document.getElementById("worker-form");
            if (workerForm.style.display === "none") {
                workerForm.style.display = "block";
            } else {
                workerForm.style.display = "none";
                // Resetiraj unos i rezultate pretrage
                document.getElementById("worker_email").value = "";
                document.getElementById("worker-search-results").innerHTML = "";
            }
        }

        function searchWorker() {
            var email = document.getElementById("worker_email").value;
            fetch('searchWorkerForTeam.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'email': email
                })
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById("worker-search-results").innerHTML = data;
            })
            .catch(error => console.error('Error:', error));
        }

        function addWorker(workerId, workerName) {
            if (!selectedWorkers.includes(workerId)) {
                selectedWorkers.push(workerId);
                var workerList = document.getElementById("selected-workers");
                var workerItem = document.createElement("div");
                workerItem.textContent = workerName;
                workerItem.className = "worker-item";
                workerItem.setAttribute('data-worker-id', workerId);

                // Dodavanje iksića za uklanjanje radnika
                var removeButton = document.createElement("span");
                removeButton.textContent = "✖";
                removeButton.className = "remove-worker";
                removeButton.onclick = function() {
                    removeWorker(workerId);
                };

                workerItem.appendChild(removeButton);
                workerList.appendChild(workerItem);
                document.getElementById("worker_ids").value = selectedWorkers.join(",");
            }
        }

        function removeWorker(workerId) {
            selectedWorkers = selectedWorkers.filter(id => id != workerId);
            var workerItem = document.querySelector(`.worker-item[data-worker-id='${workerId}']`);
            if (workerItem) {
                workerItem.remove();
            }
            document.getElementById("worker_ids").value = selectedWorkers.join(",");
        }
    </script>
</body>
</html>
