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
$new_user_id=htmlentities($_POST['new_user_id']);

//ADD THE NEW EVENT
$stmt = $mysqli->prepare("insert into events (new_user_id, event_id) values (?, ?)");
echo $mysqli->error;
$stmt->bind_param('ss', $new_user_id, $event_id);echo $mysqli->error;
$stmt->execute();echo $mysqli->error;
$stmt->close(); //it worked!! YAY
exit;
?>