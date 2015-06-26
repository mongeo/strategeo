<?php
ini_set('display_errors', 1);

//Check if authenticated
session_start();
if (isset($_SESSION['auth'])){
   print "You have an open session as " . $_SESSION['auth'] . ". Cannot create an additional account. ";
   print "Redirecting to home in 3 seconds.";
   header("refresh:3;url=welcome.php");
   exit();
} else {

   #Require database connection file
   require '../php_includes/db_connect.php';

   $success = False;
   $msg = "";

   #
   # Checks submited user creation values
   # If valid will add user to database
   #
   if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['password_repeat']) && isset($_SESSION['created']) == false){
      $pw = strip_tags($_POST['password']);
      $user = strip_tags($_POST['username']);

      #
      # Check if username already exists in database
      #
      $stmt = $conn->prepare("SELECT * FROM USER where Username= ?");
      $stmt -> bind_param("s", $name);           
      $name = $user;
      $stmt->execute();
      $found = False;
      while($stmt->fetch()){
	$found = True;
      }  
      $stmt->close();

      #
      # Simple form validation
      # Passwords must match, be >= 8 characters,
      #  user must be unique, > 1 character <= 64 
      #
      if ($pw != $_POST['password_repeat']){
      	 $msg = "Passwords did not match";
      } elseif (strlen($pw) < 8 ){
      	 $msg = "Password must be at least 8 characters long";
      } elseif (strlen($user) < 1 || strlen($user) > 64) {
      	 $msg = "Username must be between 1 and 64 characters";
      } elseif ($found == True) {
      	 $msg = "User already exists";
      } else {
      	 #	  
      	 # Store username, password, salt in db
      	 # Uses SHA-512 with randomly generated salt
      	 #
      	 if ($stmt = $conn->prepare("INSERT INTO USER (Username, Password, Salt) VALUES (?,?,?)")){
            $stmt -> bind_param("sss", $user, $hashed, $s);
            $s = substr(base64_encode(mcrypt_create_iv(24, MCRYPT_DEV_URANDOM)), 0, 16);
            $hashed = crypt($pw, '$6$'.$s.'$');
            $stmt->execute();
            $success = True;
            $msg = "Account created. Redirecting to log in page in 5 seconds. . .";
	    $_SESSION['created'] = true;
            $stmt->close();
	    header("refresh:5;url=login.php");
      	 } else {
            printf("Prepared Statement Error: %s\n", $mysqli->error);
      	 }
   }
   # Clears $_POST array
   $_POST = array();
} elseif (isset($_SESSION['created'])){
   $msg = "You\'ve already created an account this session.";
}

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
    <title>Geoffrey Montague - Home</title>

    <!-- Bootstrap core CSS -->
    <link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../css/login.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
  <h1 id="welcome">Welcome to Strategeo</h1>

  <form class="form-horizontal" id="login_box" method='POST' action='#'>
        <div class="form-group">
             <label for="username" class="col-sm-2 control-label">Username</label>
             <div class="col-sm-6">
                  <input type="text" class="form-control" id="username" name='username' placeholder="Username">
             </div>
        </div>
        <div class="form-group">
             <label for="password" class="col-sm-2 control-label">Password</label>
             <div class="col-sm-6">
                  <input type="password" class="form-control" id="password" name="password" placeholder="Password">
             </div>
        </div>
        <div class="form-group">
             <label for="password_repeat" class="col-sm-2 control-label">Repeat Password</label>
             <div class="col-sm-6">
                  <input type="password" class="form-control" id="password_repeat" name="password_repeat" placeholder="Password">
             </div>
        </div>
        <div class="form-group">
             <div class="col-sm-offset-2 col-sm-6">
                  <button type="submit" class="btn btn-default">Create Account</button>
             </div>
        </div>
	<div><?php print $msg ?><br><a href="login.php">Home</a></div>
  </form>

    <!-- Bootstrap core JavaScript                  
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../../cover/jquery.min.js"></script>
    <script src="../../bootstrap/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../cover/ie10-viewport-bug-workaround.js"></script>


  </body>
</html>

