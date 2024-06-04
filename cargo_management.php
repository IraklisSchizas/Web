<?php
session_start();

// Σύνδεση στη βάση δεδομένων
include 'config.php';

if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}
$user_name = $_SESSION['user_name'];

// Λήψη του user_id του χρήστη
$user_query = $conn->prepare("SELECT id FROM users WHERE username = ?");
$user_query->bind_param('s', $user_name);
$user_query->execute();
$user_result = $user_query->get_result();
$user_row = $user_result->fetch_assoc();
$user_id = $user_row['id'];

// SQL ερώτημα για την τοποθεσία της βάσης
$base_query = $conn->prepare("SELECT latitude, longitude FROM users WHERE username = 'admin'");
$base_query->execute();
$base_result = $base_query->get_result();
$base_row = $base_result->fetch_assoc();
$base_latitude = $base_row['latitude'];
$base_longitude = $base_row['longitude'];

// SQL ερώτημα για την τοποθεσία του χρήστη
$user_query = $conn->prepare("SELECT latitude, longitude FROM users WHERE username = ?");
$user_query->bind_param('s', $user_name);
$user_query->execute();
$user_result = $user_query->get_result();
$user_row = $user_result->fetch_assoc();
$user_latitude = $user_row['latitude'];
$user_longitude = $user_row['longitude'];

$distance_from_base = sqrt(pow($base_latitude - $user_latitude, 2) + pow($base_longitude - $user_longitude, 2));

// Έλεγχος αν πατήθηκε το κουμπί φόρτωσης ή εκφόρτωσης
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($distance_from_base <= 100) {
        if (isset($_POST['load_items']) && !isset($_POST['unload_items'])) {
            loadItems($user_id);
        } elseif (isset($_POST['unload_items']) && !isset($_POST['load_items'])) {
            unloadItems($user_id);
        }
    } else {
        echo "Ο διασώστης είναι εκτός εμβέλειας της βάσης.";
    }
}

function loadItems($rescuer_id) {
    global $conn;

    $selected_item_id = $_POST['item'];
    $selected_quantity = $_POST['quantity'];

    $item_query = $conn->prepare("SELECT * FROM items WHERE id = ? AND quantity >= ?");
    $item_query->bind_param('ii', $selected_item_id, $selected_quantity);
    $item_query->execute();
    $item_result = $item_query->get_result();

    if ($item_result->num_rows > 0) {
        $item_row = $item_result->fetch_assoc();
        $new_quantity = $item_row['quantity'] - $selected_quantity;

        $update_item_query = $conn->prepare("UPDATE items SET quantity = ? WHERE id = ?");
        $update_item_query->bind_param('ii', $new_quantity, $selected_item_id);
        $update_item_query->execute();

        $cargo_query = $conn->prepare("SELECT * FROM cargo WHERE rescuer_id = ? AND item_ids = ?");
        $cargo_query->bind_param('ii', $rescuer_id, $selected_item_id);
        $cargo_query->execute();
        $cargo_result = $cargo_query->get_result();

        if ($cargo_result->num_rows > 0) {
            $cargo_row = $cargo_result->fetch_assoc();
            $new_cargo_quantity = $cargo_row['quantity'] + $selected_quantity;

            $update_cargo_query = $conn->prepare("UPDATE cargo SET quantity = ? WHERE rescuer_id = ? AND item_ids = ?");
            $update_cargo_query->bind_param('iii', $new_cargo_quantity, $rescuer_id, $selected_item_id);
            $update_cargo_query->execute();
        } else {
            $insert_cargo_query = $conn->prepare("INSERT INTO cargo (rescuer_id, item_ids, quantity) VALUES (?, ?, ?)");
            $insert_cargo_query->bind_param('iii', $rescuer_id, $selected_item_id, $selected_quantity);
            $insert_cargo_query->execute();
        }

        echo "Το αντικείμενο φορτώθηκε με επιτυχία.";
    } else {
        echo "Το αντικείμενο δεν είναι διαθέσιμο ή η ποσότητα δεν επαρκεί.";
    }
}

function unloadItems($rescuer_id) {
    global $conn;

    $selected_item_id = $_POST['unload_item'];
    $selected_quantity = $_POST['unload_quantity'];

    $cargo_query = $conn->prepare("SELECT * FROM cargo WHERE rescuer_id = ? AND item_ids = ?");
    $cargo_query->bind_param('ii', $rescuer_id, $selected_item_id);
    $cargo_query->execute();
    $cargo_result = $cargo_query->get_result();

    if ($cargo_result->num_rows > 0) {
        $cargo_row = $cargo_result->fetch_assoc();
        $current_quantity = $cargo_row['quantity'];

        if ($current_quantity >= $selected_quantity) {
            $new_cargo_quantity = $current_quantity - $selected_quantity;

            if ($new_cargo_quantity == 0) {
                $delete_cargo_query = $conn->prepare("DELETE FROM cargo WHERE rescuer_id = ? AND item_ids = ?");
                $delete_cargo_query->bind_param('ii', $rescuer_id, $selected_item_id);
                $delete_cargo_query->execute();
            } else {
                $update_cargo_query = $conn->prepare("UPDATE cargo SET quantity = ? WHERE rescuer_id = ? AND item_ids = ?");
                $update_cargo_query->bind_param('iii', $new_cargo_quantity, $rescuer_id, $selected_item_id);
                $update_cargo_query->execute();
            }

            $item_query = $conn->prepare("SELECT quantity FROM items WHERE id = ?");
            $item_query->bind_param('i', $selected_item_id);
            $item_query->execute();
            $item_result = $item_query->get_result();
            $item_row = $item_result->fetch_assoc();

            $new_item_quantity = $item_row['quantity'] + $selected_quantity;

            $update_item_query = $conn->prepare("UPDATE items SET quantity = ? WHERE id = ?");
            $update_item_query->bind_param('ii', $new_item_quantity, $selected_item_id);
            $update_item_query->execute();

            echo "Το αντικείμενο εκφορτώθηκε με επιτυχία.";
        } else {
            echo "Η ποσότητα προς εκφόρτωση είναι μεγαλύτερη από την διαθέσιμη.";
        }
    } else {
        echo "Το αντικείμενο δεν υπάρχει στο φορτίο.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Διαχείριση Φορτίου</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        html {
            background: #efefef; /* Light grey */
        }
        .container {
            display: flex;
            justify-content: space-between;
        }
        .form-container {
            width: 48%;
        }
        .table-container {
            margin-top: -20px;
            margin-left: 40px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            padding: 0.625rem;
            border: 0.0625rem solid #ddd;
            text-align: left;
        }
        th, td {
            text-align: left;
            padding: 0.625rem;
            border: 0.0625rem solid #ddd;
            text-align: left;
        }
        .table-container h2 {
            text-align: center;
            padding-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <p><a href="rescuer_page.php">Πίσω στη σελίδα Διασώστη</a></p><br>
            <h2>Load Items</h2><br>
            <form id="load_form" action="" method="post">
                <label for="item">Select Item to Load:</label>
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

                <label for="quantity">Quantity to Load:</label>
                <input type="number" id="quantity" name="quantity" min="1" required>
                <input type="submit" name="load_items" value="Load">
            </form>
        </div>

        <div class="form-container">
            <h2>Unload Items</h2>
            <form id="unload_form" action="" method="post">
                <label for="unload_item">Select Item to Unload:</label>
                <select name="unload_item" id="unload_item">
                    <?php
                        $cargo_query = mysqli_query($conn, "SELECT * FROM cargo WHERE rescuer_id = $user_id");
                        if ($cargo_query) {
                            while ($cargo_row = mysqli_fetch_assoc($cargo_query)) {
                                $item_ids = explode(',', $cargo_row['item_ids']);
                                foreach ($item_ids as $item_id) {
                                    $item_name_query = mysqli_query($conn, "SELECT name FROM items WHERE id = '$item_id'");
                                    $item_name = mysqli_fetch_assoc($item_name_query)['name'];
                                    echo "<option value='" . $item_id . "'>" . $item_name . "</option>";
                                }
                            }
                        }
                    ?>
                </select>

                <label for="unload_quantity">Quantity to Unload:</label>
                <input type="number" id="unload_quantity" name="unload_quantity" min="1" required>
                <input type="submit" name="unload_items" value="Unload">
            </form>
        </div>
    </div>

    <div class="table-container">
        <form>
            <h2>Items in Cargo</h2>
            <table>
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $cargo_query = mysqli_query($conn, "SELECT * FROM cargo WHERE rescuer_id = $user_id");
                        if ($cargo_query) {
                            while ($cargo_row = mysqli_fetch_assoc($cargo_query)) {
                                $item_ids = explode(',', $cargo_row['item_ids']);
                                foreach ($item_ids as $item_id) {
                                    $item_name_query = mysqli_query($conn, "SELECT name FROM items WHERE id = '$item_id'");
                                    $item_name = mysqli_fetch_assoc($item_name_query)['name'];
                                    echo "<tr>";
                                    echo "<td>" . $item_id . "</td>";
                                    echo "<td>" . $item_name . "</td>";
                                    echo "<td>" . $cargo_row['quantity'] . "</td>";
                                    echo "</tr>";
                                }
                            }
                        }
                    ?>
                </tbody>
            </table><br><br><br>
        </form>
    </div>
</body>
</html>
