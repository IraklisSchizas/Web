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
   
<div class="container">

   <div class="content">
      <h3>Γειά σου, <span>admin</span></h3>
      <h1>Καλωσήρθες <span><?php echo $_SESSION['user_name'] ?></span>!</h1>
      <p>αυτή είναι η σελίδα του διαχειριστή</p>
      <!-- <a href="login.php" class="btn">login</a>
      <a href="register_form.php" class="btn">register</a> -->
      <a href="register_rescuer.php" class="btn">Εγγραφή Διασώστη</a>
      <a href="manage_database.php" class="btn">Διαχείρηση Βάσης</a>
      <a href="logout.php" class="btn">αποσύνδεση</a>
   </div>

</div>

</body>
</html>