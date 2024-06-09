<?php

@include 'config.php';

if(isset($_POST['submit'])){

   $username = mysqli_real_escape_string($conn, $_POST['username']);
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $surname = mysqli_real_escape_string($conn, $_POST['surname']);
   $phone = mysqli_real_escape_string($conn, $_POST['phone']);
   $latitude = mysqli_real_escape_string($conn, $_POST['latitude']);
   $longitude = mysqli_real_escape_string($conn, $_POST['longitude']);
   $pass = md5($_POST['password']);
   $cpass = md5($_POST['cpassword']);

   $select = " SELECT * FROM users WHERE username = '$username' && password = '$pass' ";

   $result = mysqli_query($conn, $select);

   if(mysqli_num_rows($result) > 0){

      $error[] = 'User already exists!';

   }else{

      if($pass != $cpass){
         $error[] = 'Password does not match!';
      }elseif($latitude == null){
         $error[] = 'Location Denied!';
      }else{
         $insert = "INSERT INTO users(username, name, surname, phone, latitude, longitude, password, user_type) VALUES('$username','$name','$surname','$phone','$latitude','$longitude','$pass','civilian')";
         mysqli_query($conn, $insert);
         header('location:login.php');
      }
   }
};

?>


<!DOCTYPE html>
<html lang="el">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Εγγραφή</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<div class="form-container">

   <form action="" method="post">
      <h3>Εγγραφή</h3>
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
      <input type="submit" name="submit" value="Εγγραφή" class="form-btn"><br>
      <p>Έχετε ήδη λογαριασμό; <a href="login.php">Σύνδεση</a></p>
   </form>

</div>

<script src="javascript/main.js"></script>
</body>
</html>
