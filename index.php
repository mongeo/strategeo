<?php
ini_set('display_errors', 1);

//Check if authenticated
session_start();
if (isset($_SESSION['auth'])){
   header("Location: user/welcome.php");
   exit();
} else {
   header("Location: user/login.php");
   exit();
}
?>