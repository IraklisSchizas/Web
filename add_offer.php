<?php

@include 'config.php';

session_start();

// Ελέγχουμε αν ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

if(isset($_POST['submit'])){

    $itemName = mysqli_real_escape_string($conn, $_POST['itemName']);
    $itemCategory = mysqli_real_escape_string($conn, $_POST['itemCategory']);
    $quantity = mysqli_real_escape_string($conn, $_POST['itemQuantity']);

    $civilian_name = $_SESSION['user_name'];
    // Χρήση προετοιμασμένων δηλώσεων για ασφάλεια
    $stmt = $conn->prepare("SELECT * FROM users WHERE name = ?");
    $stmt->bind_param("s", $civilian_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $civilian_id = $row['id'];
    
    $date;
    $itemId;

    
    $insertOffer = "INSERT INTO offers(civilian_id, date, item_id, quantity, load_date, rescuer_id) VALUES ('$civilian_id', '$date', '$itemId, '$quantity', 0, 0)";
    
    if($itemId != NULL) {
        mysqli_query($conn, $insertOffer);
        echo("Η προσφορά προστέθηκε επιτυχώς!");
        header('location: civilian_requests.php');
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
            grid-template-columns: repeat(3, 1fr); /* 5 στήλες για τα Items */
            gap: 10px; /* Κενό μεταξύ των στοιχείων */
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
            <h3>Δημιουργία Προσφοράς</h3>
            <div class="boxInput" id="items">
                <input type="text" name="itemName" placeholder="Όνομα αντικειμένου">
                <input type="text" name="itemCategory" placeholder="Κατηγορία αντικειμένου">
                <input type="number" name="itemQuantity" placeholder="Ποσότητα">
            </div><br>
            <button type="submit" name="submit" class="form-btn">Προσθήκη</button><br><br>
            <p><a href="civilian_requests.php">Πίσω στα Αιτήματα</a></p>
        </form>

    </div>
    </body>
</html>
