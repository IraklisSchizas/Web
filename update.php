<?php

@include 'config.php';

session_start();

// Ελέγχουμε αν ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

if(isset($_POST['submit'])){

    if (isset($_GET['updateid'])){
        $id = $_GET['updateid'];
    }
    $itemName = mysqli_real_escape_string($conn, $_POST['itemName']);
    $itemCategory = mysqli_real_escape_string($conn, $_POST['itemCategory']);
    $itemQuantity = mysqli_real_escape_string($conn, $_POST['itemQuantity']);
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
    
    $categoryName = mysqli_real_escape_string($conn, $_POST['categoryName']);

    $updateItem = "UPDATE items SET name='$itemName', category='$itemCategory', details='$itemDetailsJSON', quantity='$itemQuantity' WHERE id=$id";
    $updateCategory = "UPDATE categories SET name='$categoryName' WHERE id=$id";
    
    if (isset($_GET['is_a']) && $_GET['is_a'] == 'item') {
        // Προφόρτωση δεδομένων για το αντικείμενο
        $sql = "SELECT * FROM items WHERE id='$id' ";
        $result2 = mysqli_query($conn, $result2);
        $row = mysqli_fetch_assoc($result2);
        $old_name = $row['name'];
        $old_categorty = $row['category'];
        $old_details = $row['details'];
        $old_quantity = $row['quantity'];
        // Εντολή ενημέρωσης για τα αντικείμενα
        $updateItem = "UPDATE items SET name='$itemName', category='$itemCategory', details='$itemDetailsJSON', quantity='$itemQuantity' WHERE id=$id";
        $result = mysqli_query($conn, $updateItem);
    } elseif (isset($_GET['is_a']) && $_GET['is_a'] == 'category') {
        // Προφόρτωση δεδομένων για την κατηγορία
        $sql = "SELECT * FROM items WHERE id=$id ";
        $result2 = mysqli_query($conn, $result2);
        $row = mysqli_fetch_assoc($result2);
        $old_name = $row['name'];
        // Εντολή ενημέρωσης για τις κατηγορίες
        $categoryName = mysqli_real_escape_string($conn, $_POST['categoryName']);
        $updateCategory = "UPDATE categories SET name='$categoryName' WHERE id=$id";
        $result = mysqli_query($conn, $updateCategory);
    }    
    if($result) {
        header("Location: display.php");
        exit();
    }else{
        die(mysqli_error($conn));
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
            grid-template-columns: repeat(4, 1fr); /* 4 στήλες για τα Items */
            gap: 10px; /* Κενό μεταξύ των στοιχείων */
        }

        .boxInput.categories {
            grid-template-columns: repeat(1, 1fr); /* 1 στήλη για τις Categories */
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
            <?php if (isset($_GET['is_a']) && $_GET['is_a'] == 'item'): ?>
            <h3>Επεξεργασία Αντικειμένου</h3>
            <div class="boxInput" id="items">
                <input type="text" name="itemName" placeholder="Όνομα αντικειμένου" value=$old_name>
                <input type="number" name="itemCategory" placeholder="Κατηγορία αντικειμένου" value=$old_categorty>
                <input type="text" name="itemDetails" placeholder="Λεπτομέρειες" value=$old_details>
                <input type="number" name="itemQuantity" placeholder="Ποσότητα" value=$old_quantity>
            </div>
            <?php endif; ?>
            <?php if (isset($_GET['is_a']) && $_GET['is_a'] == 'category'): ?>
            <h3>Επεξεργασία Κατηγορίας</h3>
            <div class="boxInput categories" id="categories">
                <input type="text" name="categoryName" placeholder="Όνομα κατηγορίας" value=$old_name>
            </div>
            <?php endif; ?>
            <br>
            <button type="submit" name="submit" class="form-btn">Ενημέρωση</button><br><br>
            <p><a href="display.php">Πίσω στην Διαχείρηση Βάσης</a></p>
        </form>

    </div>
    <script src="main.js"></script>
    </body>
</html>
