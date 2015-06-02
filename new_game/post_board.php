<?php
ini_set('display_errors', 1);

if (!isset($_SESSION['boardString']) || !isset($_SESSION['auth'])) {
   header("Location: ../user/index.php");
   exit();			      
}

require "../php_includes/db_connect.php";
$name = mysql_real_escape_string($_SESSION['auth']);
$bStr = mysql_real_escape_string($_SESSION['boardString']);

#
# Create new game in database
# set state to 1 (created)
#
$gameInsert = "INSERT INTO games 
	      	      (red, state, lastMoveBy, boardStr, lastMoveRed) 
	       VALUES 
	       	      ('$name','1','$name','$bStr','Created game')";
if (mysqli_query($conn, $gameInsert)) {
    print "<h1>New game created successfully!</h1><br><br>";
    print "<a href='../user/index.php'>Return to your home page</a>";
} else {
    print "Failed to create game.<br>" . mysqli_error($conn);
}

#
# Get current gameID value
#
$gidq = "SELECT gameID FROM games 
         ORDER BY gameID DESC Limit 1";
$gidres = mysqli_query($conn, $gidq);
$gidrow = $gidres->fetch_array(MYSQLI_NUM);
$gidval = $gidrow[0];
if (mysqli_num_rows($gidres) < 1){
     echo "Could not find any rows for gameID in games";
}


print_r($_POST);

#
# Insert values into board
#
$binstmt = $conn->stmt_init();
$binstmt -> prepare("INSERT INTO board (gameID, redPlayer, location) VALUES (?,?,?)");
foreach($_POST as $key => $value){
  $binstmt -> bind_param('iss', $gidval, $value, $key);
  $binstmt -> execute();
}
$binstmt -> close();

# Unset boardString
$_SESSION['boardString'] = '';
unset($_SESSION['boardString']);
?>