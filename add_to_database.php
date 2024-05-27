<?php

@include 'config.php';

session_start();

// Ελέγχουμε αν ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

if(isset($_POST['submit'])){

    $itemId = mysqli_real_escape_string($conn, $_POST['itemId']);
    $itemName = mysqli_real_escape_string($conn, $_POST['itemName']);
    $itemCategory = mysqli_real_escape_string($conn, $_POST['itemCategory']);
    
    // Αλλαγή των detail ώστε να έρθουν στην JSON μορφή που θέλουμε
    $itemDetailsInput = array_map('trim', explode(',', $_POST['itemDetails']));
    $itemDetails = [];
    foreach ($itemDetailsInput as $detail) {
        // Αφαίρεση περιττών χαρακτηρών
        $detail = str_replace('"', '', $detail);
        $detailParts = explode(':', $detail);
        $detailArray = [
            "detail_name" => trim($detailParts[0]),
            "detail_value" => trim($detailParts[1])
        ];
        if (!empty($detailArray["detail_name"])) {
            $itemDetails[] = $detailArray;
        }
    }
    $itemDetailsJSON = json_encode($itemDetails);

    $itemQuantity = mysqli_real_escape_string($conn, $_POST['itemQuantity']);
    $categoryId = mysqli_real_escape_string($conn, $_POST['categoryId']);
    $categoryName = mysqli_real_escape_string($conn, $_POST['categoryName']);

    $insertItem = "INSERT INTO items(id, name, category, details, quantity) VALUES ('$itemId', '$itemName', '$itemCategory', '$itemDetailsJSON', '$itemQuantity')";
    $insertCategory = "INSERT INTO categories(id, name) VALUES ('$categoryId', '$categoryName')";
    
    if($itemId != NULL && $categoryId != NULL) {
        mysqli_query($conn, $insertItem);
        mysqli_query($conn, $insertCategory);
        echo("Το αντικείμενο και η κατηγορία αποθηκεύτηκαν επιτυχώς!");
        header('location: manage_database.php');
        exit();
    }else if($itemId != NULL) {
        mysqli_query($conn, $insertItem);
        echo("Το αντικείμενο αποθηκεύτηκε επιτυχώς!");
        header('location: manage_database.php');
        exit();
    }else if($categoryId != NULL){
        mysqli_query($conn, $insertCategory);
        echo("Η κατηγορία αποθηκεύτηκε επιτυχώς!");
        header('location: manage_database.php');
        exit();
    }else{
        die("Connection failed: " . $conn->connect_error);
        $error[] = 'Σφάλμα!';
    };
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
            <h3>Αντικείμενο</h3>
            <div class="boxInput" id="items">
                <input type="number" name="itemId" placeholder="ID">
                <input type="text" name="itemName" placeholder="Όνομα αντικειμένου">
                <input type="number" name="itemCategory" placeholder="Κατηγορία αντικειμένου">
                <input type="text" name="itemDetails" placeholder="Λεπτομέρειες">
                <input type="number" name="itemQuantity" placeholder="Ποσότητα">
            </div>
            <br>
            <h3>Κατηγορία</h3>
            <div class="boxInput categories" id="categories">
                <input type="number" name="categoryId" placeholder="ID">
                <input type="text" name="categoryName" placeholder="Όνομα κατηγορίας">
            </div><br>
            <button type="submit" name="submit" class="form-btn">Προσθήκη</button><br><br>
            <p><a href="manage_database.php">Πίσω στην Διαχείρηση Βάσης</a></p>
        </form>

    </div>
    </body>
</html>
