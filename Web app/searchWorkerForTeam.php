<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Pronađi korisnike s ulogom 'worker' koji imaju sličan email
    $query = "SELECT id, firstName, lastName, email FROM users WHERE email LIKE ? AND role = 'worker'";
    $stmt = $con->prepare($query);
    $searchEmail = "%" . $email . "%";
    $stmt->bind_param("s", $searchEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='worker-result'>";
            echo "<span>" . htmlspecialchars($row['firstName'] . " " . $row['lastName']) . " (" . htmlspecialchars($row['email']) . ")</span>";
            echo "<button type='button' onclick=\"addWorker(" . $row['id'] . ", '" . htmlspecialchars($row['firstName'] . " " . $row['lastName']) . "')\">Add</button>";
            echo "</div>";
        }
    } else {
        echo "Nema rezultata.";
    }

    $stmt->close();
}
?>
