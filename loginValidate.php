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

// Use a prepared statement
$stmt = $mysqli->prepare("SELECT COUNT(*), id, hashed_password FROM users WHERE username=?");

// Bind the parameter
$user = htmlentities($_POST['username']);
$stmt->bind_param('s', $user);
$stmt->execute();

// Bind the results
$stmt->bind_result($cnt, $user_id, $pwd_hash);
$stmt->fetch();
$stmt->close();

$pwd_guess = htmlentities($_POST['password']);
// Compare the submitted password to the actual password hash

if($cnt == 1 && password_verify($pwd_guess, $pwd_hash)){
	echo("{\"login\":\"success\"}");
} else{
	echo("{\"login\":\"failure\"}");
}
?>