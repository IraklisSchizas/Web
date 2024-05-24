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
    <title>Ανακοινώσεις</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <h3>Ανακοινώσεις</h3>
        <table class="table" id="announcementsTable">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Τίτλος</th>
                    <th scope="col">Λεπτομέρειες</th>
                    <th scope="col">Ημερομηνία</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->prepare("SELECT id, title, details, date FROM announcements");
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>
                            <th scope="row">' . htmlspecialchars($row['id']) . '</th>
                            <td>' . htmlspecialchars($row['title']) . '</td>
                            <td>' . htmlspecialchars($row['details']) . '</td>
                            <td>' . htmlspecialchars($row['date']) . '</td>
                        </tr>';
                    }
                } else {
                    echo "<tr><td colspan='4'>Δεν βρέθηκαν ανακοινώσεις</td></tr>";
                }

                $stmt->close();
                $conn->close();
                ?>
            </tbody>
        </table>
        <p><a href="civilian_page.php">Πίσω στην αρχική σελίδα</a></p>
    </div>
</body>
</html>
