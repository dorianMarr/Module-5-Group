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

//If the username variable in calendar.html is set, then the user must have logged in properly
//Also no need for sanity check for the above reason
$username=htmlentities($_POST['username']);

//GET USER ID
$stmt = $mysqli->prepare("SELECT id FROM users WHERE username=?");echo $mysqli->error;
$stmt->bind_param('s', $username);echo $mysqli->error;
$stmt->execute();echo $mysqli->error;
$stmt->bind_result($user_id);echo $mysqli->error;
$stmt->fetch();echo $mysqli->error;
$stmt->close();echo $mysqli->error; //it worked!! YAY

$event_name=htmlentities($_POST['event_name']);
$event_time=htmlentities($_POST['event_time']);
$event_date=htmlentities($_POST['event_date']);

//ADD THE NEW EVENT
$stmt = $mysqli->prepare("insert into events (user_id, event_name, start_time, date) values (?, ?, ?, ?)");
echo $mysqli->error;
if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error); //aw man :(
}
$stmt->bind_param('ssss', $user_id, $event_name, $event_time, $event_date);echo $mysqli->error;
$stmt->execute();echo $mysqli->error;
$stmt->close(); //it worked!! YAY
echo(htmlentities("Successful Event Addition"));
exit;
?>