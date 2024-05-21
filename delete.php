<?php

@include 'config.php';
session_start();
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

if (isset($_GET['deleteid'])){
    $id = $_GET['deleteid'];

    $sql = "DELETE FROM items WHERE id=$id";
    $result = mysqli_query($conn, $sql);
    if($result) {
        echo("Deleted successfully!");
        header("Location: display.php");
    }else{
        die(mysqli_error($conn));
    }
} 
?>