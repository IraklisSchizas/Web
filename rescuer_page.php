<?php

@include 'config.php';

session_start();

//Redirect to loggin page if not logged in
if(!isset($_SESSION['user_name'])){
   header('location:login.php');
}
$query = $conn->prepare("SELECT name FROM users WHERE username = ?");
$query->bind_param('s', $_SESSION['user_name']);
$query->execute();
$result = $query->get_result();
$user_row = $result->fetch_assoc();
$name = $user_row['name'];
?>

<!DOCTYPE html>
<html lang="el">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Σελίδα Διασώστη</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<div class="container">

   <div class="content">
      <h3>Γειά σου, <span>Διασώστη</span></h3>
      <h1>Καλωσήρθες <span><?php echo $name ?></span>!</h1>
      <p>αυτή είναι η σελίδα των διασωστών</p>
      <a href="cargo_management.php" class="btn">διαχείριση φορτίου</a>
      <a href="rescuer_map.php" class="btn">προβολή χάρτη & διαχείριση tasks</a>
      <a href="logout.php" class="btn">αποσύνδεση</a>
   </div>

</div>

</body>
</html>
