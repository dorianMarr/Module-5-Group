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
header('Content-Type: application/json');

$username=htmlentities($_POST['username']);

//GET THE USER ID
$stmt = $mysqli->prepare("SELECT id FROM users WHERE username=?");echo $mysqli->error;
$stmt->bind_param('s', $username);echo $mysqli->error;
$stmt->execute();echo $mysqli->error;
$stmt->bind_result($user_id);echo $mysqli->error;
$stmt->fetch();echo $mysqli->error;
$theUserId=$user_id;
$stmt->close();echo $mysqli->error; //it worked!! YAY

//GET THE EVENT IDS THAT ARE SHARED WITH THE USER
$stmt = $mysqli->prepare("SELECT event_id FROM sharedEvents WHERE new_user_id=?");
$stmt->bind_param('s', $theUserId); echo $mysqli->error;
$stmt->execute(); echo $mysqli->error;
$stmt->bind_result($event_id); echo $mysqli->error;
$all_event_ids[0]=0; //adding a dummy value to the array so that I can use it later
$count=0;
while($stmt->fetch())
{
    $all_event_ids[$count]=$event_id;
    $count++;
}
$stmt->close();

if($count!=0) //there aren't any shared events if $count is 0
{
    //GET THE NAME OF THE ORIGNIAL OWNER OF ALL THE EVENTS
    $allUsernames[0]=0; //dummy value, I'll replace it in the first iteration of this loop
    for($i=0;$i<count($all_event_ids);$i++)
    {
        $stmt = $mysqli->prepare("SELECT user_id FROM events WHERE event_id=?");
        $this_event_id=$all_event_ids[$i];
        $stmt->bind_param('s', $this_event_id); echo $mysqli->error;
        $stmt->execute(); echo $mysqli->error;
        $stmt->bind_result($temp); echo $mysqli->error;
        $stmt->fetch();
        $allUsernames[$i]=$temp;
    }
    $stmt->close();
    for($x=0;$x<count($all_event_ids);$x++)
    {
        $stmt = $mysqli->prepare("SELECT username FROM users WHERE user_id=?");
        $temp2=$allUsernames[$x];
        $stmt->bind_param('s', $temp2); echo $mysqli->error;
        $stmt->execute(); echo $mysqli->error;
        $stmt->bind_result($temp3); echo $mysqli->error;
        $stmt->fetch();
        $allUsernames[$x]=$temp3;
    }
    $stmt->close();

    // PRINT THE INFORMATION FOR ALL OF THE EVENTS
    $allEventInfo[0]=0; //dummy data time
    for($y=0;$y<count($all_event_ids);$y++)
    {
        $thisEvent['username']=$allUsernames[$y];
        $stmt = $mysqli->prepare("SELECT event_name, start_time, date FROM events WHERE event_id=?");
        $stmt->bind_param('s', $user_id); echo $mysqli->error;
        $stmt->execute(); echo $mysqli->error;
        $stmt->bind_result($event_name, $start_time, $event_date); echo $mysqli->error;
        $stmt->fetch();
        $thisEvent['event_name']=$event_name;
        $thisEvent['start_time']=$start_time;
        $thisEvent['date']=$event_date;
        $stmt->close();
        $allEventInfo[$y]=$thisEvent;
    }
    $resultString="{ \"events:\" {";
    $count=0;
    $finalArray[0]=0;
    for($a=0;$a<count($allEventInfo);$a++){
        //-----------print event info in JSON format---------------
        $finalArray[$count]=array("username" => $allEventInfo['username'],
            "event_name"=>$allEventInfo['event_name'],"start_time"=>$allEventINfo['start_time'],
            "event_date"=>$allEventInfo['date'],"event_id"=>$all_event_ids[$count]);
        $count++;
    }
    if($count!=0)
    {
        echo htmlentities(json_encode($finalArray));
    }
    else
    {
        echo htmlentities(json_encode(array(0)));
    }
}
?>