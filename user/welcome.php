<?php
ini_set('display_errors', 1);

//Check if authenticated
session_start();
if (isset($_SESSION['auth']) == false){
   header("Location: login.php");
   exit();
} else {
   require '../php_includes/db_connect.php';
   $name = $_SESSION['auth'];

   #
   # Create html table for user's current games
   #
   $gStr = "<table><tr><th>GameID</th><th>Red</th><th>Blue</th><th>Turn</th><th>Last Move</th></tr>";
   $gquery = "SELECT gameID, red, blue, state, lastMoveBy, lastMoveTime 
   	     	     FROM games 
		     WHERE red='".$name."' OR blue='".$name."' 
		     AND state < 0 AND state <= 3";
   $gameN = 0;//number of total current games
   if ($gstmt = $conn->prepare($gquery)){
      $gstmt->execute();
      $gstmt->bind_result($gid,$red,$blue,$state,$lmb,$lmt);
      while ($gstmt->fetch()){
      	    $gameN++;
            $gStr .= "<tr><td><a href='#'>$gid</a></td>";
            $gStr .= "<td>$red</td>";
            $gStr .= "<td>$blue</td>";
            if ($state == 1){
               $gStr .= "<td>Awaiting Player</td>";
            } else if ($name == $lmb){
               $gStr .= "<td>Yours</td>";
            } else {
               $gStr .= "<td>Theirs</td>";
            }
	    $gStr .= "<td>$lmt</td>";
            $gStr .= "</tr>";
      }
      $gstmt->close();
   }
   $gStr .= "</table>";

   #
   # Create table of games that are awaiting players
   #
   $cStr = "<table><tr><th>GameID</th><th>Player</th></tr>";
   $cquery = "SELECT gameID, red 
   	      FROM games 
	      WHERE state='1' AND red<>'".$name."' 
	      ORDER BY lastMoveTime LIMIT 10";
   if ($cstmt = $conn->prepare($cquery)){
      $cstmt->execute();
      $cstmt->bind_result($cid,$cr);
      while ($cstmt->fetch()){
         $cStr .= "<tr><td><a href='../joingame/joingameboard.php?gid='$cid'>$cid</a></td>";
         $cStr .= "<td>$cr</td>";
         $cStr .= "</tr>";
      }
         $cstmt->close();
   }
   $cStr .= "</table>";

   #
   # Create header string
   #
   $h = "";
   $h .= "<h1>Welcome ". ucfirst(strtolower($name))  ."!</h1>";
   $h .= "<h2>Your Current Games: $gameN / 3</h2>";
   $h .= "$gStr";
   if ($gameN < 4){
      $h .= "<br><form action='../new_game/index.php' method='POST'>";
      $h .= "<input type='submit' value='Create A Game!' name='createGame'></form>";
   }
   $h .= "<h2>Open Games:</h2>";
   $h .= "$cStr";
   if ($gameN < 4){
      $h .= "<br><form action='../join_game/index.php' method='POST'>";
      $h .= "<input type='submit' value='Join A Game!' name='joinGame'></form>";
   }
   $h .= "<form action='logout.php' method='POST'>";
   $h .= "<button id='logout' value='logout' name='logout'>Logout</button></form>";
   echo $h;
}
?>