<?php

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
?>
