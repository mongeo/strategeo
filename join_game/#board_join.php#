<?php
ini_set('display_errors', 1);

#
# Check if user is authenticated
#
if (!isset($_SESSION['auth']){
   header("Location: ../user/login.php");
   exit();
   #$gid = $_GET['gid'];//gameID... temporary for testing
} 

//Update games database
//state set to 2 (=joined)
$gameInsert = "UPDATE games SET blue='$name', state='2', lastMoveBy='$name' WHERE gameID='$gid'";
if (mysqli_query($conn, $gameInsert)) {
    echo "Successfuly joined Game $gid";
} else {
    echo "Failed to join game.<br>" . mysqli_error($conn);
}
//End update games

//Build game board 
$b = "";
if ($gstmt = $conn->prepare("SELECT boardString FROM games WHERE gameID=?")){
   $gstmt -> bind_param('i', $gid);
   $gstmt -> execute();
   $gstmt -> bind_result($board);
   $gstmt -> fetch();
   $b = $board;
   $gstmt -> close();
} else {
  echo "Couldn't connect<br> " . mysqli_error($conn);
}
//End get game board

//Begin header table (h)

//Create hidden form and append to h (f)
$f = "";
for ($i = 1; $i < 102; $i++){
    $f .= "<input type='hidden' id='F" . $i . "' name='B" . $i . "' value=''>";
}
//End hidden form

$h = "<table id='header'>";
$h .= "<tr><td><h2>Stratego</h2>";
$h .= "Welcome " . ucfirst($name) . "! Place your pieces on the board, and click the ready button when it appears to start. <form action='joingamepost.php' method='POST'>" . $f . "<div id='readyButton'></div></form> Or <a href='../user/signout.php'>Signout</a></td></tr>";
$h .= "</table>";
//End header table

//Build message table
$m = "<table id='cornerBox'><tr><td><div id='selected'></div><div id='mbox'></div></td></tr></table>";
//End message table

//Build top html (htmlT)
$htmlT = "<!DOCTYPE html><html lang='en'><head>";
$htmlT .= "<link rel='stylesheet' type='text/css' href='../css/board.css'>";
$htmlT .= "<script src='../js/jquery-1.11.2.js'></script>";
$htmlT .= "<script src='../js/newgameboard.js'></script>";
$htmlT .= "</head>";
$htmlT .= "<body>";
$htmlT .= "<div id='container'>";
//End top html


//Build bottom html (htmlB)
$htmlB = "</div>";
$htmlB .= "</body>";
$htmlB .= "</html>";
//End bottom html

/*String Key:
$htmlT = top html
$f = hidden form appended to h
$h = header
$m = message box
$htmlB = bottom html
*/

//Echo blue board string
$bGBStr = $htmlT . $h . $m . $htmlB;
echo $bGBStr;

?>
