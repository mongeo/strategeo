<?php
$path = $_SERVER['CONTEXT_DOCUMENT_ROOT'] . "/stratego/";

if (isset($_COOKIE['name']) || isset($_SESSION['name'])){
   header("Location: user/welcome.php");
   exit();
} else {
   header("Location: user/login.php");
}

?>
