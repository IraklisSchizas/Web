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

        $cargo_query = "INSERT INTO cargo (rescuer_id, item_ids, quantity) SELECT ?, item_id, quantity FROM offers WHERE id = ?";
        $stmt = $conn->prepare($cargo_query);
        $stmt->bind_param("ii", $rescuer_id, $id);
        $stmt->execute();
    }

    if ($action == 'take_request') {
        $update_query = "UPDATE requests SET rescuer_id = ?, load_date = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("isi", $rescuer_id, $load_date, $id);
        $stmt->execute();

        $cargo_query = "INSERT INTO cargo (rescuer_id, item_ids, quantity) SELECT ?, item_id, quantity FROM requests WHERE id = ?";
        $stmt = $conn->prepare($cargo_query);
        $stmt->bind_param("ii", $rescuer_id, $id);
        $stmt->execute();
    }

    echo 'Success';
}
?>