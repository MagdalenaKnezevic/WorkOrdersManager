<?php
require_once 'db.php';

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $workers = getWorkersByEmail($email);
    if (!empty($workers)) {
        foreach ($workers as $worker) {
            echo '<div>';
            echo '<span>' . htmlspecialchars($worker['firstName'] . ' ' . $worker['lastName']) . ' (' . htmlspecialchars($worker['email']) . ')</span>';
            echo '<button type="button" class="addWorker" data-id="' . htmlspecialchars($worker['id']) . '" data-name="' . htmlspecialchars($worker['firstName'] . ' ' . $worker['lastName']) . '">Add</button>';
            echo '</div>';
        }
    } else {
        echo '<div>Nema radnika s tim emailom.</div>';
    }
}
?>
