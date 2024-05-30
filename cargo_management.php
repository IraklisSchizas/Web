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
    // Έλεγχος αν πατήθηκε το κουμπί φόρτωσης ή εκφόρτωσης
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['load'])) {
            loadItems();
        } elseif (isset($_POST['unload'])) {
            unloadItems();
        }
    }

    function loadItems() {
        // Προσθήκη κώδικα για φόρτωση αντικειμένων
        // Εδώ θα πρέπει να γίνει η λογική για την προσθήκη των αντικειμένων από τη βάση
        echo "Items loaded successfully.";
    }

    function unloadItems() {
        // Προσθήκη κώδικα για εκφόρτωση αντικειμένων
        // Εδώ θα πρέπει να γίνει η λογική για την αφαίρεση των αντικειμένων από το φορτίο του διασώστη και την προσθήκη τους στη βάση
        echo "Items unloaded successfully.";
    }
    ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="load_items">Load Items:</label>
        <input type="submit" name="load" value="Load">

        <br><br>

        <label for="unload_items">Unload Items:</label>
        <input type="submit" name="unload" value="Unload">
    </form>
</body>
</html>
