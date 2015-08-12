<?php
ini_set('display_errors', 1);

//Check if authenticated
session_start();
if (isset($_SESSION['auth'])){
   header("Location: welcome.php");
   exit();
} else {

   #Require database connection file
   require '../php_includes/db_connect.php';
   
   #Check if logout button was click
   #If so end session / deauthenticate
   if (isset($_POST['logout'])){
      $_SESSION = array();	
      if (ini_get("session.use_cookies")) {
      	 $params = session_get_cookie_params();
      	 setcookie(session_name(), '', 1, $params["path"], $params["domain"],$params["secure"], $params["httponly"]);
      }
      session_destroy();
      $_POST = array();
   }

   #Check if username and password were posted and user not authenticated
   #If so verify if user / password combination exists
   #If so authenticate user and store username in $_SESSION['auth']
   if (isset($_POST['username']) and isset($_POST['password']) and isset($_SESSION['auth']) != True){
      $pw = strip_tags($_POST['password']);
      $name = strip_tags($_POST['username']);
      $stmt = $conn->prepare("SELECT Password, Salt FROM USER WHERE Username=?");
      $stmt->bind_param('s', $n);
      $n = $name;
      $stmt->execute();
      $stmt->bind_result($p, $s);
      $stmt->fetch();
      $hashed = crypt($pw, '$6$'.$s.'$');
      if ($p == $hashed){
      	 $_SESSION['auth'] = $n;
	 header("Location: welcome.php");
	 exit();
      } else {
         printf("<p>Wrong username / password combination</p>");
      }
      $stmt -> close();
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

  <form action="#" method="POST" class="form-horizontal" id="login_box">
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
    	     <div class="col-sm-offset-2 col-sm-6">
  	     	  <button type="submit" id="login" class="btn btn-default">Login</button>
  		  <button type="submit" formaction="reg.php" id="newuser" class="btn btn-default">Register</button>
    	     </div>
        </div>
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
