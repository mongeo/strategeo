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
   # Create html table for user's active games
   #
   $gStr = "<div id='current_games'><br><table id='current_games_table'><tr>" . 
   	   "<th>Game ID</th>" .
   	   "<th>Red</th>" .
	   "<th>Blue</th>" .
	   "<th>Turn</th>" .
	   "<th>Last Move</th>" .
	   "<th>Time of Move</th>";
   $gquery = "SELECT gameID, red, blue, state, lastMove, lastMoveBy, lastMoveTime 
   	      FROM GAME 
	      WHERE state < 5 AND state > 0 
              AND (red='".$name."' OR blue='".$name."')";
   $activeGameN = 0;//number of open games
   if ($gstmt = $conn->prepare($gquery)){
      $gstmt->execute();
      $gstmt->bind_result($gid,$red,$blue,$state,$lm, $lmb,$lmt);
      while ($gstmt->fetch()){
      	    $activeGameN++;	
            $gStr .= "<tr><td>$gid</td>";
            $gStr .= "<td>$red</td>";
            $gStr .= "<td>$blue</td>";
            if ($state == 1){
               $gStr .= "<td>Awaiting Player</td>";
            } elseif ($state == 2 && $name == $blue){
               $gStr .= "<td><a href='../join_game/board_join.php?gid=".$gid."'>Set up board!</a></td>"; 
	    } elseif ($state > 2 && $lmb != $name) {
               $gStr .= "<td><a href='../load_game/board_load.php?gid=".$gid."'>Make a move!</a></td>";
            } else {
               $gStr .= "<td>Other player's turn</td>";
            }
	    $gStr .= "<td>$lm</td>";
	    $gStr .= "<td>$lmt</td>";
            $gStr .= "</tr>";
      }
      $gstmt->close();
   }
   $gStr .= "</table><br></div>";

   #
   # Create table of games that are awaiting players
   #
   $cStr = "<div id='open_games'><table id='open_games_table'><tr>" .
   	   "<th>Game ID</th>" .
	   "<th>Player</th>" .
	   "<th>Click to Join</th></tr>";
   $cquery = "SELECT gameID, red 
   	      FROM GAME 
	      WHERE state='1' AND red<>'".$name."' 
	      ORDER BY lastMoveTime LIMIT 10";
   if ($cstmt = $conn->prepare($cquery)){
      $cstmt->execute();
      $cstmt->bind_result($cid,$cr);
      $openGameN = 0;//number of total current games	
      while ($cstmt->fetch()){
      	 $openGameN++;
         $cStr .= "<tr><td>$cid</td>";
         $cStr .= "<td>$cr</td>";
         $cStr .= "<td><a href='../join_game/board_join.php?gid=$cid'>Join Game!</a></td>";
         $cStr .= "</tr>";
      }
         $cstmt->close();
   }
   $cStr .= "</table></div>";

   #
   # Create header string
   #
   $h = "";
   $h .= "<div id='welcome_header'><h1>Welcome ". ucfirst(strtolower($name))  ."!</h1></div>";
   $h .= "<h2>Your Active Games:</h2>";
   if ($activeGameN == 0){
       $h .= "<h4>You have no active games.</h4>";
   }

   $h .= "$gStr";
   if ($activeGameN < 4){
      $h .= "<br><form action='../new_game/index.php' method='POST'>";
      $h .= "<input type='submit' value='Create A Game!' name='createGame'></form>";
   } else {
      $h .= "<h4>You have reached the max amount of open games. Please finish a current game to create a new one.</h4>";
   }  
   $h .= "<h2>Open Games:</h2>";
   if ($openGameN == 0){
       $h .= "<h4>There are no open games.</h4>";
   }  
   else if ($openGameN < 4 && $openGameN > 0){
       $h .= "$cStr";  
   }
   else {
       $h .= "<h4>You have reached the max amount of open games. Please finish a current game to join another game.</h4>";
   }  
   $h .= "<br><form action='logout.php' method='POST'>";
   $h .= "<button id='logout' value='logout' name='logout'>Logout</button></form>";
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Geoffrey Montague - Strategeo</title>

    <!-- Bootstrap core CSS -->
    <link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../css/welcome.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>      
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
    <?php
	echo $h; 
    ?>
    
    </div>
    <!-- Bootstrap core JavaScript                                                                                                                             
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../../cover/jquery.min.js"></script>
    <script src="../../bootstrap/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../cover/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>