<?php
$path = $_SERVER['CONTEXT_DOCUMENT_ROOT'] . "/stratego/";

//Create array from data file
//for red game pieces

#
# Create an array of values for red pieces
#
$vals = "../data/vals";
$reds = file($vals, FILE_IGNORE_NEW_LINES);

//Build board table (b)
//M = Map (aka (b)oard)
$b = "<table id='board'>";//Use for init and store string in database
$i = 1;
while($i < 101){
        if($i % 10 == 1){
              $b .= "<tr>";
        }
        if ($i == 43 || $i == 47){
           $b .= "<td background='../img/L1.png'><div id='M".$i."' class='square'></div></td>";
        } else if ($i == 44 || $i == 48){
           $b .= "<td background='../img/L2.png'><div id='M".$i."' class='square'></div></td>";
        } else if ($i == 53 || $i == 57){
           $b .= "<td background='../img/L3.png'><div id='M".$i."' class='square'></div></td>";
        } else if ($i == 54 || $i == 58){
           $b .= "<td background='../img/L4.png'><div id='M".$i."' class='square'></div></td>";
        } else {
           $b .= "<td background='../img/T" . rand(1,3). ".png'><div id='M".$i."' class='clickable square'></div></td>";
        }
        $i++;
        if($i % 10 == 1){
              $b .= "</tr>";
        }
}
$b .= "</table>";
//End build board table

//Red pool

$rP = "<table id='rPool'>";
$i = 0;
while($i < 40){
        if($i % 10 == 0){
              $rP .= "<tr>";
        }
        $rP .= "<td><div id='rS" . $i  . "' class='clickable square'>";
        $rP .= "<img src='../img/R" . $reds[$i] . ".png' id='R".$reds[$i]."l".$i."' class='clickable square'>";
        $rP .= "</div></td>";

        $i++;
        if($i % 10 == 0){
              $rP .= "</tr>";
        }
}
$rP .= "</table>";

//End red pool

//Blue pool

$bP = "<table id='bPool'>";
$i = 0;
while($i < 40){
        if($i % 10 == 0){
              $bP .= "<tr>";
        }
        $bP .= "<td><div id='bS" . $i  . "' class='clickable square'>";
        $bP .= "<img src='../img/B" . $blues[$i] . ".png' id='B".$blues[$i]."l".$i."' class='clickable square'>";
        $bP .= "</div></td>";

        $i++;
        if($i % 10 == 0){
              $bP .= "</tr>";
        }
}
$bP .= "</table>";

//End blue pool

//Create hidden form and append to h (f)
$f = "";
for ($i = 1; $i < 102; $i++){
    $f .= "<input type='hidden' id='F" . $i . "' name='M" . $i . "' value=''>";
}
//End hidden form

//Begin header table (h)
$h = "<div id='header'>";
$h .= "<h1>Stratego</h1>";
$h .= "<br><div id='headerText'> Welcome " . ucfirst($name) . "! Place your pieces on the board. Click ready when button appears <form id='readyForm' action='newgamepost.php' method='POST'>" . $f . "<div id='readyButton'></div></form></div></div>";
//End header table

//Build side bar

$rSide = "<div id='rSide'>";

$sImg = "<div id='sImg' class='square'>";
$sImg .= "";
$sImg .= "</div>";

$sMsg = "<div id='sMsg'>";
$sMsg .= "Select your piece to move";
$sMsg .= "</div>";

$sPhase = "<div id='sPhase'>";
$sPhase .= "<b>Phase:</b><br>Game creation";
$sPhase .= "</div>";

$sLastMove = "<div id='sLastMove'>";
$sLastMove .= "<b>Last move:</b><br>";
$sLastMove .= "By Megan<br>";
$sLastMove .= "R41 -> R51<br>";
$sLastMove .= "@ 2/5/84 2pm PST";
$sLastMove .= "</div>";

$sSignout = "<div id='sSignout'>";
$sSignout .= "<a href='../user/signout.php'>Signout</a>";
$sSignout .= "</div>";


$rSide .= $sImg . $sMsg . $sPhase . $sLastMove . $sSignout;
$rSide .= "</div>";
//

//Demarcation lines
$rLine = "<div id='rLine'>Red Zone</div>";
$bLine = "<div id='bLine'>Blue Zone</div>";
//End demarcation


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
$b = game board
$f = hidden form appended to h
$h = header
$rSide = right side bar
$htmlB = bottom html
*/

//Echo red board string
$rGBStr = $htmlT . $h . $b . $rP . $bP . $rSide . $rLine . $bLine . $htmlB;
echo $rGBStr;

//Get blue side board and board
//To store in database and use for next player
//$bGBStr = $b . $s2;
//$_SESSION['bStr'] = $bGBStr;
$boardStr = $b . $rSide . $rLine . $bSide . $bLine;
$_SESSION['boardString'] = $boardStr;

?>