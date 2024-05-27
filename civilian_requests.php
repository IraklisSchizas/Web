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
    <title>Σελίδα Διαχειριστή</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <form id="initialize_form" action="" method="post">
            <input type="hidden" name="initialize" value="true">
            <input type="button" id="j_button" class="form-btn" onclick="window.location.href = 'add_offer_or_request.php?is_a=request'" value="Δημιουργία Αιτήματος"><br>
            <p><a href="civilian_page.php">Πίσω στη σελίδα Πολίτη</a></p><br>
            <br><br>
            <h2>Τα αιτήματά μου</h2><br>
            <table class="table" id="requestsTable">
                <thead>
                    <tr>
                        <th scope="col">Αντικείμενα</th>
                        <th scope="col">Ποσότητα</th>
                        <th scope="col">Ημερομηνία</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Χρήση προετοιμασμένων δηλώσεων για ασφάλεια
                    $stmt = $conn->prepare("SELECT * FROM requests WHERE civilian_id = ?");
                    $stmt->bind_param("i", $civilian_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            // Διαχωρίζουμε τα ids των αντικειμένων και φτιάχνουμε μια λίστα με τα ονόματα
                            $itemIds = explode(',', $row['item_id']);
                            $itemNames = [];
                            foreach ($itemIds as $itemId) {
                                $itemStmt = $conn->prepare("SELECT name FROM items WHERE id = ?");
                                $itemStmt->bind_param("i", $itemId);
                                $itemStmt->execute();
                                $itemResult = $itemStmt->get_result();
                                $itemRow = $itemResult->fetch_assoc();
                                $itemNames[] = $itemRow['name'];
                            }
                            $itemNamesString = implode(', ', $itemNames);
                            echo '<tr>
                                <th scope="row">'.$itemNamesString.'</th>
                                <td>'.$row['quantity'].'</td>
                                <td>'.$row['date'].'</td>
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