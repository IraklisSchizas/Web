<?php
@include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $id = $_POST['id'];
    $rescuer_id = $_POST['rescuer_id'];
    $load_date = date('Y-m-d H:i:s');

    if ($action == 'take_offer') {
        $update_query = "UPDATE offers SET rescuer_id = ?, load_date = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("isi", $rescuer_id, $load_date, $id);
        $stmt->execute();

        // Fetch item_ids and quantity from the offer
        $fetch_query = "SELECT item_id, quantity FROM offers WHERE id = ?";
        $stmt = $conn->prepare($fetch_query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($item_ids, $quantity);
        $stmt->fetch();
        $stmt->close();

        // Split item_ids and insert each item individually into the cargo table
        $item_ids_array = explode(',', $item_ids);
        foreach ($item_ids_array as $item_id) {
            $cargo_query = "INSERT INTO cargo (rescuer_id, item_ids, quantity) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($cargo_query);
            $stmt->bind_param("iii", $rescuer_id, trim($item_id), $quantity);
            $stmt->execute();
        }
    }

    if ($action == 'take_request') {
        $update_query = "UPDATE requests SET rescuer_id = ?, load_date = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("isi", $rescuer_id, $load_date, $id);
        $stmt->execute();

        // Fetch item_ids and quantity from the request
        $fetch_query = "SELECT item_id, quantity FROM requests WHERE id = ?";
        $stmt = $conn->prepare($fetch_query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($item_ids, $quantity);
        $stmt->fetch();
        $stmt->close();

        // Split item_ids and insert each item individually into the cargo table
        $item_ids_array = explode(',', $item_ids);
        foreach ($item_ids_array as $item_id) {
            $cargo_query = "INSERT INTO cargo (rescuer_id, item_ids, quantity) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($cargo_query);
            $stmt->bind_param("iii", $rescuer_id, trim($item_id), $quantity);
            $stmt->execute();
        }
    }

    echo 'Success';
}
?>
