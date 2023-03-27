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
require 'databaseAccess.php';

// come back to this I guess
// $destination_username = $_POST['dest'];
// $amount = $_POST['amount'];
// if(!hash_equals($_SESSION['token'], $_POST['token'])){
// 	die("Request forgery detected");
// }
$event_name=htmlentities($_POST['event_name']);
$username=htmlentities($_POST['ownerUsername']);
$start_time=htmlentities($_POST['start_time']);
$event_date=htmlentities($_POST['date']);
$event_id=htmlentities($_POST['event_id']);

$stmt = $mysqli->prepare("UPDATE events set event_name = ?,
    start_time = ?, date = ? WHERE event_id=?"); echo $mysqli->error;
//it worked!! YAY
$stmt->bind_param('ssss', $event_name, $start_time, $event_date, $event_id);
$stmt->execute();
$stmt->close(); 
?>