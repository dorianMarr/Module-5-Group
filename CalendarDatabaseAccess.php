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
$mysqli = new mysqli('localhost', 'friend2', 'password', 'calendar');

if($mysqli->connect_errno) {
	printf(htmlentities("Connection Failed: %s\n", $mysqli->connect_error));
	exit;
}
?>