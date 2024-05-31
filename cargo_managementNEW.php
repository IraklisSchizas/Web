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
$base_result = $base_query->get_result();
$base_row = $base_result->fetch_assoc();
$base_latitude = $base_row['latitude'];
$base_longitude = $base_row['longitude'];

// SQL ερώτημα για την τοποθεσία του χρήστη
$user_query = $conn->prepare("SELECT latitude, longitude FROM users WHERE username = '$user_name'");
$user_query->execute();
$user_result = $user_query->get_result();
$user_row = $user_result->fetch_assoc();
$user_latitude = $user_row['latitude'];
$user_longitude = $user_row['longitude'];

$distance_from_base = sqrt(pow($base_latitude-$user_latitude,2)+pow($base_latitude-$user_longitude,2));
// Έλεγχος αν πατήθηκε το κουμπί φόρτωσης ή εκφόρτωσης
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Κώδικας για την επιλογή φορτίου από τη βάση
    if (isset($_POST['load_items']) && !isset($_POST['unload_items'])) {
        if ($distance_from_base <= 100) {
            loadItems();
        } else {
            echo "Δεν μπορείτε να φορτώσετε αντικείμενα. Ο διασώστης είναι εκτός εμβέλειας της βάσης.";
        }
    }

    // Κώδικας για την εκφόρτωση φορτίου στη βάση
    if (isset($_POST['unload_items']) && !isset($_POST['load_items'])) {
        if ($distance_from_base <= 100) {
            unloadItems();
        } else {
            echo "Δεν μπορείτε να εκφορτώσετε αντικείμενα. Ο διασώστης είναι εκτός εμβέλειας της βάσης.";
        }
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
            <label for="item">Select Item:</label>
            <select name="item" id="item">
                <?php
                    $items_query = mysqli_query($conn, "SELECT * FROM items WHERE quantity > 0");
                    if ($items_query) {
                        while ($item_row = mysqli_fetch_assoc($items_query)) {
                            echo "<option value='" . $item_row['id'] . "'>" . $item_row['name'] . "</option>";
                        }
                    }
                    ?>
            </select>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="1" required>
            <input type="submit" name="load_items" value="Load">

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
                
                    // Λάβετε το επιλεγμένο αντικείμενο και την ποσότητα από τη φόρμα
                    $selected_item_id = $_POST['item'];
                    $selected_quantity = $_POST['quantity'];
                
                    // Επιλέξτε το επιλεγμένο αντικείμενο από τον πίνακα "items"
                    $item_query = mysqli_query($conn, "SELECT * FROM items WHERE id = '$selected_item_id' AND quantity >= $selected_quantity");
                
                    if ($item_query) {
                        $item_row = mysqli_fetch_assoc($item_query);
                        $item_id = $item_row['id'];
                        $item_quantity = $selected_quantity;
                
                        // Εισάγετε το επιλεγμένο αντικείμενο και την ποσότητά του στον πίνακα "cargo"
                        $insert_query = "INSERT INTO cargo (item_ids, quantity) VALUES ('$item_id', '$item_quantity')";
                        $insert_result = mysqli_query($conn, $insert_query);
                
                        if ($insert_result) {
                            // Ενημερώστε την ποσότητα του επιλεγμένου αντικειμένου στον πίνακα "items"
                            $new_quantity = $item_row['quantity'] - $item_quantity;
                            $update_query = "UPDATE items SET quantity = '$new_quantity' WHERE id = '$item_id'";
                            $update_result = mysqli_query($conn, $update_query);
                
                            if ($update_result) {
                                echo "Το αντικείμενο φορτώθηκε με επιτυχία.";
                            } else {
                                echo "Σφάλμα κατά την ενημέρωση της ποσότητας του αντικειμένου.";
                            }
                        } else {
                            echo "Σφάλμα κατά την φόρτωση του αντικειμένου.";
                        }
                    } else {
                        echo "Το αντικείμενο δεν είναι διαθέσιμο ή η ποσότητα δεν επαρκεί.";
                    }
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
                    
                            foreach($row as $r){
                                // Ενημέρωση της ποσότητας του αντικειμένου στον πίνακα items
                                $restore_items_query = "UPDATE items SET quantity = '{$r['quantity']}' ";
                                $restore_items_result = mysqli_query($conn, $restore_items_query);

                                if (!$restore_items_result) {
                                    echo "Σφάλμα κατά τη μεταφορά των αντικειμένων πίσω στον πίνακα Items.";
                                    return;
                                }
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

                <label for="unload_item">Select Item to Unload:</label>
                <select name="unload_item" id="unload_item">
                    <?php
                    $cargo_query = mysqli_query($conn, "SELECT * FROM cargo");
                    if ($cargo_query) {
                        while ($cargo_row = mysqli_fetch_assoc($cargo_query)) {
                            $item_id = $cargo_row['item_ids'];
                            $item_name_query = mysqli_query($conn, "SELECT name FROM items WHERE id = '$item_id'");
                            $item_name = mysqli_fetch_assoc($item_name_query)['name'];
                            echo "<option value='" . $item_id . "'>" . $item_name . "</option>";
                        }
                    }
                    ?>
                </select>

                <label for="unload_quantity">Quantity to Unload:</label>
                <input type="number" id="unload_quantity" name="unload_quantity" min="0" required>

                <input type="submit" name="unload_items" value="Unload">

            </form>
        </form>
    </div>
</body>
</html>