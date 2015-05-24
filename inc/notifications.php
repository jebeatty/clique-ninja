<?php

session_start();

require_once("config.php");
require_once("database.php");
require_once("helperFunctions.php");

if (isset($_SESSION['username'])) {
	if (isset($_POST["action"])) {
		$action = $_POST["action"];
	} else{
		$action = $_GET["action"];
	}

  notificationActions($action);

} else {
  echo "Invalid session data";
} 

function notificationActions($action){

	if ($action=='changeSettings') {
		$postFreq = $_POST["postFreq"];
		$commentFreq = $_POST["commentFreq"];
		$userId = $_SESSION["userId"];
		setSettingsForUser($userId, $postFreq, $commentFreq);
		
	} else if($action=='toggleNotifications'){
		$status=$_POST["enabled"];
		if ($status=="true") {
			$status=1;
		} else{
			$status=0;
		}
		$userId=$_SESSION["userId"];
		toggleNotifications($userId,$status);
		
	}  else {
		echo "Invalid action code";
	}


}

function setSettingsForUser($userId, $postFreq, $commentFreq){
	global $db;

	$alreadyRegistered = checkUserNotificationRegistration($userId);
	if ($alreadyRegistered) {
		try{
		 	$results = $db->prepare("UPDATE notifications SET enabled=?, postCode=?, commentCode=? WHERE userId=?");
		 	$results->execute(array(1,$postFreq,$commentFreq,$userId));
		} catch (Exception $e){
			echo "notification update error".$e;
			exit();
		}
		
		echo json_encode("success");	

	} else{
		try {
	      $results = $db->prepare("INSERT INTO `notifications` (`userId`, `enabled`, `postCode`, `commentCode`, `pendingPosts`, `pendingComments`)
	                                VALUES (?,?,?,?,?,?)
	                                ");
	      $results->execute(array($userId,1,$postFreq,$commentFreq,0,0));
	     
	    } catch(Exception $e){
	       echo "Data loading error!".$e;
	       exit;
	    }

	    echo json_encode("success");
	}
}

function toggleNotifications($userId, $status){
	global $db;

	$alreadyRegistered = checkUserNotificationRegistration($userId);
	if ($alreadyRegistered) {
		try{
		 	$results = $db->prepare("UPDATE notifications SET enabled=?, postCode=1, commentCode=1 WHERE userId=?");
		 	$results->execute(array($status, $userId));
		} catch (Exception $e){
			echo "notification update error";
			exit();
		}
		
		echo json_encode("success");	
	}
	else{
		//enable notifications with default values for freq. control items.
		setSettingsForUser($userId,1,1);
	}
}

function checkUserNotificationRegistration($userId){
	global $db;

	try{
		$results = $db->prepare("SELECT userId FROM notifications WHERE userId=?");
		$results->execute(array($userId));
	} catch (Exception $e){
		echo "notification registration search error";
		exit();
	}

	 $users = $results->fetchAll(PDO::FETCH_ASSOC);
	 if (count($users)>0) {
	 	return true;
	 } else {
	 	return false;
	 }


}




?>