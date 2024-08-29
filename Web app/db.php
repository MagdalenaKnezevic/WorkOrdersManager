<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "work_orders_manager";

$con = new mysqli($host, $username, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

function getWorkersByEmail($email) {
    global $con;
    $stmt = $con->prepare("SELECT id, firstName, lastName, email FROM users WHERE email LIKE ?");
    $likeEmail = "%$email%";
    $stmt->bind_param("s", $likeEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $workers = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $workers;
}
?>
