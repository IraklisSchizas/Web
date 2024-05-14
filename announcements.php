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
        <input type="text" name="title" required placeholder="Τίτλος Ανακοίνωσης">
        <input type="text" name="details" required placeholder="Γράψτε το κείμενό σας εδώ.">
        <input type="button" name="submit" onclick="getCurrentDateTime()" required value="Δημιουργία Ανακοίνωησς" class="form-btn">
        <p>Έχετε ήδη λογαριασμό; <a href="login.php">Σύνδεση</a></p>
    </form>
</div>

<script src="main.js"></script>

</body>