<?php
session_start();
require_once 'db.php';

// Provjera da li je korisnik prijavljen
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Prikaz poruke ako postoji u sesiji
$updateMessage = '';
if (isset($_SESSION['updateMessage'])) {
    $updateMessage = $_SESSION['updateMessage'];
    unset($_SESSION['updateMessage']); // Uklanjanje poruke nakon prikaza
}


// Dohvati ID prijavljenog korisnika iz sesije
$userId = $_SESSION['id'];

// Ako je forma poslana, ažuriraj podatke u bazi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $company = $_POST['company'];
    $position = $_POST['position'];
    $city = $_POST['city'];
    $country = $_POST['country'];

    $stmt = $con->prepare("UPDATE users SET firstName = ?, lastName = ?, email = ?, phoneNumber = ?, company = ?, position = ?, city = ?, country = ? WHERE id = ?");
    $stmt->bind_param("ssssssssi", $firstName, $lastName, $email, $phoneNumber, $company, $position, $city, $country, $userId);

    if ($stmt->execute()) {
        $_SESSION['updateMessage'] = "Data successfully changed!";
    } else {
        $_SESSION['updateMessage'] = "Error. Try again.";
    }


    $stmt->close();

    // Preusmjeri natrag na profil kako bi se prikazala poruka
    header('Location: profile.php');
    exit();
}

// Dohvati podatke o korisniku iz baze podataka
$stmt = $con->prepare("SELECT firstName, lastName, email, phoneNumber, company, position, city, country FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
<div class="profile-container">
    <div class="profile-image">
        <!-- Prikaz slike korisnika -->
        <img src="Images/user.png" alt="Profile Picture">
    </div>
    <div class="profile-info">
        <!-- Prikaz informacija o korisniku -->
        <h2><?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?></h2> 
        <form id="editProfileForm" action="profile.php" method="post" style="display:none;">
            <p><strong>First Name:</strong> <input type="text" name="firstName" value="<?php echo htmlspecialchars($user['firstName']); ?>"></p>
            <p><strong>Last Name:</strong> <input type="text" name="lastName" value="<?php echo htmlspecialchars($user['lastName']); ?>"></p>
            <p><strong>Email:</strong> <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"></p>
            <p><strong>Phone Number:</strong> <input type="text" name="phoneNumber" value="<?php echo htmlspecialchars($user['phoneNumber']); ?>"></p>
            <p><strong>Company:</strong> <input type="text" name="company" value="<?php echo htmlspecialchars($user['company']); ?>"></p>
            <p><strong>Position:</strong> <input type="text" name="position" value="<?php echo htmlspecialchars($user['position']); ?>"></p>
            <p><strong>City:</strong> <input type="text" name="city" value="<?php echo htmlspecialchars($user['city']); ?>"></p>
            <p><strong>Country:</strong> <input type="text" name="country" value="<?php echo htmlspecialchars($user['country']); ?>"></p>
            <button type="submit" class="save-button">Save</button>
            <button type="button" onclick="cancelEdit()" class="cancel-button">Cancel</button>
        </form>
        <div id="profileDisplay">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phoneNumber']); ?></p>
            <p><strong>Company:</strong> <?php echo htmlspecialchars($user['company']); ?></p>
            <p><strong>Position:</strong> <?php echo htmlspecialchars($user['position']); ?></p>
            <p><strong>City:</strong> <?php echo htmlspecialchars($user['city']); ?></p>
            <p><strong>Country:</strong> <?php echo htmlspecialchars($user['country']); ?></p>
            <button type="button" onclick="editProfile()" class="edit-button">Edit</button>
        </div>
    </div>
</div>

<button type="button" onclick="window.history.back();" class="back-button">Nazad</button>

<script>
    function editProfile() {
        document.getElementById('editProfileForm').style.display = 'block';
        document.getElementById('profileDisplay').style.display = 'none';
    }

    function cancelEdit() {
        document.getElementById('editProfileForm').style.display = 'none';
        document.getElementById('profileDisplay').style.display = 'block';
    }
</script>

<?php if ($updateMessage): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        alert("<?php echo htmlspecialchars($updateMessage); ?>");
    });
</script>
<?php endif; ?>

</body>
</html>
