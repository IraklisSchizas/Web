<?php
session_start();

// Σύνδεση στη βάση δεδομένων
include 'config.php';

if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

$user_name = $_SESSION['user_name'];

// SQL ερώτημα για την τοποθεσία της βάσης
$base_query = $conn->prepare("SELECT latitude, longitude FROM users WHERE username = 'admin'");
$base_query->execute();
$base_result = $user_query->get_result();
$base_row = $user_result->fetch_assoc();
$base_latitude = $user_row['latitude'];
$base_longitude = $user_row['longitude'];

// SQL ερώτημα για την τοποθεσία του χρήστη
$user_query = $conn->prepare("SELECT latitude, longitude FROM users WHERE username = '$user_name'");
$user_query->execute();
$user_result = $user_query->get_result();
$user_row = $user_result->fetch_assoc();
$user_latitude = $user_row['latitude'];
$user_longitude = $user_row['longitude'];

$distance_from_base = sqrt(pow($base_latitude-$user_latitude,2)+pow($base_latitude-$user_longitude,2));

// Κώδικας για την επιλογή φορτίου από τη βάση
if (isset($_POST['load_items'])) {
    if ($distance_from_base <= 100) {
        loadItems();
    } else {
        echo "Δεν μπορείτε να φορτώσετε αντικείμενα. Ο διασώστης είναι εκτός εμβέλειας της βάσης.";
    }
}

// Κώδικας για την εκφόρτωση φορτίου στη βάση
if (isset($_POST['unload_items'])) {
    if ($distance_from_base <= 100) {
        unloadItems();
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
    // Έλεγχος αν πατήθηκε το κουμπί φόρτωσης ή εκφόρτωσης
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['load_items']) && $distance_from_base <= 100) {
            loadItems();
        } elseif (isset($_POST['unload_items']) && $distance_from_base <= 100) { 
            unloadItems();
        }
    }

function loadItems() {
    global $conn;

    // Επιλέγουμε τα αντικείμενα προς φόρτωση από τη βάση
    $result = mysqli_query($conn, "SELECT * FROM items WHERE quantity > 0 ");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $item_id = $row['id'];
            $item_name = $row['name'];
            $item_quantity = $row['quantity'];

            // Προσθήκη των αντικειμένων και της ποσότητάς τους στον πίνακα "cargo"
            $insert_query = "INSERT INTO cargo (item_ids, quantity) VALUES ('$item_id', '$item_quantity')";
            $insert_result = mysqli_query($conn, $insert_query);

            if ($insert_result) {
                // Αν η εισαγωγή στον πίνακα "cargo" είναι επιτυχής, τότε διαγράψτε τα αντικείμενα από τον πίνακα "Items"
                $delete_query = "DELETE FROM items WHERE id = '$item_id'";
                $delete_result = mysqli_query($conn, $delete_query);

                if ($delete_result) {
                    echo "Items loaded successfully.";
                } else {
                    echo "Items were not loaded";
                }
             }
        }
    }
    echo "Items loaded successfully.";
}


function unloadItems() {
    global $conn;

    // Επιλέγουμε τα αντικείμενα που εκφορτώνονται από τον πίνακα cargo
    $cargo_query = "SELECT * FROM cargo";
    $cargo_result = mysqli_query($conn, $cargo_query);

    if ($cargo_result) {
        while ($row = mysqli_fetch_assoc($cargo_result)) {
            $item_id = $row['item_ids'];
            $quantity = $row['quantity'];

            // Ενημέρωση της ποσότητας του αντικειμένου στον πίνακα items
            $restore_items_query = "INSERT INTO items (id, quantity) SELECT item_ids, quantity FROM cargo";
            $restore_items_result = mysqli_query($conn, $restore_items_query);

            if (!$restore_items_result) {
                echo "Σφάλμα κατά τη μεταφορά των αντικειμένων πίσω στον πίνακα Items.";
                return;
            }
        }

        // Αφαίρεση όλων των αντικειμένων από το φορτίο του διασώστη
        $clear_cargo_query = "DELETE FROM cargo";
        $clear_cargo_result = mysqli_query($conn, $clear_cargo_query);

        // Έλεγχος επιτυχίας της διαγραφής του φορτίου
        if ($clear_cargo_result) {
            echo "Items unloaded successfully.";
        } else {
            echo "Error during unlooad";
        }
    } else {
        echo "Δεν υπάρχουν αντικείμενα στο φορτίο για εκφόρτωση.";
    }
    
}
?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="load_items">Load Items:</label>
    <input type="submit" name="load_items" value="Load">

    <br><br>

    <label for="unload_items">Unload Items:</label>
    <input type="submit" name="unload_items" value="Unload">
</form>

