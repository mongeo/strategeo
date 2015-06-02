<?php
ini_set('display_errors', 1);

//Check if authenticated
session_start();
if (isset($_SESSION['auth'])){
   print "You have an open session as " . $_SESSION['auth'] . ". Cannot create an additional account. ";
   print "Redirecting to home in 5 seconds.";
   header("refresh:5;url=welcome.php");
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


$f = "<form id='newuserform' method='POST' action='#'>";
$f .= "<input type='text' name='username' placeholder='Username'>";
$f .= "<input type='text' name='password' placeholder='Password'>";
$f .= "<input type='text' name='password_repeat' placeholder='Repeat Password'>";
$f .= "<button type='submit'>Create account</button>";
$f .= "</form>";
print $f;
print $msg;
echo "<br><a href='index.php'>Home</a>";

}

?>