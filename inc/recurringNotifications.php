<?php

require_once("config.php");
require_once("database.php");
require_once("helperFunctions.php");
require_once("notificationFunctions.php");


sendPostNotifications();
sendCommentNotifications();
//array of arrays


function sendPostNotifications(){
	global $db;

	//get users who have daily updates...
	try{
		$results = $db->prepare("SELECT userId, pendingPosts FROM notifications WHERE postCode=1 AND enabled=1 AND pendingPosts>0");
		$results->execute(array());
	} catch (Exception $e){
		echo "daily post notification search error";
		exit();
	}
	$dailies = $results->fetchAll(PDO::FETCH_ASSOC);

	//get users who are due for a weekly update...which all go out sunday night (wday==0)
	if(getdate()[wday]==0){
		try{
		$results = $db->prepare("SELECT userId, pendingPosts FROM notifications WHERE postCode=2 AND enabled=1 AND pendingPosts>0");
		$results->execute(array());
		} catch (Exception $e){
			echo "weekly post notification search error";
			exit();
		}
		$weeklies = $results->fetchAll(PDO::FETCH_ASSOC);
	} else{
		$weeklies = array();
	}
	

	//combine the two
	$combined = array_merge($dailies, $weeklies);
	$notificationArray=deduplicateByPending($combined, 'pendingPosts');
	echo var_dump($notificationArray);
	//for each array of aliases in array_prime (at index N)
	//sendNotificationsToAliases("You have N new recommendations waiting for you", "https://www.discoverclique.com/doublesecretbeta", $aliases);
	foreach ($notificationArray as $key => $value) {
		$aliases=$value;
		if ($key>1) {
			sendNotificationsToAliases("You have ".$key." new recommendations waiting for you", "https://www.discoverclique.com/doublesecretbeta", $aliases);
		} else{
			sendNotificationsToAliases("You have ".$key." new recommendation waiting for you", "https://www.discoverclique.com/doublesecretbeta", $aliases);
		}
		
	}
}

function sendCommentNotifications(){
	global $db;
	
	//get users who have daily updates...
	try{
		$results = $db->prepare("SELECT userId, pendingComments FROM notifications WHERE commentCode=1 AND enabled=1 AND pendingComments>0");
		$results->execute(array());
	} catch (Exception $e){
		echo "daily post notification search error";
		exit();
	}
	$dailies = $results->fetchAll(PDO::FETCH_ASSOC);

	//get users who are due for a weekly update...which all go out sunday night (wday==0)
	if(getdate()[wday]==0){
		try{
		$results = $db->prepare("SELECT userId, pendingComments FROM notifications WHERE commentCode=2 AND enabled=1 AND pendingCommentss>0");
		$results->execute(array());
		} catch (Exception $e){
			echo "weekly post notification search error";
			exit();
		}
		$weeklies = $results->fetchAll(PDO::FETCH_ASSOC);
	} else{
		$weeklies = array();
	}
	

	//combine the two
	$combined = array_merge($dailies, $weeklies);
	$notificationArray=deduplicateByPending($combined, 'pendingComments');
	echo var_dump($notificationArray);
	//for each array of aliases in array_prime (at index N)
	//
	foreach ($notificationArray as $key => $value) {
		$aliases = $value;
		if ($key>1) {
			sendNotificationsToAliases("There are ".$key." new comments in your groups!", "https://www.discoverclique.com/doublesecretbeta", $aliases);
		} else{
			sendNotificationsToAliases("There is ".$key." new comments in your groups!", "https://www.discoverclique.com/doublesecretbeta", $aliases);
		}
		
	}
}



function deduplicateByPending($inputArray,$keyString){
	$outputArray = array();

	foreach ($inputArray as $userInfo) {
		$key=$userInfo[$keyString];
		if(array_key_exists($key, $outputArray)){
			array_push($outputArray[$key], $userInfo['userId']);
		} else{
			$outputArray[$key]=array($userInfo['userId']);
		}
	}

	return $outputArray;
}




?>