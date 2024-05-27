<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

@include 'config.php';
session_start();
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

$civilian_name = $_SESSION['user_name'];
// Χρήση προετοιμασμένων δηλώσεων για ασφάλεια
$stmt = $conn->prepare("SELECT * FROM users WHERE name = ?");
$stmt->bind_param("s", $civilian_name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$civilian_id = $row['id'];
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
                $stmt = $conn->prepare("SELECT id, title, details, date, item_ids FROM announcements");
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $announcement_id = $row['id'];
                        // Μετατροπή των item_ids σε item_names
                        $item_ids = explode(',', $row['item_ids']);
                        $item_names = [];
                        foreach ($item_ids as $item_id) {
                            $item_stmt = $conn->prepare("SELECT name FROM items WHERE id = ?");
                            $item_stmt->bind_param("i", $item_id);
                            $item_stmt->execute();
                            $item_result = $item_stmt->get_result();
                            if ($item_row = $item_result->fetch_assoc()) {
                                $item_names[] = $item_row['name'];
                            }
                        }
                        $item_names_str = implode(', ', $item_names);
                        
                        echo '<tr>
                            <th scope="row">' . htmlspecialchars($row['title']) . '</th>
                            <td>' . htmlspecialchars($row['details']) . '</td>
                            <td>' . htmlspecialchars($row['date']) . '</td>
                            <td>' . htmlspecialchars($item_names_str) . '</td>
                            <td>
                                <button class="update-btn form-btn"><a href="add_offer_or_request.php?is_a=offer&announcement_id=' . $announcement_id . '">Προσφορά</a></button>
                            </td>
                        </tr>';
                    }
                } else {
                    echo "<tr><td colspan='5'>Δεν βρέθηκαν ανακοινώσεις</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <br><br>
        <form id="initialize_form" action="" method="post">
            <input type="hidden" name="initialize" value="true">
            <br><br>
            <h2>Οι προσφορές μου</h2><br>
            <table class="table" id="offersTable">
                <thead>
                    <tr>
                        <th scope="col">Item</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Χρήση προετοιμασμένων δηλώσεων για ασφάλεια
                    $stmt = $conn->prepare("SELECT * FROM offers WHERE civilian_id = ?");
                    $stmt->bind_param("i", $civilian_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>
                                <th scope="row">'.$row['item_id'].'</th>
                                <td>'.$row['quantity'].'</td>
                                <td>'.$row['date'].'</td>
                            </tr>';
                        }
                    }
                    $stmt->close();
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </form>
    </div>
</body>
</html>
