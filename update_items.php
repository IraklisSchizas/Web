<?php
include 'config.php';

// Ελέγχουμε αν τα απαιτούμενα πεδία υπάρχουν στην αίτηση
if (isset($_GET['id']) && isset($_GET['name']) && isset($_GET['category']) && isset($_GET['details']) && isset($_GET['quantity'])) {
    // Αναθέτουμε τις τιμές από την αίτηση σε μεταβλητές
    $id = $_GET['id'];
    $name = $_GET['name'];
    $category = $_GET['category'];
    $details = $_GET['details'];
    $quantity = $_GET['quantity'];

    // Εκτελούμε το ερώτημα ενημέρωσης στη βάση δεδομένων
    $sql = "UPDATE items SET name='$name', category='$category', details='$details', quantity='$quantity' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    echo "Required fields are missing";
}

$conn->close();
?>