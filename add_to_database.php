<?php

@include 'config.php';

session_start();

// Ελέγχουμε αν ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

if(isset($_POST['submit'])){

    $itemId = $_POST['itemId'];
    $itemName = $_POST['itemName'];
    $itemCategory = $_POST['itemCategory'];
    $itemDetails = $_POST['itemDetails'];
    $itemQuantity = $_POST['itemQuantity'];
    $categoryId = $_POST['categoryId'];
    $categoryName = $_POST['categoryName'];

    $insertItem = "INSERT INTO 'items'(id, name, category, details, quantity)
            VALUES ('$itemId, $itemName, $itemCategory, $itemDetails, $itemQuantity')";
    $insertCategory = "INSERT INTO 'categories'(id, name)
            VALUES ('$categoryId, $categoryName')";
    
    $itemResult = mysqli_real_escape_string($conn, $insertItem);
    $categoryResult = mysqli_real_escape_string($conn, $insertCategory);
    if($itemResult | $categoryResult) {
        echo("Τα δεδομένα αποθηκεύτηκαν επιτυχώς!");
    }else{
        die("Connection failed: " . $conn->connect_error);
    }
};

?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Σελίδα Διαχειριστή</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Κώδικας CSS για το boxInput tag */
        .boxInput {
            display: grid;
            grid-template-columns: repeat(5, 1fr); /* 5 στήλες για τα Items */
            gap: 10px; /* Κενό μεταξύ των στοιχείων */
        }

        .boxInput.categories {
            grid-template-columns: repeat(2, 1fr); /* 2 στήλες για τις Categories */
        }

        .boxInput input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            width: auto; /* Προσθέτουμε αυτή την επιλογή για να επιβάλουμε το πλάτος του input να είναι αυτό που καθορίζουμε μέσω του grid */
        }
    </style>
</head>
<body>
<div class="form-container">
    <form action="" method="post">
        <?php
        if(isset($error)){
            foreach($error as $error){
                echo '<span class="error-msg">'.$error.'</span>';
            };
        };
        ?>
        <br>
        <h3>Items</h3>
        <div class="boxInput" id="items">
            <input type="text" name="itemId" placeholder="ID">
            <input type="text" name="itemName" placeholder="Όνομα αντικειμένου">
            <input type="text" name="itemCategory" placeholder="Κατηγορία αντικειμένου">
            <input type="text" name="itemDetails" placeholder="Λεπτομέρειες">
            <input type="text" name="itemQuantity" placeholder="Ποσότητα">
        </div>
        <button type="submit" class="form-btn" name="submit">Προσθήκη Αντικειμένου</button>
        <br><br><br>
        <h3>Categories</h3>
        <div class="boxInput categories" id="categories">
            <input type="text" name="categoryId" placeholder="ID">
            <input type="text" name="categoryName" placeholder="Όνομα κατηγορίας">
        </div>
        <button type="submit" class="form-btn" name="submitCategory">Προσθήκη Κατηγορίας</button>
    </form>

</div>
<script src="main.js"></script>
</body>
</html>
