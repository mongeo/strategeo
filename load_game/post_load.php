<?php
ini_set('display_errors', 1);
#var_dump($_POST);
#exit();

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
$gameArray = explode(",",$_POST['gameArray']);
$move = "";
$fight = false;
if ($dVal == "R0" || $dVal == "B0"){
   $state = 5;
   $move = "$name captured the flag!";
} elseif ($dVal == "N"){
   $move = "$sLoc to $dLoc";
} else {
   $fight = true;
   $move = "$sVal @ $sLoc to $dVal @ $dLoc";
}

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


#var_dump($state);
#exit();

#
# Update database new game in database
#
if ($state == 4){
$gameUpdate = "UPDATE GAME 
	       SET lastMoveBy='$name', lastMove='$name', state='$state' 
	       Where gameID='$gid'";
} else {
$gameUpdate = "UPDATE GAME 
	       SET lastMoveBy='$name', lastMove='$move', state='$state', winner='$name' 
	       Where gameID='$gid'";
}

if (!mysqli_query($conn, $gameUpdate)) {
    print "Failed to connect to database and update game.<br>" . mysqli_error($conn);
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


$res = "<div id='fight_box'>";
$res .= "<table id='fight_table'>";


if ($fight == true && substr($sVal,1) == substr($dVal,1)) {
   $res .= "<tr><td><img src='../img/".$sVal.".png'></td>";
   $res .= "<td><h3> vs. </h3></td>";
   $res .= "<td><img src='../img/".$dVal.".png'></td></tr>";
   $res .= "<tr><td colspan='3' align='center'><h3>Both pieces perish in the battle</h3></td></tr>";
   $res .= "<tr><td colspan='3' align='center'><a href='../user/welcome.php'>Home</a></td></tr>";
} elseif ($fight == true){
   $res .= "<tr><td><img src='../img/".$sVal.".png'></td>";
   $res .= "<td><h3> vs. </h3></td>";
   $res .= "<td><img src='../img/".$dVal.".png'></td></tr>";
   $res .= "<tr><td colspan='3' align='center'><h3>".$rVal." survives the battle</h3></td></tr>";
   $res .= "<tr><td colspan='3' align='center'><a href='../user/welcome.php'>Home</a></td></tr>";
} elseif ($state == 4){
   $res .= "<tr><td><h2>Move Results: </h2></td></tr>";
   $res .= "<tr><td align='center'><img src='../img/".$sVal.".png'></td>";
   $res .= "<tr><td align='center'><h3> $sVal moved to space $sLoc </h3></td></tr>";
   $res .= "<tr><td align='center'><a href='../user/welcome.php'>Home</a></td></tr>";
} elseif ($state == 5){
   $res .= "<tr><td><h2>Move Results: </h2></td></tr>";
   $res .= "<tr><td align='center'><img src='../img/".$sVal.".png'></td>";
   $res .= "<tr><td align='center'><h3>Congratulations $name! $sVal captures the flag! You are the winner!</h3></td></tr>";
   $res .= "<tr><td align='center'><a href='../user/welcome.php'>Home</a></td></tr>";
}
$res .= "</table></div>";

#
# Top html (htmlT) - Adds beginning html code
#
$htmlT = "<!DOCTYPE html><html lang='en'><head>";
$htmlT .= "<link rel='stylesheet' type='text/css' href='../css/board.css'>";
$htmlT .= "<script src='../js/jquery-1.11.2.js'></script>";
$htmlT .= "</head>";
$htmlT .= "<body>";
$htmlT .= "<div id='container'>";

#
# Bottom html - Adds ending html code
#
$htmlB = "</div>";
$htmlB .= "</body>";
$htmlB .= "</html>";

print $htmlT . $res . $htmlB;
#header("refresh:10;url=../user/index.php");
?>