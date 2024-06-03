<?php
@include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $taskId = intval($_POST['id']);
    $type = $_POST['type'];

    if ($action === 'complete') {
        // Update the task status to 'completed'
        if ($type === 'offer') {
            $update_query = "UPDATE offers SET status = 'completed' WHERE id = $taskId";
        } elseif ($type === 'request') {
            $update_query = "UPDATE requests SET status = 'completed' WHERE id = $taskId";
        }

        if (mysqli_query($conn, $update_query)) {
            echo 'Task completed successfully.';
        } else {
            echo 'Error completing task: ' . mysqli_error($conn);
        }
    } elseif ($action === 'cancel') {
        // Update the task status to 'available'
        if ($type === 'offer') {
            $update_query = "UPDATE offers SET status = 'available', rescuer_id = NULL WHERE id = $taskId";
        } elseif ($type === 'request') {
            $update_query = "UPDATE requests SET status = 'available', rescuer_id = NULL WHERE id = $taskId";
        }

        if (mysqli_query($conn, $update_query)) {
            echo 'Task cancelled successfully.';
        } else {
            echo 'Error cancelling task: ' . mysqli_error($conn);
        }
    }
}
?>
