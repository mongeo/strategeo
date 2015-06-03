<?php
ini_set('display_errors', 1);

$path = $_SERVER['CONTEXT_DOCUMENT_ROOT'] . "/stratego/";
include $path . "php_includes/functions.php";
include $path . "php_includes/verifyname.php";

if (!isset($_SESSION['bStr'])) {
   header("Location: http://jeff.cis.cabrillo.edu/~gimontague/stratego/user/index.php");
   exit();			      
}


$name = mysql_real_escape_string($name);
$bStr = mysql_real_escape_string($_SESSION['bStr']);

//Create new game in database
//state set to 1 (=created)
$gameInsert = "INSERT INTO games (red, state, lastMoveBy, boardString) VALUES ('$name','1','$name','$bStr')";
if (mysqli_query($conn, $gameInsert)) {
    echo "<h1>New game created successfully!</h1><br><br><a href='../user/index.php'>Return to your home page</a>";
} else {
    echo "Failed to create game. Email administrator for resolution.";
}
//

//Get current gameID value
$gidq = "SELECT gameID FROM games ORDER BY gameID DESC Limit 1";
$gidres = mysqli_query($conn, $gidq);
$gidrow = $gidres->fetch_array(MYSQLI_NUM);
$gidval = $gidrow[0];
if (mysqli_num_rows($gidres) < 1){
     echo "Could not find any rows for gameID in games";
}
//End get gameID value

//Insert values into board
$binstmt = $conn->stmt_init();
$binstmt -> prepare("INSERT INTO board (gameID, gamePiece, location) VALUES (?,?,?)");
foreach($_POST as $key => $value){
  $binstmt -> bind_param('iss', $gidval, $value, $key);
  $binstmt -> execute();
}
$binstmt -> close();

//End insert into board


//unset board string
$_SESSION['bStr'] = '';
unset($_SESSION['bStr']);

?>
