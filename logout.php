<?php

@include 'config.php';

session_start();
session_unset();
session_destroy();

//Redirect to loggin page
header('location:login.php');

?>