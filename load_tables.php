<?php
include 'config.php';

// Επιλέξτε τα αντικείμενα από τη βάση δεδομένων
$sql_items = "SELECT * FROM items";
$result_items = $conn->query($sql_items);
$items = array();

// Επεξεργασία των αποτελεσμάτων για τη δημιουργία του HTML πίνακα
if ($result_items->num_rows > 0) {
    while($row = $result_items->fetch_assoc()) {
        $details = json_decode($row['details'], true);
        $formatted_details = array();
        foreach ($details as $detail) {
            $formatted_details[] = $detail['detail_name'] . ' ' . $detail['detail_value'];
        }
        $row['details'] = implode('<br>', $formatted_details);

        // Κουμπί επεξεργασίας για κάθε εγγραφή
        $row['edit_button'] = '<button onclick="editRow('.$row['id'].')">Επεξεργασία</button>';

        // Δημιουργία του HTML για κάθε γραμμή του πίνακα με ID
        $items[] = '<tr id="item_'.$row['id'].'"><td>'.$row['id'].'</td><td>'.$row['name'].'</td><td>'.$row['category'].'</td><td>'.$row['details'].'</td><td>'.$row['quantity'].'</td><td>'.$row['edit_button'].'</td></tr>';
    }
}

// Επιστροφή του HTML πίνακα ως αποτέλεσμα
echo implode('', $items);

$conn->close();
?>
