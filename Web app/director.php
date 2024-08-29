<?php
session_start();
require_once 'db.php';

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


$firstName = isset($_SESSION['firstName']) ? $_SESSION['firstName'] : 'First';
$lastName = isset($_SESSION['lastName']) ? $_SESSION['lastName'] : 'Last';
$customer = $firstName . ' ' . $lastName;

// Dohvaćanje radnih naloga iz baze podataka
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$query = "SELECT * FROM workOrders WHERE customer = ? AND (status = 'On hold' OR status = 'Accepted' OR status = 'In process')";
if ($statusFilter) {
    $query .= " AND status = ?";
}
$stmt = $con->prepare($query);

if ($statusFilter) {
    $stmt->bind_param("ss", $customer, $statusFilter);
} else {
    $stmt->bind_param("s", $customer);
}

$stmt->execute();
$result = $stmt->get_result();
$workOrders = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Orders Manager</title>
    <link rel="stylesheet" href="director.css">
    
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

    <div class="grey-divider-1"></div>
    <div class="add-new-order">
        <a href="workOrdersDirector.php">
            <img class="plus" src="Images/plus.png" />
        </a>
        <p class="add-p">Add new Work Order</p>
    </div>
    <div class="grey-divider-2"></div>

    <p class="active-p">Active Work Orders:</p>

    <div class="work-orders-list">
        <?php foreach ($workOrders as $order): ?>
            <div class="work-order" data-id="<?php echo htmlspecialchars($order['id']); ?>">
                <h3>Work Order Number: <?php echo htmlspecialchars($order['number']); ?></h3>
                <p>Contractor: <?php echo htmlspecialchars($order['customer']); ?></p>
                <div class="order-date"><?php echo htmlspecialchars($order['startDate']); ?></div>
                <div class="order-status">
                    <?php if ($order['status'] == 'In process'): ?>
                        <img src="Images/process.png" alt="In process">
                    <?php elseif ($order['status'] == 'On hold'): ?>
                        <img src="Images/hold.png" alt="On hold">
                    <?php elseif ($order['status'] == 'Accepted'): ?>
                        <img src="Images/accept.png" alt="Accepted">
                    <?php elseif ($order['status'] == 'Finished'): ?>
                        <img src="Images/done.png" alt="Finished">
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="side-menu" class="side-menu">
        <a href="#" class="closebtn" onclick="closeMenu()">&times;</a>
        <a href="#">Home</a>
        <a href="ordersDirectorList.php">Work Orders</a>
        <a href="teamsList.php">Teams</a>
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
                window.location.href = `fullWorkOrderDirector.php?id=${orderId}`;
            });
        });
    });
    </script>
</body>
</html>
