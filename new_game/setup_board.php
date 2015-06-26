<?php
#
# Authenticate user
#
session_start();
if (!isset($_SESSION['auth'])){
   header("Location: ../user/login.php");
}
$name = $_SESSION['auth'];

#
# Create an array of values for red pieces
#
$vals = "../data/vals";
$reds = file($vals, FILE_IGNORE_NEW_LINES);

#
# Build board table (b)
#
$b = "<table id='board'>";
$i = 1;
while($i < 101){
        if($i % 10 == 1){
              $b .= "<tr>";
        }
        if ($i == 43 || $i == 47){
           $b .= "<td background='../img/L1.png'><div id='M".$i."' class='square lake'></div></td>";
        } else if ($i == 44 || $i == 48){
           $b .= "<td background='../img/L2.png'><div id='M".$i."' class='square lake'></div></td>";
        } else if ($i == 53 || $i == 57){
           $b .= "<td background='../img/L3.png'><div id='M".$i."' class='square lake'></div></td>";
        } else if ($i == 54 || $i == 58){
           $b .= "<td background='../img/L4.png'><div id='M".$i."' class='square lake'></div></td>";
        } else {
           $b .= "<td background='../img/T" . rand(1,3). ".png' id='B".$i."'><div id='M".$i."' class='clickable square'></div></td>";
        }
        $i++;
        if($i % 10 == 1){
              $b .= "</tr>";
        }
}
$b .= "</table>";

#
# Red pool - Area that stores red peices for board placement
#
$rP = "<table id='rPool'>";
$i = 0;
while($i < 40){
        if($i % 4 == 0){
              $rP .= "<tr>";
        }
        $rP .= "<td><div id='rS" . $i  . "' class='clickable square'>";
        $rP .= "<img src='../img/R" . $reds[$i] . ".png' id='R".$reds[$i]."_".$i."' class='clickable square'>";
        $rP .= "</div></td>";

        $i++;
        if($i % 4 == 0){
              $rP .= "</tr>";
        }
}
$rP .= "</table>";

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
#
$h = "<div id='header'>";
$h .= "<h1>Stratego</h1>";
$h .= "<br><div id='headerText'> Welcome " . ucfirst($name) . "! ";
$h .= "Place your pieces on the board. Click ready when button appears ";
$h .= "<form id='readyForm' action='post_board.php' method='POST'>";
$h .=  $f . "<div id='readyButton'></div></form></div></div>";

#
# Sidebar - Displays game information
#
$rSide = "<div id='rSide'>";

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
$sPhase .= "<b>Phase:</b><br>Game creation";
$sPhase .= "</div>";

#Displays last move information
$sLastMove = "<div id='sLastMove'>";
$sLastMove .= "<b></b><br>";//Last Move:
$sLastMove .= "<br>";//By Geoff
$sLastMove .= "<br>";//R41 -> R51
$sLastMove .= "";//@ 6/1/2015 2pm PST
$sLastMove .= "</div>";

#Logs off user
$sSignout = "<div id='sSignout'>";
$sSignout .= "<form action='../user/logout.php' method='POST'>";
$sSignout .= "<button id='logout' value='logout' name='logout'>Logout</button></form>";
$sSignout .= "</div>";

#Combines sidebar string
$rSide .= $sImg . $sMsg . $sPhase . $sLastMove . $sSignout;
$rSide .= "</div>";

# 
# Top html (htmlT) - Adds beginning html code
#
$htmlT = "<!DOCTYPE html><html lang='en'><head>";
$htmlT .= "<link rel='stylesheet' type='text/css' href='../css/board.css'>";
$htmlT .= "<script src='../js/jquery-1.11.2.js'></script>";
$htmlT .= "<script src='../js/new_game_board.js'></script>";
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
$sbg = "<div id='s_board_border'></div>";

#
# Prints entire string for the board
# 
$rGBStr = $htmlT . $h . $b . $rP . $rSide . $bg . $sbg . $htmlB;
echo $rGBStr;

#
# Board string - Saves randomly generated terrain 
#
$_SESSION['boardString'] = $b;
?>