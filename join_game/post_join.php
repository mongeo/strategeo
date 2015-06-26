<?php
ini_set('display_errors', 1);

# Authenticate user
session_start();
if (!isset($_SESSION['auth'])) {
   header("Location: ../user/login.php");
   exit();			      
}
$name = $_SESSION['auth'];

# Check if game id has been set
if (!isset($_SESSION['gid'])) {
   print "No game selected. Redirecting in 3 seconds. . . ";
   header("refresh:3;url=../user/index.php");
   exit();
}
$gid = $_SESSION['gid'];

require "../php_includes/db_connect.php";

#
# Get game information and see if it is joinable
#
$state = "";
$blue = "";
$red = "";
if ($gstmt = $conn->prepare("SELECT state, blue, red 
   	     		     FROM GAME 
			     WHERE gameID=?")){
   $gstmt -> bind_param('i', $gid);
   $gstmt -> execute();
   $gstmt -> bind_result($s, $b, $r);
   $gstmt -> fetch();
   $state = $s;
   $blue = $b;
   $red = $r;
   $gstmt -> close();
} else {
  echo "Couldn't connect<br> " . mysqli_error($conn);
}
if ($state == 0){
   print "Cannot join game: Not yet initialized. ";
   print "Redirecting in 3 seconds. . . ";
   header("refresh:3;url=../user/index.php");
   exit();
} elseif ($state == 1){
   print "Cannot join game: Already joined. ";
   print "Redirecting in 3 seconds. . . ";
   header("refresh:3;url=../user/index.php");
   exit();
} elseif ($state > 2 && $state <= 5){
   print "Cannot join game: Already joined. ";
   print "Redirecting in 3 seconds. . . ";
   header("refresh:3;url=../user/index.php");
   exit();
} elseif ($state != 2){
   print "Cannot join game: Unknown error. Game state: $state ";
   print "Redirecting in 3 seconds. . . ";
   header("refresh:3;url=../user/index.php");
   exit();
} elseif ($blue != $name){
   print "Cannot join game: Wrong user. You: $name. Blue Player: $blue ";
   print "Redirecting in 3 seconds. . . ";
   header("refresh:3;url=../user/index.php");
   exit();
}


$ucn = ucfirst($name);
#
# Update database new game in database
#
$gameUpdate = "UPDATE GAME 
	       SET state='3', lastMoveBy='$name', lastMove='$ucn joined game' 
	       Where gameID='$gid'";
if (!mysqli_query($conn, $gameUpdate)) {
    print "Failed to connect ot database and update game.<br>" . mysqli_error($conn);
    print "<br><a href='../user/index.php'>Return to your home page</a>";
}

# Turn $_POST into comma seperated string
$b_stack = [];
array_push($b_stack, "Error: Don't use 0");
foreach($_POST as $key => $value){
   $k = intval(substr($key, 1));
   if ($k == 43 || $k == 44 || $k == 47 || $k == 48 || $k == 53 || $k == 54 || $k == 57 || $k == 58){
      $value = "X";
   }  elseif ($k >= 1 && $k <= 40){
      $value = "R";
   }	     
   if ($value == ""){
      $value = "N";
   }
   array_push($b_stack, "$value");
}
$post_b_str = implode(',', $b_stack);
#$post_Str = base64_encode($post_Str);


#
# Insert red values into BOARD
# Only show red pieces (not values for blue player's view)
#
$binstmt = $conn->prepare("UPDATE BOARD 
	 SET bluePlayerView=? 
	 WHERE gameID=?");
$binstmt -> bind_param('si', $post_b_str, $gid);
$binstmt -> execute();
$binstmt -> close();

#debug
#print_r($_POST);

# Unset gid
$_SESSION['gid'] = '';
unset($_SESSION['gid']);

print "You have joined the game with $red successfully! ";
print "Redirecting in 3 seconds. . . ";
print "<br><br><a href='../user/index.php'>Home</a> ";
header("refresh:3;url=../user/index.php");
?>