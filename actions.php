<?php
@include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'completeOffer' && isset($_POST['offerId'])) {
        $offerId = intval($_POST['offerId']);
        $sql = "DELETE FROM offers WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $offerId);
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
        $stmt->close();
    } elseif ($action == 'cancelOffer' && isset($_POST['offerId'])) {
        $offerId = intval($_POST['offerId']);
        $sql = "UPDATE offers SET rescuer_id = 0, load_date = '0000-00-00 00:00:00' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $offerId);
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
        $stmt->close();
    } elseif ($action == 'completeRequest' && isset($_POST['requestId'])) {
        $requestId = intval($_POST['requestId']);
        $sql = "DELETE FROM requests WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $requestId);
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
        $stmt->close();
    } elseif ($action == 'cancelRequest' && isset($_POST['requestId'])) {
        $requestId = intval($_POST['requestId']);
        $sql = "UPDATE requests SET rescuer_id = 0, load_date = '0000-00-00 00:00:00' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $requestId);
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
        $stmt->close();
    } else {
        echo 'error';
    }

    $conn->close();
}
?>
