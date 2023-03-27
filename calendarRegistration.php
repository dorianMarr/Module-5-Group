<?php
ini_set("session.cookie_httponly", 1);
session_start(); 
$previous_ua = @$_SESSION['useragent'];
$current_ua = $_SERVER['HTTP_USER_AGENT'];

if(isset($_SESSION['useragent']) && $previous_ua !== $current_ua){
	die("Session hijack detected");
}else{
	$_SESSION['useragent'] = $current_ua;
}
session_name("1");
require 'CalendarDatabaseAccess.php';

$username=htmlentities($_POST['username']);
$password=htmlentities($_POST['password']);

//come back to this ig
// if( !preg_match('/^[\w_\-]+$/', $username) || !preg_match('/^[\w_\-]+$/', $password)){ //some of the input was invalid :(
//     echo("Invalid input, please try again.");
//     exit;
// }
// else //valid input yay
// {
    $password=password_hash($password,PASSWORD_BCRYPT); //gotta be secure!

    $stmt = $mysqli->prepare("INSERT into users (username, hashed_password) values (?, ?)");
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $stmt->close(); //it worked!! YAY
//}
?>