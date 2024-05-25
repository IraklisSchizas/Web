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
    <title>Ανακοινώσεις</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
    <form id="initialize_form" action="" method="post">
    <p><a href="civilian_page.php">Πίσω στην αρχική σελίδα</a></p>
    <br><br>
        <h2>Ανακοινώσεις</h2><br>
        <table class="table" id="announcementsTable">
            <thead>
                <tr>
                    <th scope="col">Τίτλος</th>
                    <th scope="col">Λεπτομέρειες</th>
                    <th scope="col">Ημερομηνία</th>
                    <th scope="col">Αντικείμενα</th>
                    <th scope="col">Ενέργειες</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->prepare("SELECT title, details, date, item_ids FROM announcements");
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Αντικατάσταση των item_ids με τα item_names
                        $item_ids = explode(',', $row['item_ids']);
                        $item_names = array_map(function($id) use ($items) {
                            return $items[$id];
                        }, $item_ids);
                        $item_names_str = implode(', ', $item_names);

                        echo '<tr>
                            <th scope="row">' . htmlspecialchars($row['title']) . '</th>
                            <td>' . htmlspecialchars($row['details']) . '</td>
                            <td>' . htmlspecialchars($row['date']) . '</td>
                            <td>' . htmlspecialchars($item_names_str) . '</td>
                            <td>
                                <button class="update-btn form-btn"><a href="add_offer_or_request.php?is_a=offer">Προσφορά</a></button>
                            </td>
                        </tr>';
                    }
                } else {
                    echo "<tr><td colspan='5'>Δεν βρέθηκαν ανακοινώσεις</td></tr>";
                }
                $stmt->close();
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
