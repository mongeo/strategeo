<?php
#ini_set('display_errors', 1);
require '../php_includes/db_connect.php';

//Check if authenticated
session_start();
if (isset($_SESSION['auth']) == false){
   header("Location: login.php");
   exit();
} else {
   print "Welcome " . $_SESSION['auth'];
}
?>
