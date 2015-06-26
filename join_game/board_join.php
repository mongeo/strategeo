<?php
ini_set('display_errors', 1);

#
# Authenticate user
#
session_start();
if (!isset($_SESSION['auth'])){
   header("Location: ../user/login.php");
   exit();
}
# Get user's name
$name = $_SESSION['auth'];

#
# Check if gid was set via $_GET
#  or if a session exists
#  and set as appropriate
#
$gid = "";
if (isset($_GET['gid'])){
   $gid = $_GET['gid'];
} elseif(!isset($_SESSION['gid'])) {
   $gid = $_SESSION['gid'];
} else {
   print "No game selected. Redirecting in 3 seconds. . . ";
   header("refresh:3;url=../user/index.php");
   exit();
}

$vals = "../data/vals";
$blues = file($vals, FILE_IGNORE_NEW_LINES);

require "../php_includes/db_connect.php";

#
# Get game information and see if it is joinable
#
$board = "";
$state = "";
$red = "";
$time = "";
if ($gstmt = $conn->prepare("SELECT red, boardStr, state, lastMoveTime FROM GAME  WHERE gameID=?")){
   $gstmt -> bind_param('i', $gid);
   $gstmt -> execute();
   $gstmt -> bind_result($r, $b, $s, $t);
   $gstmt -> fetch();
   $board = $b;
   $state = $s;
   $red = $r;
   $time = $t;
   $gstmt -> close();
} else {
  echo "Couldn't connect<br> " . mysqli_error($conn);
}
if ($state == 0){
   print "Cannot join game: Not yet initialized. "; 
   print "Redirecting in 3 seconds. . . ";
   header("refresh:3;url=../user/index.php");
   exit();
} elseif ($state > 2){
   print "Cannot join game: Already joined. "; 
   print "Redirecting in 3 seconds. . . ";
   header("refresh:3;url=../user/index.php");
   exit();
}

#
# Update games database
#  set state to 2 (Blue's selection) and add blue's name 
#
$gameUpdate = "UPDATE GAME 
	       SET blue='$name', state='2', lastMove='$name joined game', lastMoveBy='$name' 
	       WHERE gameID='$gid'";
if (!mysqli_query($conn, $gameUpdate)) {
    echo "Failed to join game.<br>" . mysqli_error($conn);
}

#
# Blue pool - Area to store blue pieces for board placement
#
$bP = "<table id='bPool'>";
$i = 0;
while($i < 40){
        if($i % 4 == 0){
              $bP .= "<tr>";
        }
        $bP .= "<td><div id='bS" . $i  . "' class='clickable square'>";
        $bP .= "<img src='../img/B" . $blues[$i] . ".png' id='B".$blues[$i]."_".$i."' class='clickable square'>";
        $bP .= "</div></td>";

        $i++;
        if($i % 4 == 0){
              $bP .= "</tr>";
        }
}
$bP .= "</table>";

#
# Create hidden form and append to h (f)
# used to store placements in database
#
$f = "";
for ($i = 1; $i < 101; $i++){
    $f .= "<input type='hidden' id='F" . $i . "' name='M" . $i . "' value=''>";
}

#
# Header - Displays welcome message and ready button
# for when user has placed all pieces on board
#80
$h = "<div id='header'>";
$h .= "<h1>Stratego</h1>";
$h .= "<br><div id='headerText'> Welcome " . ucfirst($name) . "! ";
$h .= "Place your pieces on the board. Click ready when button appears ";
$h .= "<form id='readyForm' action='post_join.php' method='POST'>";
$h .=  $f . "<div id='readyButton'></div></form></div></div>";

#
# Sidebar - Displays game information
#
$side = "<div id='rSide'>";

#Displays selected piece
$sImg = "<div id='sImg' class='square'>";
$sImg .= "";
$sImg .= "</div>";

#Displays status message of selection
$sMsg = "<div id='sMsg'>";
$sMsg .= "Select your piece to move";
$sMsg .= "</div>";

#Displays current phase of the game
$sPhase = "<div id='sPhase'>";
$sPhase .= "<b>Phase:</b><br>Join Game";
$sPhase .= "</div>";

#Displays last move information
$sLastMove = "<div id='sLastMove'>";
$sLastMove .= "<b>Last Move: </b><br>";
$sLastMove .= "By " . ucfirst($red)."<br>";
$sLastMove .= ucfirst($red) . " created game<br>";
$sLastMove .= "$time";
$sLastMove .= "</div>";

#Logs off user
$sSignout = "<div id='sSignout'>";
$sSignout .= "<form action='../user/logout.php' method='POST'>";
$sSignout .= "<button id='logout' value='logout' name='logout'>Logout</button></form>";	
$sSignout .= "</div>";

#Combines sidebar string
$side .= $sImg . $sMsg . $sPhase . $sLastMove . $sSignout;
$side .= "</div>";

#
# Top html (htmlT) - Adds beginning html code
#
$htmlT = "<!DOCTYPE html><html lang='en'><head>";
$htmlT .= "<link rel='stylesheet' type='text/css' href='../css/board.css'>";
$htmlT .= "<script src='../js/jquery-1.11.2.js'></script>";
$htmlT .= "<script src='../js/join_game_board.js'></script>";
$htmlT .= "</head>";
$htmlT .= "<body>";
$htmlT .= "<div id='container'>";

#
# Bottom html - Adds ending html code
#
$htmlB = "</div>";
$htmlB .= "</body>";
$htmlB .= "</html>";

#
# Prints entire string for the board
#
echo $htmlT . $h . base64_decode($board) . $bP . $side .  $htmlB;

$_SESSION['gid'] = $gid;
?>
