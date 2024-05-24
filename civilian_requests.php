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
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!--<?php if (isset($_GET['initialized']) && $_GET['initialized'] == 'true'): ?>
        <div class="alert alert-success">Η αρχικοποίηση έγινε με επιτυχία!</div>
    <?php endif; ?>-->

    <div class="form-container">
        <form id="initialize_form" action="" method="post">
            <input type="hidden" name="initialize" value="true">
            <input type="button" id="j_button" class="form-btn" onclick="window.location.href = 'add_offer.php'" value="Δημιουργία Προσφοράς"><br>
            <input type="button" id="j_button" class="form-btn" onclick="window.location.href = 'add_request.php'" value="Δημιουργία Αιτήματος"><br>
            <p><a href="civilian_page.php">Πίσω στη σελίδα Πολίτη</a></p><br>
            <br><br>
            <h2>Items Table</h2><br>
            <table class="table" id="jsonItemsTable">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Category</th>
                        <th scope="col">Details</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM items");
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
                                <td>'.$row['category'].'</td>
                                <td>'.$details_formatted.'</td>
                                <td>'.$row['quantity'].'</td>
                                <td>
                                <button class="update-btn form-btn"><a href="update.php?updateid='.$row['id'].'&is_a=item">Ενημέρωση</a></button>
                                <button class="delete-btn form-btn"><a href="delete.php?deleteid='.$row['id'].'&is_a=item">Διαγραφή</a></button>
                                </td>
                            </tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </form>
    </div>
</body>
</html>