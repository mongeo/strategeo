<?php
ini_set('display_errors', 1);

$path = $_SERVER['CONTEXT_DOCUMENT_ROOT'] . "/strategeo/";

if(function_exists('mysqli_connect')){
	echo "EXISTS!";
} else {
  echo "Nope";
}


$conn = "";
$file = "../../conf/db.conn";
if($f = fopen($file, 'r')){
   $line = fgets($f);
   $fields = explode(' ', $line);
   $conn = mysqli_connect($fields[0],$fields[1],$fields[2],$fields[3]) or die(mysqli_error());
   fclose($f);
} else {
   echo "Could not read configuration file: $file . to connect ot database";
   exit();
}

#if (isset($_COOKIE['name']) || isset($_SESSION['name'])){
#   header("Location: user/welcome.php");
#   exit();
#} else {
#   header("Location: user/login.php");
#}
echo $path;
?>
