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
} else {
   print "No game selected. Redirecting in 3 seconds. . . ";
   header("refresh:3;url=../user/index.php");
   exit();
}

require "../php_includes/db_connect.php";

#
# Get game information and see if it is joinable
#
$red =  "";
$blue = "";
$state = "";
$board = "";
$lastMoveBy = "";
$lastMoveTime = "";
$lastMove = "";
$redPlayerView = "";
$bluePlayerView = "";
if ($gstmt = $conn->prepare(
	"SELECT red, blue, state, boardStr, lastMoveBy, lastMoveTime, lastMove, redPlayerView, bluePlayerView
	 FROM GAME
	 LEFT JOIN BOARD ON GAME.gameID=BOARD.gameID
	 WHERE GAME.gameID=?")){
   $gstmt -> bind_param('i', $gid);
   $gstmt -> execute();
   $gstmt -> bind_result($re, $bl, $s, $b, $lmby, $lmt, $lm, $rpv, $bpv);
   $gstmt -> fetch();
   $red = $re;
   $blue = $bl;
   $state = $s;
   $board = $b;
   $lastMoveBy = $lmby;
   $lastMoveTime = $lmt;
   $lastMove = $lm;
   $redPlayerView = $rpv;
   $bluePlayerView = $bpv;
   $gstmt -> close();
} else {
  echo "Couldn't connect<br> " . mysqli_error($conn);
}
if ($state == 0){
   print "Cannot join game: Not yet initialized. "; 
   print "Redirecting in 3 seconds. . . ";
   header("refresh:3;url=../user/index.php");
   exit();
} elseif ($state < 3){
   print "Cannot load game: Game has not been fully initiated. "; 
   print "Redirecting in 3 seconds. . . ";
   header("refresh:3;url=../user/index.php");
   exit();
} elseif ($state > 4){ //Needed?
   print "Error?";
}

#var_dump($redPlayerView);
#var_dump($bluePlayerView);
#exit();

# Get current color
$color = "";
if ($name == $red){
   $color = "Red";
} elseif ($name == $blue){
   $color = "Blue";
} else {
   $color = "Error: Player's color could not be determined";
}

#
# Get game piece locations and values place them into divs
#  which will be pulled into javascript array and used
#  for gameplay
#
$red_pieces = explode(',', $redPlayerView);
$blue_pieces = explode(',', $bluePlayerView);
$divs = "";
for ($i = 1; $i < 101; $i++){
    if (strlen($red_pieces[$i]) > 1){
       $divs .= "<div id='T$i' style='display:none'>$red_pieces[$i]</div>";
    } elseif (strlen($blue_pieces[$i]) > 1){
       $divs .= "<div id='T$i' style='display:none'>$blue_pieces[$i]</div>";
    } elseif ($red_pieces[$i] == 'X') { //Should be the same in either red or blue view
       $divs .= "<div id='T$i' style='display:none'>X</div>";
    } else {
       $divs .= "<div id='T$i' style='display:none'>N</div>";
    }
}
echo $divs;

#
# Create hidden form and append to h (f)
# used to post data
#
$f = "";
$f .= "<input type='hidden' id='sLoc' name='sLoc' value=''>";
$f .= "<input type='hidden' id='dLoc' name='dLoc' value=''>";
$f .= "<input type='hidden' id='sVal' name='sVal' value=''>";
$f .= "<input type='hidden' id='dVal' name='dVal' value=''>";
$f .= "<input type='hidden' id='rVal' name='rVal' value=''>";
$f .= "<input type='hidden' id='gameArray' name='gameArray' value=''>";

#
# Header - Displays welcome message and ready button
# for when user has placed all pieces on board
#
$h = "<div id='header'>";
$h .= "<h1>Stratego</h1>";
$h .= "<br><div id='headerText'> Welcome <span id='user_name'>" . ucfirst($name) . "</span>! ";
$h .= "Move your piece and confirm move when button appears.  ";
$h .= "<form id='readyForm' action='post_load.php' method='POST'>";
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
$sPhase .= "<b>Game State (<span id='state_num'>$state</span>)</b><br>";
$sPhase .= ucfirst($name) . "'s Move (<span id='player_color'>".$color."</span>)";
$sPhase .= "</div>";

#Displays last move information
$sLastMove = "<div id='sLastMove'>";
$sLastMove .= "<b>Last Move:</b><br>";
$sLastMove .= "By " . ucfirst($lastMoveBy) . "<br>";
$sLastMove .= "$lastMove @:<br>";
$sLastMove .= "$lastMoveTime <br>";
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
$htmlT .= "<script src='../js/load_game_board.js'></script>";
$htmlT .= "</head>";
$htmlT .= "<body>";
$htmlT .= "<div id='container'>";

#
# Bottom html - Adds ending html code
#
$htmlB = "</div>";
$htmlB .= "</body>";
$htmlB .= "</html>";

$bg = "<div id='board_border'></div>";

#
# Prints entire string for the board
#
$bGBStr = $htmlT . $h . base64_decode($board) . $side . $bg . $htmlB;
echo $bGBStr;

$_SESSION['gid'] = $gid;
?>
