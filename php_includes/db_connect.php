<?php
$conn = "";
$file = "/var/www/conf/db_conn";
if($f = fopen($file, 'r')){
   $line = fgets($f);
   $fields = explode(' ', $line);
   $conn = mysqli_connect($fields[0],$fields[1],$fields[2],$fields[3]) or die(mysqli_error());
   fclose($f);
} else {
   echo "Could not read configuration file: $file . to connect ot database";
   exit();
}

?>