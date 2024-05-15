<?php
include 'config.php';

$sql_items = "SELECT * FROM items";
$result_items = $conn->query($sql_items);
$items = array();

if ($result_items->num_rows > 0) {
    while($row = $result_items->fetch_assoc()) {
        $details = json_decode($row['details'], true);
        $formatted_details = array();
        foreach ($details as $detail) {
            $formatted_details[] = $detail['detail_name'] . ' ' . $detail['detail_value'];
        }
        $row['details'] = implode('<br>', $formatted_details);

        $row['edit_button'] = '<button onclick="editRow('.$row['id'].')">Edit</button>';

        $items[] = '<tr id="item_'.$row['id'].'"><td>'.$row['id'].'</td><td>'.$row['name'].'</td><td>'.$row['category'].'</td><td>'.$row['details'].'</td><td>'.$row['quantity'].'</td><td>'.$row['edit_button'].'</td></tr>';
    }
}

echo implode('', $items);

$conn->close();
?>