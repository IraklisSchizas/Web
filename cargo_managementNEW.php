<?php
session_start();

// Σύνδεση στη βάση δεδομένων
include 'config.php';

if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

$user_name = $_SESSION['user_name'];
$distance_from_base = 50; // Υποθετική απόσταση από τη βάση

// Κώδικας για την επιλογή φορτίου από τη βάση
if (isset($_POST['load_items'])) {
    if ($distance_from_base <= 100) {
        loadItemsFromDatabase();
    } else {
        echo "Δεν μπορείτε να φορτώσετε αντικείμενα. Ο διασώστης είναι εκτός εμβέλειας της βάσης.";
    }
}

// Κώδικας για την εκφόρτωση φορτίου στη βάση
if (isset($_POST['unload_items'])) {
    if ($distance_from_base <= 100) {
        unloadItemsToDatabase();
    } else {
        echo "Δεν μπορείτε να εκφορτώσετε αντικείμενα. Ο διασώστης είναι εκτός εμβέλειας της βάσης.";
    }
}

// Κώδικας επιλογής δεδομένων οχημάτων, αιτημάτων, προσφορών κλπ.

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Διαχείριση Φορτίου</title>
    <link rel="stylesheet" href="css/style.css">
   
    

</head>
<body>
    <div class="form-container">
        <form id="initialize_form" action="" method="post">
            <p><a href="rescuer_page.php">Πίσω στη σελίδα Διασώστη</a></p>
            <br><br>            
            <h2>Cargo Management</h2><br>


<?php
    function loadItemsFromDatabase() {
           global $conn;

    // Επιλέγουμε τα αντικείμενα προς φόρτωση από τη βάση
    $result = mysqli_query($conn, "SELECT * FROM items");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Εδώ μπορείτε να προσθέσετε τη λογική για τον έλεγχο διαθεσιμότητας των αντικειμένων
            // και την προσθήκη τους στο φορτίο του διασώστη (cargo)
            $item_id = $row['id'];
            $item_name = $row['name'];
            $item_quantity = $row['quantity'];
            // Προσθήκη κώδικα για την προσθήκη των αντικειμένων στο φορτίο (cargo) του διασώστη
            // Π.χ. ενημέρωση του πίνακα cargo με τα αντικείμενα που θα φορτωθούν
            echo "Τα αντικείμενα φορτώθηκαν επιτυχώς.";
        }
    }
}

function unloadItemsToDatabase() {
    global $conn;

    // Προσθήκη κώδικα για την εκφόρτωση των αντικειμένων από το φορτίο του διασώστη και την ενημέρωση της βάση
}