<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

if(isset($_POST['submit'])) {
        $number = $_POST['number'];
        $startDate = $_POST['startDate'];
        $type = $_POST['type'];
        $descriptionToDO = $_POST['descriptionToDO'];
        $directorsNote = $_POST['directorsNote'];
        $customer = $_SESSION['firstName'] . ' ' . $_SESSION['lastName'];
        $status = 'On hold';

        $stmt = $con->prepare("INSERT INTO workOrders (number, startDate, type, descriptionToDO, customer, directorsNote, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $number, $startDate, $type, $descriptionToDO, $customer, $directorsNote, $status);

        if ($stmt->execute()) {
            $workOrderId = $stmt->insert_id;
            if (isset($_POST['workers'])) {
                foreach ($_POST['workers'] as $workerId) {
                    $stmt = $con->prepare("INSERT INTO workOrderWorkers (workOrderId, workerId) VALUES (?, ?)");
                    $stmt->bind_param("ii", $workOrderId, $workerId);
                    $stmt->execute();
                }
            }
            header("Location: director.php");
        exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

// function getWorkersByEmail($email) {
//     global $con;
//     $stmt = $con->prepare("SELECT id, firstName, lastName, email FROM users WHERE email LIKE ?");
//     $likeEmail = "%$email%";
//     $stmt->bind_param("s", $likeEmail);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $workers = $result->fetch_all(MYSQLI_ASSOC);
//     $stmt->close();
//     return $workers;
// }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Orders Director</title>
    <link rel="stylesheet" href="workOrdersDirector.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css">
</head>
<body>
<div class="container">
    <form action="workOrdersDirector.php" method="post">
        <h2>Fills in the Contractor:</h2>
        <label for="number">Work Order Number:</label>
        <input type="text" name="number" id="number" required><br>

        <label for="startDate">Date:</label>
        <input type="text" id="startDate" name="startDate" required><br>

        <label for="type">Work Order Type:</label>
        <select id="type" name="type" required>
            <option value="Redovni rad">Regular work</option>
            <option value="Intervencija">Intervention</option>
            <option value="Pripravnost">Standby</option>
            <option value="Pripravnost">External service</option>
            <option value="Pripravnost">Duty in facility</option>
        </select><br>

        <label for="descriptionToDO">To Do:</label>
        <textarea id="descriptionToDO" name="descriptionToDO" required></textarea><br>

        <label for="directorsNote">Note:</label>
        <textarea id="directorsNote" name="directorsNote"></textarea><br>
        
        <label for="selected_workers">Workers:</label>
            <div id="selected-workers" class="selected-workers">
                <!-- Odabrani radnici će biti prikazani ovdje -->
            </div>

        <button type="button" id="addWorkerButton">Press to add workers</button><br>

        <div id="workersContainer"></div>

        <div class="create-order-button-container">
            <input class="create-order-button" type="submit" name="submit" value="Create Work Order">
        </div>
    </form>
</div>

<div class="button-container">
    <button onclick="window.history.back();" id="back-button">Back</button>
</div>

<!-- Worker Modal -->
<div id="workerModal" style="display:none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Add worker</h2>
        <input type="text" id="workerEmail" placeholder="Enter worker's email">
        <button id="searchWorkerButton">Search</button>
        <div id="workerResults"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
$(function() {
    $("#startDate").datepicker({ dateFormat: 'yy-mm-dd' });

    var modal = $("#workerModal");
    var addWorkerButton = $("#addWorkerButton");
    var closeAddWorker = $(".close");

    addWorkerButton.on("click", function() {
        $("#workerEmail").val(''); // Resetira polje za unos emaila
        $("#workerResults").html(''); // Opcionalno, resetira rezultate pretrage
        modal.show();
    });

    closeAddWorker.on("click", function() {
        modal.hide();
    });

    $(window).on("click", function(event) {
        if ($(event.target).is(modal)) {
            modal.hide();
        }
    });

    $("#searchWorkerButton").on("click", function() {
        var email = $("#workerEmail").val();
        $.post("searchWorkers.php", { email: email }, function(data) {
            $("#workerResults").html(data);
        });
    });

    $(document).on("click", ".addWorker", function() {
    var workerId = $(this).data("id");
    var workerName = $(this).data("name");

    // Provjeravamo je li radnik već odabran
    if ($("#worker-" + workerId).length === 0) {
        $("#selected-workers").append('<div id="worker-' + workerId + '" class="selected-worker-item">' + 
            workerName + 
            ' <span class="remove-worker" data-id="' + workerId + '">✖</span>' +
            '<input type="hidden" name="workers[]" value="'+workerId+'">' +
            '</div>');
        modal.hide();
    }
    });

    
    // Uklanjanje radnika s radnog naloga
    $(document).on("click", ".remove-worker", function() {
        var workerId = $(this).data("id");
        $("#worker-" + workerId).remove();
    });
});
</script>
</body>
</html>
