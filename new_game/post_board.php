<?php
ini_set('display_errors', 1);

# Authenticate user
session_start();
if (!isset($_SESSION['boardString']) && !isset($_SESSION['auth'])) {
   header("Location: ../user/login.php");
   exit();			      
}

require "../php_includes/db_connect.php";
$name = $_SESSION['auth'];
$bStr = base64_encode($_SESSION['boardString']);//use base64_decode to extract

#
# Create new game in database
# set state to 1 (created)
#
$gameInsert = "INSERT INTO GAME (red, state, lastMoveBy, boardStr, lastMoveRed) 
	       VALUES ('$name','1','$name','$bStr','Created game')";
if (mysqli_query($conn, $gameInsert)) {
    print "<h1>New game created successfully!</h1><br><br>";
    print "<a href='../user/index.php'>Return to your home page</a>";
} else {
    print "Failed to create game.<br>" . mysqli_error($conn);
}

#
# Get current gameID value created from $gameInsert
# Used to identify current game
#
$gidq = "SELECT gameID FROM GAME 
         ORDER BY gameID DESC Limit 1";
$gidres = mysqli_query($conn, $gidq);
$gidrow = $gidres->fetch_array(MYSQLI_NUM);
$gidval = $gidrow[0];
if (mysqli_num_rows($gidres) < 1){
     echo "Could not find any rows for gameID in games";
}


# Turn $_POST into comma seperated string
$stack = [];
foreach($_POST as $key => $value){
   if ($value == ""){
      $value = "Null";
   }
   array_push($stack, "$key $value");
}
$post_Str = implode(',', $stack);
#$post_Str = base64_encode($post_Str);

echo $post_Str;

#
# Insert red values into BOARD
#
$binstmt = $conn->prepare("INSERT INTO BOARD (gameID, redPlayerView) VALUES (?,?)");
$binstmt -> bind_param('is', $gidval, $post_Str);
$binstmt -> execute();
$binstmt -> close();


#debug
#print_r($_POST);

# Unset boardString
$_SESSION['boardString'] = '';
unset($_SESSION['boardString']);
?>