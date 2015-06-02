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

$f = "<form id='userauth' method='POST' action='#'>";
$f .= "<input type='text' id='username' name='username'>";
$f .= "<input type='text' id='password' name='password'>";
$f .= "<button type='submit' id='login'>Login</button>";
$f .= "<button type='submit' formaction='reg.php' id='newuser'>Register</button>";
$f .= "</form>";
print $f;

?>
