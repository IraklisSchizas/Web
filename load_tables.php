<?php
include 'config.php';

// Επιλέξτε τα αντικείμενα και τις κατηγορίες από τη βάση δεδομένων
$sql_items = "SELECT * FROM items";
$result_items = $conn->query($sql_items);
$items = array();

// Επεξεργασία των αποτελεσμάτων για τη δημιουργία του JSON
if ($result_items->num_rows > 0) {
    while($row = $result_items->fetch_assoc()) {
        $items[] = $row;
    }
}

$sql_categories = "SELECT * FROM categories";
$result_categories = $conn->query($sql_categories);
$categories = array();

if ($result_categories->num_rows > 0) {
    while($row = $result_categories->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Συνδυασμός των δεδομένων σε έναν συνολικό πίνακα JSON
$data = array(
    'items' => $items,
    'categories' => $categories
);

// Επιστροφή των δεδομένων ως JSON
header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
