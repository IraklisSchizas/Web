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
            <input type="hidden" name="initialize" value="true">
            <input type="button" id="j_button" class="form-btn" onclick="window.location.href = 'create_announcement.php'" value="Δημιουργία Ανακοίνωσης"><br>
            <p><a href="admin_page.php">Πίσω στη σελίδα Διαχειριστή</a></p>
            <br><br>            
            <h2>Announcements</h2><br>
            <table class="table" id="announcementsTable">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Title</th>
                        <th scope="col">Details</th>
                        <th scope="col">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM announcements");

                    // Έλεγχος αν υπάρχουν αποτελέσματα και εμφάνιση τους
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>
                                <th scope="row">'.$row['id'].'</th>
                                <td>'.$row['title'].'</td>
                                <td>'.$row['details'].'</td>
                                <td>'.$row['date'].'</td>
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