<?php

@include 'config.php';

session_start();

//Redirect to loggin page if not logged in
if(!isset($_SESSION['user_name'])){
   header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="el">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Σελίδα Διαχειριστή</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

  
<div class="form-container">

    <form action="" method="post">
        <h3>Ανακοινώσεις</h3>
        <?php
        if(isset($error)){
            foreach($error as $error){
                echo '<span class="error-msg">'.$error.'</span>';
            };
        };
        ?>
        <input type="text" name="username" required placeholder="Όνομα Χρήστη">
        <input type="text" name="name" required placeholder="Όνομα">
        <input type="text" name="surname" required placeholder="Επίθετο">
        <input type="tel" name="phone" pattern="[0-9]{10}" required placeholder="Τηλέφωνο">
        <input type="button" name="position" onclick="getLocation()" required value="Τοποθεσία" class="form-btn">
        <input type="text" name="latitude" id="latitude" required placeholder="Γεωγραφικό Πλάτος" readonly>
        <input type="text" name="longitude" id="longitude" required placeholder="Γεωγραφικό Μήκος" readonly>
        <input type="password" name="password" required placeholder="Κωδικός πρόσβασης">
        <input type="password" name="cpassword" required placeholder="Επιβεβαίωση Κωδικού">
        <input type="submit" name="submit" value="Εγγραφή" class="form-btn">
        <p>Έχετε ήδη λογαριασμό; <a href="login.php">Σύνδεση</a></p>
    </form>
</div>


</body>