<?php
session_start();
require_once 'db.php';

// Provjera da li je korisnik prijavljen
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Dohvati ID radnog naloga iz GET parametra
$workOrderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Dohvati radni nalog iz baze podataka
$stmt = $con->prepare("SELECT * FROM workOrders WHERE id = ?");
$stmt->bind_param("i", $workOrderId);
$stmt->execute();
$result = $stmt->get_result();
$workOrder = $result->fetch_assoc();
$stmt->close();

// Ako radni nalog ne postoji, preusmjeri korisnika
if (!$workOrder) {
    header('Location: director.php');
    exit;
}

// Ažuriraj radni nalog
if (isset($_POST['submit'])) {
    $status = $_POST['status'];

    // Ažuriraj samo status
    if ($status == 'On hold' || $status == 'Accepted' || $status == 'In process') {
        $stmt = $con->prepare("UPDATE workOrders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $workOrderId);
    } else { // Ažuriraj sve podatke
        $descriptionDone = $_POST['descriptionDone'];
        $material = $_POST['material'];
        $endDate = $_POST['endDate'];
        $workersNote = $_POST['workersNote'];

        $stmt = $con->prepare("UPDATE workOrders SET status = ?, descriptionDone = ?, material = ?, endDate = ?, workersNote = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $status, $descriptionDone, $material, $endDate, $workersNote, $workOrderId);
    }

    if ($stmt->execute()) {
        header("Location: director.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Order Details</title>
    <link rel="stylesheet" href="fullWorkOrderDirector.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css">
</head>
<body>
<div class="container">
    <form action="fullWorkOrderWorker.php?id=<?php echo $workOrderId; ?>" method="post">
        <h2>Filled in by Executor:</h2>
        <label for="number">Work Order Number:</label>
        <input type="text" id="number" value="<?php echo htmlspecialchars($workOrder['number']); ?>" readonly><br>

        <label for="startDate">Date:</label>
        <input type="text" id="startDate" value="<?php echo htmlspecialchars($workOrder['startDate']); ?>" readonly><br>

        <label for="type">Work Order Type:</label>
        <input type="text" id="type" value="<?php echo htmlspecialchars($workOrder['type']); ?>" readonly><br>

        <label for="descriptionToDO">To Do:</label>
        <textarea id="descriptionToDO" readonly><?php echo htmlspecialchars($workOrder['descriptionToDO']); ?></textarea><br>

        <label for="directorsNote">Note:</label>
        <textarea id="directorsNote" readonly><?php echo htmlspecialchars($workOrder['directorsNote']); ?></textarea><br>

        <h2>Filled in by :</h2>
        <label for="status">Status:</label>
        <textarea id="status" readonly><?php echo htmlspecialchars($workOrder['status']); ?></textarea><br>

        <div id="executorFields" style="display: none;">
            <label for="descriptionDone">Description of done work:</label>
            <textarea id="descriptionDone" readonly><?php echo htmlspecialchars($workOrder['descriptionDone']); ?></textarea><br>

            <label for="material">Materials used:</label>
            <textarea id="material" readonly><?php echo htmlspecialchars($workOrder['material']); ?></textarea><br>

            <label for="endDate">End of work:</label>
            <input type="text" id="endDate" value="<?php echo htmlspecialchars($workOrder['endDate']); ?>" readonly><br>

            <label for="workersNote">Note:</label>
            <textarea id="workersNote" readonly><?php echo htmlspecialchars($workOrder['workersNote']); ?></textarea><br>
        </div>
    </form>
</div>

<div class="button-container">
    <button onclick="window.history.back();" id="back-button">Back</button>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
$(function() {
    $("#endDate").datepicker({ dateFormat: 'yy-mm-dd' });

    $("#status").change(function() {
        var status = $(this).val();
        if (status === 'Finished') {
            $("#executorFields").show();
        } else {
            $("#executorFields").hide();
        }
    }).trigger('change'); // Initial trigger to set the correct display based on the current status
});
</script>
</body>
</html>
