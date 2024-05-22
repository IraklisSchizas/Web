<?php

@include 'config.php';
session_start();
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

if (isset($_GET['deleteid'])){
    $id = $_GET['deleteid'];
    if (isset($_GET['is_a'])){
        $is_a = $_GET['is_a'];
        if ($is_a == 'item'){
            // Εντολή διαγραφής για τα αντικείμενα
            $sql = "DELETE FROM items WHERE id=$id";
        }elseif ($is_a == 'category'){
            // Εντολή διαγραφής για τις κατηγορίες
            $sql = "DELETE FROM categories WHERE id=$id";
        }else{
            die(mysqli_error($conn));
        }
        $result = mysqli_query($conn, $sql);
        if($result) {
            header("Location: display.php");
        }else{
            die(mysqli_error($conn));
        }
    }else{
        die(mysqli_error($conn));
    }
}else{
    die(mysqli_error($conn));
}
?>