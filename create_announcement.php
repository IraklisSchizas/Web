<?php

@include 'config.php';

session_start();
// Redirect to login page if not logged in
if(!isset($_SESSION['user_name'])){
   header('location:login.php');
}

class AnnouncementManager {
    private $conn;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create announcement
    public function createAnnouncement($title, $details) {
        $title = mysqli_real_escape_string($this->conn, $title);
        $details = mysqli_real_escape_string($this->conn, $details);
        $date = date("Y-m-d H:i:s");

        if(empty($title) || empty($details)) {
            return 'Όλα τα πεδία είναι υποχρεωτικά.';
        } else {
            $insert = "INSERT INTO announcements (title, details, date) VALUES ('$title', '$details', '$date')";
            if(mysqli_query($this->conn, $insert)) {
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

if(isset($_POST['submit'])){
    $title = $_POST['title'];
    $details = $_POST['details'];
    $message = $announcementManager->createAnnouncement($title, $details);
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
</head>
<body>

<div class="form-container">

    <form action="" method="post">
        <h3>Ανακοινώσεις</h3>
        <?php
        if(!empty($message)){
            echo '<span class="message-msg">'.$message.'</span>';
        }
        ?>
        <input type="text" name="title" required placeholder="Τίτλος Ανακοίνωσης">
        <textarea name="details" required placeholder="Γράψτε το κείμενό σας εδώ."></textarea>
        <input type="submit" name="submit" value="Δημιουργία Ανακοίνωσης" class="form-btn">
        <p><a href="announcements.php">Πίσω στις Ανακοινώσεις</a></p>
    </form>

</div>

</body>
</html>
