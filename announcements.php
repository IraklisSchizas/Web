<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

@include 'config.php';
session_start();
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

// Λήψη των αντικειμένων και των ονομάτων τους σε έναν πίνακα
$items = [];
$item_result = $conn->query("SELECT id, name FROM items");
while ($item_row = $item_result->fetch_assoc()) {
    $items[$item_row['id']] = $item_row['name'];
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
            <input type="hidden" name="initialize" value="true">
            <input type="button" id="j_button" class="form-btn" onclick="window.location.href = 'create_announcement.php'" value="Δημιουργία Ανακοίνωσης"><br>
            <p><a href="admin_page.php">Πίσω στη σελίδα Διαχειριστή</a></p>
            <br><br>            
            <h2>Ανακοινώσεις</h2><br>
            <table class="table" id="announcementsTable">
                <thead>
                    <tr>
                        <th scope="col">Τίτλος</th>
                        <th scope="col">Λεπτομέρειες</th>
                        <th scope="col">Ημερομηνία</th>
                        <th scope="col">Αντικείμενα</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM announcements");

                    // Έλεγχος αν υπάρχουν αποτελέσματα και εμφάνιση τους
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Αντικατάσταση των item_ids με τα item_names
                            $item_ids = explode(',', $row['item_ids']);
                            $item_names = array_map(function($id) use ($items) {
                                return $items[$id];
                            }, $item_ids);
                            $item_names_str = implode(', ', $item_names);

                            echo '<tr>
                                <th scope="row">'.$row['title'].'</th>
                                <td>'.$row['details'].'</td>
                                <td>'.$row['date'].'</td>
                                <td>'.$item_names_str.'</td>
                            </tr>';
                        }
                    } else {
                        echo "<tr><td colspan='4'>No announcements found</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </form>
    </div>
</body>
</html>
