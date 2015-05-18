<?php
require_once("config.php");
require_once("database.php");
require_once("helperFunctions.php");

function updateNotifications($groups, $type){
	global $db;

	$category='';
	$freq='';
	if ($type=='post') {
		$category='pendingPosts';
		$freq='postCode';
	} else if($type=='comment'){
		$category='pendingComments';
		$freq='commentCode';
	} else{
		exit();
	}

	
	//group array is just the id
	foreach ($groups as $group) {
		$groupId = $group;
		if ($groupId!='library') {
			$groupName=getGroupNameForId($groupId); 
			$memberArray=getMemberIdsForGroup($groupId);

			$groupMembers = array();
			foreach($memberArray as $member){
				array_push($groupMembers,$member['userId']);
			} 

			$immediateSendList = array();

			//generate the appropriate SQL query
			$SQLQuery = "SELECT userId, enabled, ".$freq.", ".$category." FROM notifications WHERE ";

			for ($i=0; $i < count($groupMembers); $i++) { 
			    $SQLQuery .= "userId=? ";
			    $remainingLoops = (count($groupMembers)-$i)-1;
			    if ($remainingLoops!==0) {
			      $SQLQuery .= "OR ";
			    }
		  	}

		  	try {
		    $results = $db->prepare($SQLQuery);
		    $results->execute($groupMembers);

		    } catch(Exception $e){
		        echo "Data loading error!";
		        exit;

		    }

		    $membersToUpdate = $results->fetchAll(PDO::FETCH_ASSOC);

		    //now take each of the members of the group and update or notify as need be
		    foreach ($membersToUpdate as $member) {
		    	if ($member['enabled']=='1') {
		    		echo "member note code: ".$member[$freq];
		    		if ($member[$freq]=='0') {
		    			array_push($immediateSendList, $member['userId']); //member to be notified	
		    		} else {
		    			try{
		    				$updateQuery = "UPDATE notifications SET ".$category."=".$category."+1 WHERE userId=".$member['userId'];
						 	$updates = $db->prepare($updateQuery);
						 	$updates->execute();
						} catch (Exception $e){
							echo "notification update error";
							exit();
						}
		    		}
		    	}
		    }

		    $urlforgroupLibrary="https://www.discoverclique.com/doublesecretbeta/groupLibrary.php?groupName=".$groupName."&groupId=".$groupId;
		    if (count($immediateSendList)>0) {
		    	sendNotificationsToAliases("There's been a ".$type." to ".$groupName, $urlforgroupLibrary, $immediateSendList);
		    }
		}
	}
}


//takes in a msg, a url, and an array of aliases
function sendNotificationsToAliases($text, $url, $aliases){
	$data=array("alert"=>$text, "url"=>$url,"aliases"=>$aliases);
	$payload=json_encode($data);


	$req=curl_init("https://api.goroost.com/api/push");
	curl_setopt($req, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
	curl_setopt($req, CURLOPT_POSTFIELDS, $payload);  
	curl_setopt($req, CURLOPT_RETURNTRANSFER, true);  
	curl_setopt($req, CURLOPT_USERPWD, "pvneqf9i8wdjrh7yj0pxw000xy3ex3me:hcnizd5dwf9weml5wr4n48z99i701q4u"); 
	curl_setopt($req, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

	$result = curl_exec($req);
	echo $result;
}

function resetPendingPosts($userId){
	global $db;

	try{
		$updates = $db->prepare("UPDATE notifications SET pendingPosts=0 WHERE userId=?");
		$updates->execute(array($userId));
	} catch (Exception $e){
		echo "notification update error";
		exit();
	}
}

function resetPendingComments($userId){
	global $db;

	try{
		$updates = $db->prepare("UPDATE notifications SET pendingComments=0 WHERE userId=?");
		$updates->execute(array($userId));
	} catch (Exception $e){
		echo "notification update error";
		exit();
	}
}


?>