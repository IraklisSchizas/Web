<?php
@include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $username = $_POST['username'];

    // Ενημέρωση της βάσης δεδομένων με τις νέες συντεταγμένες
    $update_query = $conn->prepare("UPDATE users SET latitude = ?, longitude = ? WHERE username = ?");
    $update_query->bind_param("dds", $latitude, $longitude, $username);

    if ($update_query->execute()) {
        echo "Η τοποθεσία ενημερώθηκε επιτυχώς.";
    } else {
        echo "Σφάλμα κατά την ενημέρωση της τοποθεσίας.";
    }
}
?>