<?php

@include 'config.php';

session_start();

// Ελέγχουμε αν ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Σελίδα Διαχειριστή</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <form action="" method="post">
            <input type="button" id="j_button" class="form-btn" onclick="initialize()" value="Αρχικοποίηση"><br>
            <input type="button" id="j_button" class="form-btn" onclick="window.location.href = 'add_to_database.php'" value="Προσθήκη Αντικειμένου - Κατηγορίας"><br>   
            <p><a href="admin_page.php">Πίσω στη σελίδα Διαχειριστή</a></p>
            <br><br>
            <h2>Items Table</h2>
            <table class="table" id="jsonItemsTable">
                <thead>
                    <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Category</th>
                    <th scope="col">Details</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT  * FROM items";
                        $result = mysqli_query($conn, $sql);
                        if($result){
                            while($row=mysqli_fetch_assoc($result)){
                                $id = $row['id'];
                                $name = $row['name'];
                                $category = $row['category'];
                                $details = $row['details'];
                                $quantity = $row['quantity'];
                                echo('<tr>
                                        <th scope="row">'.$id.'</th>
                                        <td>'.$name.'</td>
                                        <td>'.$category.'</td>
                                        <td>'.$details.'</td>
                                        <td>'.$quantity.'</td>
                                        <td>
                                            <button><a href="">Update</a></button>
                                            <button><a href="">Delete</a></button>
                                        </td>
                                    </tr>
                                ');
                            }
                        }
                    ?>
                </tbody>
                </table>
                <br><br><br>
            <h2>Categories Table</h2>
            <table class="table" id="jsonCategoriessTable">
                <thead>
                    <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT  * FROM categories";
                        $result = mysqli_query($conn, $sql);
                        if($result){
                            while($row=mysqli_fetch_assoc($result)){
                                $id = $row['id'];
                                $name = $row['name'];
                                echo('<tr>
                                        <th scope="row">'.$id.'</th>
                                        <td>'.$name.'</td>
                                        <td>'.'</td>
                                    </tr>
                                ');
                            }
                        }
                    ?>
                </tbody>
            </table>
        </form>
    </div>
</body>