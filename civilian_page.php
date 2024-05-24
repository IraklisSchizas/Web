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
   <title>Σελίδα Πολίτη</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<div class="container">

   <div class="content">
      <h3>Γειά σου, <span>Πολίτη</span></h3>
      <h1>Καλωσήρθες <span><?php echo $_SESSION['user_name'] ?></span>!</h1>
      <p>αυτή είναι η σελίδα των πολιτών</p>
      <a href="civilian_requests.php" class="btn">διαχείριση αιτημάτων</a>
      <a href="civilian_announcements.php" class="btn">Ανακοινώσεις</a>
      <a href="logout.php" class="btn">αποσύνδεση</a>
   </div>

</div>

</body>
</html>
