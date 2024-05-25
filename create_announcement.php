<?php

@include 'config.php';

session_start();
// Redirect to login page if not logged in
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
}

class AnnouncementManager {
    private $conn;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create announcement
    public function createAnnouncement($title, $details, $item_ids) {
        $title = mysqli_real_escape_string($this->conn, $title);
        $details = mysqli_real_escape_string($this->conn, $details);
        $date = date("Y-m-d H:i:s");
        $item_ids = mysqli_real_escape_string($this->conn, implode(',', $item_ids));

        if (empty($title) || empty($details)) {
            return 'Όλα τα πεδία είναι υποχρεωτικά.';
        } else {
            $insert = "INSERT INTO announcements (title, details, date, item_ids) VALUES ('$title', '$details', '$date', '$item_ids')";
            if (mysqli_query($this->conn, $insert)) {
                return 'Η ανακοίνωση δημιουργήθηκε με επιτυχία!';
            } else {
                return 'Κάτι πήγε στραβά. Παρακαλώ προσπαθήστε ξανά.';
            }
        }
    }
}

// Create AnnouncementManager object
$announcementManager = new AnnouncementManager($conn);

$message = '';

if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $details = $_POST['details'];
    $items = isset($_POST['items']) ? $_POST['items'] : [];
    $message = $announcementManager->createAnnouncement($title, $details, $items);
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Σελίδα Διαχειριστή</title>

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>

<div class="form-container">

    <form action="" method="post">
        <h3>Ανακοινώσεις</h3>
        <?php
        if (!empty($message)) {
            echo '<span class="message-msg">' . $message . '</span>';
        }
        ?>
        <input type="text" name="title" required placeholder="Τίτλος Ανακοίνωσης">
        <textarea name="details" required placeholder="Γράψτε το κείμενό σας εδώ."></textarea>
        <!-- Dropdown για επιλογή αντικειμένων -->
        <select name="items[]" id="items" multiple="multiple" required style="width: 100%;">
            <?php
            $items_result = mysqli_query($conn, "SELECT id, name FROM items");
            while ($item_row = mysqli_fetch_assoc($items_result)) {
                echo '<option value="' . $item_row['id'] . '">' . $item_row['name'] . '</option>';
            }
            ?>
        </select>

        <input type="submit" name="submit" value="Δημιουργία Ανακοίνωσης" class="form-btn">
        <p><a href="announcements.php">Πίσω στις Ανακοινώσεις</a></p>
    </form>

</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('#items').select2({
        placeholder: "Επιλέξτε αντικείμενα:",
        allowClear: true
    });
});
</script>

</body>
</html>
