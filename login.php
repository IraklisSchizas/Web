<?php

@include 'config.php';

session_start();

if(isset($_POST['submit'])){

   $username = isset($_POST['username']) ? mysqli_real_escape_string($conn, $_POST['username']) : "";
   $name = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : "";
   $surname = isset($_POST['surname']) ? mysqli_real_escape_string($conn, $_POST['surname']) : "";
   $phone = isset($_POST['phone']) ? mysqli_real_escape_string($conn, $_POST['phone']) : "";
   $latitude = isset($_POST['latitude']) ? mysqli_real_escape_string($conn, $_POST['latitude']) : "";
   $longtitude = isset($_POST['longtitude']) ? mysqli_real_escape_string($conn, $_POST['longtitude']) : "";
   $pass = isset($_POST['password']) ? md5($_POST['password']) : "";
   $cpass = isset($_POST['cpassword']) ? md5($_POST['cpassword']) : "";
   $user_type = isset($_POST['user_type']) ? $_POST['user_type'] : "";

   $select = " SELECT * FROM users WHERE username = '$username' && password = '$pass' ";

   $result = mysqli_query($conn, $select);

   if(mysqli_num_rows($result) > 0){

      $row = mysqli_fetch_array($result);

      if($row['user_type'] == 'admin'){

        $_SESSION['user_name'] = $row['name'];
        header('location:admin_page.php');

      }elseif($row['user_type'] == 'rescuer'){

         $_SESSION['user_name'] = $row['name'];
         header('location:rescuer_page.php');

      }elseif($row['user_type'] == 'civilian'){

         $_SESSION['user_name'] = $row['name'];
         header('location:civilian_page.php');

      }     
   }else{
      $error[] = 'Incorrect username or password!';
   }
};
?>

<!DOCTYPE html>
<html lang="el">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Σύνδεση</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<div class="form-container">

   <form action="" method="post">
      <h3>Σύνδεση</h3>
      <?php
      if(isset($error)){
         foreach($error as $error){
            echo '<span class="error-msg">'.$error.'</span>';
         };
      };
      ?>
      <input type="text" name="username" required placeholder="Όνομα Χρήστη">
      <input type="password" name="password" required placeholder="Κωδικός Πρόσβασης">
      <input type="submit" name="submit" value="Σύνδεση" class="form-btn">
      <p>Δεν έχετε λογαριασμό; <a href="register_form.php">Εγγραφείτε</a></p>
   </form>

</div>
</body>
</html>
