<?php
session_start();

// Σύνδεση στη βάση δεδομένων
include 'config.php';

if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

$user_name = $_SESSION['user_name'];

// Κώδικας για την επιλογή φορτίου από τη βάση
if (isset($_POST['load_items'])) {
    // Προσθήκη κώδικα για επεξεργασία της φόρτωσης εδώ
}

// Κώδικας για την εκφόρτωση φορτίου στη βάση
if (isset($_POST['unload_items'])) {
    // Προσθήκη κώδικα για επεξεργασία της εκφόρτωσης εδώ
}

// Κώδικας επιλογής δεδομένων οχημάτων, αιτημάτων, προσφορών κλπ.

?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Διαχείριση Φορτίου</title>
</head>
<body>
    <h2>Διαχείριση Φορτίου</h2>
    <!-- Φόρμα για φόρτωση εμπορευμάτων -->
    <form action="" method="POST">
        <label for="load_items">Φόρτωση Εμπορευμάτων:</label>
        <!-- Εδώ μπορείτε να προσθέσετε λογική για την επιλογή εμπορευμάτων από τη βάση -->
        <input type="submit" name="load_items" value="Φόρτωση">
    </form>
    <!-- Φόρμα για εκφόρτωση εμπορευμάτων -->
    <form action="" method="POST">
        <label for="unload_items">Εκφόρτωση Εμπορευμάτων:</label>
        <!-- Εδώ μπορείτε να προσθέσετε λογική για την εκφόρτωση εμπορευμάτων προς τη βάση -->
        <input type="submit" name="unload_items" value="Εκφόρτωση">
    </form>
</body>
</html>
