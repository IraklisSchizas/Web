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
    <div class="form-container">
        <form id="initialize_form" action="" method="post">
            <br>
            <p><a href="admin_page.php">Πίσω στη σελίδα Διαχειριστή</a></p><br><br>
            <h2>Αντικείμενα</h2><br>
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