<?php

@include 'config.php';

session_start();

// Ελέγχουμε αν ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

if(isset($_POST['submit'])){

    $itemNames = $_POST['itemName'];
    $itemName = implode(',', array_map([$conn, 'real_escape_string'], $itemNames));
    $quantity = mysqli_real_escape_string($conn, $_POST['itemQuantity']);

    $civilian_name = $_SESSION['user_name'];
    // Χρήση προετοιμασμένων δηλώσεων για ασφάλεια
    $stmt = $conn->prepare("SELECT * FROM users WHERE name = ?");
    $stmt->bind_param("s", $civilian_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $civilian_id = $row['id'];

    $date = date("Y-m-d H:i:s");
    //Πρέπει να πάρουμε το itemId βάση του ονόματος που έδωσε ο χρήστης (itemName), και την ημερομηνία.
    // Χρήση προετοιμασμένων δηλώσεων για ασφάλεια
    $stmt = $conn->prepare("SELECT * FROM items WHERE name = ?");
    $stmt->bind_param("s", $itemName);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $itemId = $row['id'];
    $load_date = 0;
    $rescuer_id = 0;
    
    // Χρήση προετοιμασμένων δηλώσεων για ασφάλεια
    if (isset($_GET['is_a'])){
        $is_a = $_GET['is_a'];
        if($is_a == 'offer') {
            $stmt = $conn->prepare("INSERT INTO offers (civilian_id, date, item_id, quantity, load_date, rescuer_id) VALUES (?, ?, ?, ?, ?, ?)");
        }elseif($is_a == 'request'){
            $stmt = $conn->prepare("INSERT INTO requests (civilian_id, date, item_id, quantity, load_date, rescuer_id) VALUES (?, ?, ?, ?, ?, ?)");
        }

        $stmt->bind_param("issiis", $civilian_id, $date, $itemId, $quantity, $load_date, $rescuer_id);

        if ($stmt->execute()) {
            if ($is_a == 'offer'){
                echo "Η προσφορά προστέθηκε με επιτυχία.";
            }else{
                echo "Το αίτημα προστέθηκε με επιτυχία.";
            }
        } else {
            echo "Σφάλμα: " . $stmt->error;
        }
    $stmt->close();
    $conn->close();
    }
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Κώδικας CSS για το boxInput tag */
        .boxInput {
            display: grid;
            grid-template-columns: repeat(2, 1fr); /* 2 στήλες */
            gap: 10px; /* Κενό μεταξύ των στοιχείων */
        }

        .boxInput input, .boxInput select {
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
            <?php if(isset($_GET['is_a']) && $_GET['is_a'] == 'offer'): ?>
                <h3>Δημιουργία Προσφοράς</h3>
            <?php else: ?>
                <h3>Δημιουργία Αιτήματος</h3>
            <?php endif; ?>
            <div class="boxInput" id="items">
                <!-- Dropdown για επιλογή αντικειμένων -->
                <select name="itemName[]" id="itemName" class="item-select" multiple="multiple" required style="width: auto;">
                    <?php
                    if (isset($_GET['announcement_id'])) {
                        $announcement_id = intval($_GET['announcement_id']);
                        $items_result = $conn->query("SELECT items.id, items.name FROM items
                                                      JOIN announcements ON FIND_IN_SET(items.id, announcements.item_ids)
                                                      WHERE announcements.id = $announcement_id");
                        while ($item_row = $items_result->fetch_assoc()) {
                            echo '<option value="'.$item_row['name'].'">'.$item_row['name'].'</option>';
                        }
                    }
                    ?>
                </select>
                <input type="number" name="itemQuantity" placeholder="Πόσα άτομα αφορά">
            </div><br>
            <button type="submit" name="submit" class="form-btn">Προσθήκη</button><br><br>
            <?php if(isset($_GET['is_a']) && $_GET['is_a'] == 'offer'): ?>
                <p><a href="civilian_announcements.php">Πίσω στις Ανακοινώσεις</a></p>
            <?php else: ?>
                <p><a href="civilian_requests.php">Πίσω στα Αιτήματα</a></p>
            <?php endif; ?>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.item-select').select2({
                placeholder: "Επιλέξτε αντικείμενο",
                allowClear: true
            });
        });
    </script>
</body>
</html>