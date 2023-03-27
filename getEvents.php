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
header('Content-Type: application/json'); //learned about this from https://stackoverflow.com/questions/48299823/proper-way-to-echo-json-in-php

//If the username variable in calendar.html is set, then the user must have logged in properly
//Also no need for sanity check for the above reason
$username=htmlentities($_POST['username']);

//GET THE USER ID
$stmt = $mysqli->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

//GET ALL THE EVENT INFO 
$stmt = $mysqli->prepare("SELECT event_name, event_id, start_time, date FROM events WHERE user_id=?");
$stmt->bind_param('s', $user_id); 
$stmt->execute(); 
$stmt->bind_result($event_name, $event_id, $start_time, $event_date); 
$count=0;
$infoArray[0]=0;//dummy value
while($stmt->fetch())
{
    //-----------print event info in JSON format---------------
    // learned about json_encode from https://stackoverflow.com/questions/48299823/proper-way-to-echo-json-in-php
    $infoArray[$count]=array("event_name" => $event_name, "event_id"=>$event_id,"start_time"=>$start_time,"date"=>$event_date);
    $count++;
}
//$resultString=substr($resultString, 0, strlen($resultString)-1); //The last character is a comma that we don't need so I'm removing it
if($count!=0)
{
    echo htmlentities(json_encode($infoArray));
}
else
{
    echo htmlentities(json_encode(array(0)));
}
?>