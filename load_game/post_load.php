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
if ($state < 3){
   print "Cannot post move: Game not fully initialized. ";
   print "Redirecting in 3 seconds. . . ";
   header("refresh:3;url=../user/index.php");
   exit();
} elseif ($state > 4){
   print "Cannot post move: Game is over. ";
   print "Redirecting in 3 seconds. . . ";
   header("refresh:3;url=../user/index.php");
   exit();
} 
if ($state == 3){
   $state = 4;
}

#
# Check for $_POST data
#
if (!isset($_POST['gameArray'])){
   print "Error: Could not get game array. ";
   print "Redirecting in 3 seconds. . . ";
   header("refresh:3;url=../user/index.php");
   exit();
}

$sLoc = $_POST['sLoc'];
$dLoc = $_POST['dLoc'];
$sVal = $_POST['sVal'];
$dVal = $_POST['dVal'];
$rVal =  $_POST['rVal'];
$sColor = substr($sVal, 0,1);
$dColor = substr($dVal, 0, 1);
$rColor = substr($rVal, 0, 1);
$gameArray = explode(",",$_POST['gameArray']);
$move = "$sVal to $dLoc ($rVal occupies $dLoc)";

# Turn $_POST into comma seperated string
# Use for red and blue views
$b_stack = [];
$r_stack = [];
foreach($gameArray as $value){
   if ($value[0] == "B"){
      array_push($b_stack, "$value");
      array_push($r_stack, "$value[0]");
   } elseif ($value[0] == "R"){
      array_push($r_stack, "$value");
      array_push($b_stack, "$value[0]");
   } else {
      array_push($b_stack, "$value");
      array_push($r_stack, "$value");
   }   
}
$post_b_str = implode(',', $b_stack);
$post_r_str = implode(',', $r_stack);
#$post_Str = base64_encode($post_Str);



#
# Update database new game in database
#
$gameUpdate = "UPDATE GAME 
	       SET lastMoveBy='$name', lastMoveBlue='Joined Game' 
	       Where gameID='$gid'";
if (!mysqli_query($conn, $gameUpdate)) {
    print "Failed to connect ot database and update game.<br>" . mysqli_error($conn);
    print "<br><a href='../user/index.php'>Return to your home page</a>";
}


#
# Insert red values into BOARD
# Only show red pieces (not values for blue player's view)
#
$binstmt = $conn->prepare("UPDATE BOARD 
	 SET bluePlayerView=?, redPlayerView=? 
	 WHERE gameID=?");
$binstmt -> bind_param('ssi', $post_b_str, $post_r_str, $gid);
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