<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

@include 'config.php';
session_start();
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Σελίδα Διαχειριστή</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <form id="initialize_form" action="" method="post">
            <br>
            <p><a href="admin_page.php">Πίσω στη σελίδα Διαχειριστή</a></p><br><br>
            <!-- Dropdown για επιλογή κατηγορίας με το Select2 -->
            <label for="category"><h2>Κατηγορίες:</h2></label>
            <select name="category[]" id="category" multiple style="width: 100%;">
                <option value="">Όλες</option>
                <?php
                // Ανάκτηση μοναδικών κατηγοριών από τη βάση δεδομένων
                $categories_result = mysqli_query($conn, "SELECT * FROM categories");
                while ($category_row = mysqli_fetch_assoc($categories_result)) {
                    echo '<option value="'.$category_row['id'].'">'.$category_row['name'].'</option>';
                }
                ?>
            </select>
            <button type="submit">Αναζήτηση</button>
            <br><br>
            <h2>Αντικείμενα</h2><br>
            <br>
            <table class="table" id="jsonItemsTable">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Όνομα</th>
                        <th scope="col">Κατηγορία</th>
                        <th scope="col">Λεπτομέρειες</th>
                        <th scope="col">Ποσότητα</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ανάκτηση της επιλεγμένης κατηγορίας από τη φόρμα
                    $selected_categories_str = isset($_POST['category']) ? $_POST['category'] : array();
                    if (!empty($selected_categories_str)) {
                        if (!is_array($selected_categories_str)) {
                            $selected_categories_str = array($selected_categories_str);
                        }
                        $selected_categories = array_map('intval', $selected_categories_str);
                        $selected_categories_str = implode(",", $selected_categories);
                    }

                    // Δημιουργία του SQL query με join για να πάρουμε το όνομα της κατηγορίας
                    if (!empty($selected_categories_str)) {
                        $query = "SELECT items.*, categories.name as category_name 
                                  FROM items 
                                  JOIN categories ON items.category = categories.id 
                                  WHERE items.category IN ($selected_categories_str)";
                    } else {
                        $query = "SELECT items.*, categories.name as category_name 
                                  FROM items 
                                  JOIN categories ON items.category = categories.id";
                    }

                    $result = mysqli_query($conn, $query);
                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $details_array = json_decode($row['details'], true);
                            $details_formatted = "";
                            foreach ($details_array as $detail) {
                                $details_formatted .= ucfirst($detail['detail_name']) . ': ' . $detail['detail_value'] . '<br>';
                            }
                            echo '<tr>
                                <th scope="row">'.$row['id'].'</th>
                                <td>'.$row['name'].'</td>
                                <td>'.$row['category_name'].'</td>
                                <td>'.$details_formatted.'</td>
                                <td>'.$row['quantity'].'</td>
                            </tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </form>
    </div>
    <!-- Συμπερίληψη του jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Συμπερίληψη του Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!-- Ενεργοποίηση του Select2 -->
    <script>
    $(document).ready(function() {
        $('#category').select2();
    });
    </script>
</body>
</html>