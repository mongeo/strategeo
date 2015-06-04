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
$r_stack = [];
$b_stack = [];
foreach($_POST as $key => $value){
   if ($value == ""){
      $value = "N";
   }
   array_push($r_stack, "$value");
   array_push($b_stack, "$value[0]");
}
$post_r_str = implode(',', $r_stack);
$post_b_str = implode(',', $b_stack);
#$post_Str = base64_encode($post_Str);

echo $post_r_str;
echo "<br>";
echo $post_b_str;

#
# Insert red values into BOARD
# Only show red pieces (not values for blue player's view)
#
$binstmt = $conn->prepare("INSERT INTO BOARD (gameID, redPlayerView, bluePlayerView) VALUES (?,?,?)");
$binstmt -> bind_param('iss', $gidval, $post_r_str, $post_b_str);
$binstmt -> execute();
$binstmt -> close();

#debug
#print_r($_POST);

# Unset boardString
$_SESSION['boardString'] = '';
unset($_SESSION['boardString']);
?>