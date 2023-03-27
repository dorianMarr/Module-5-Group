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

//come back to this I guess
// echo $_SESSION['token']."<br>".$_POST['token']."<br>";
// if(!hash_equals($_SESSION['token'], $_POST['token'])){
// 	die("Request forgery detected");
// }

$username=$_POST['username'];
$event_id=$_POST['event_id'];

// Add stuff to check that the event is owned by the username
// checking that the event id is associated with the username for extra security measure

$stmt = $mysqli->prepare("DELETE FROM events WHERE event_id = ?");
$stmt->bind_param('s', $event_id);
$stmt->execute();
$stmt->close(); //it worked!! YAY
?>