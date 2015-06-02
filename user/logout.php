<?php
#
# Destroy session
#
session_start();
$_SESSION = array();
if (ini_get("session.use_cookies")) {
   $params = session_get_cookie_params();
   setcookie(session_name(), '', 1, $params["path"], $params["domain"],$params["secure"], $params["httponly"]);
}
session_unset();
session_destroy();
$_POST = array();
header("refresh:5;url=login.php");
print "You have been logged out. Redirecting to login page in 3 seconds. . .";
exit();
?>

