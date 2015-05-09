<?php

session_start();
define("DISCOVERY_GROUP_LIMIT", 7);
if (isset($_SESSION['username'])) {
	if (isset($_POST["action"])) {
		$action = $_POST["action"];
	} else{
		$action = $_GET["action"];
	}

  discoverSelector($action);

  } else {
    echo "Invalid session data";
  } 

 
function discoverSelector($action){
  include('groupHelper.php');

  if ($action=="joinDiscovery") {
  	//find the smallest discovery group with room && one in which the user is not already a member 
  	$userId = $_SESSION['userId'];
  	$openGroupId = getSmallestAvailableDiscoveryGroup($userId);

  	if ($openGroupId>0) {
  		$groupName = getGroupNameForId($openGroupId);
  		addUserToGroup($userId, $openGroupId, $groupName); 
  		//adding a user increments the member count for a discovery group (in the groupHelper file). 
  		//Leaving doesn't change the count - we'd rather have new people form new groups than join old ones (-design question-)
  		sendBackGroupDataJSON($openGroupId);

  	}
  	else{
  		$newGroupId = createDiscoveryGroup($userId);
  		sendBackGroupDataJSON($newGroupId);
  	}

  } else{
	echo "invalid action code";
  }
}

function getSmallestAvailableDiscoveryGroup($userId){
	//include('groupHelper.php');
	require_once("../inc/config.php");
  	require(ROOT_PATH."inc/database.php");

	try {
	    $results = $db->prepare("SELECT groupId FROM groups WHERE discovery=1 AND numberOfMembers<? ORDER BY numberOfMembers");
	    $results->execute(array(DISCOVERY_GROUP_LIMIT));

    } catch(Exception $e){
        echo "Data selection error!";
        exit;
    }

    $groupData = $results->fetchAll(PDO::FETCH_ASSOC);

    foreach ($groupData as $discoveryGroup) {
    	$alreadyMember = checkUserGroupMembership($userId,$discoveryGroup['groupId']);
    	if (!$alreadyMember) {
    		return $discoveryGroup['groupId'];
    	}
    }

    return 0;

}

function createDiscoveryGroup($firstUserId){
	require_once("../inc/config.php");
  	require(ROOT_PATH."inc/database.php");
  	
  	$name = createNameForDiscoveryGroup();
  	$desc = "A discovery group!";
  	
	try {
    $results = $db->prepare("INSERT INTO `groups` (`groupName`, `groupDesc`,`public`,`discovery`) VALUES (?,?,0,1)");
    $results->execute(array($name,$desc));
    $insertId = $db->lastInsertId();
    } catch(Exception $e){
        echo "Discovery group creation data insertion error!";
        exit;
    }

 	addUserToGroup($firstUserId,$insertId, $name);
 	return $insertId;
 	
 }

function createNameForDiscoveryGroup(){
	require_once("../inc/config.php");
  	require(ROOT_PATH."inc/database.php");

	try {
	    $results = $db->prepare("SELECT groupId FROM groups WHERE discovery=1 ORDER BY groupId DESC LIMIT 1");
	    $results->execute();

    } catch(Exception $e){
        echo "Data selection error!";
        exit;
    }

    $nameData = $results->fetchAll(PDO::FETCH_ASSOC);
    $newGroupName='';
    if (count($nameData)>0) {
    	$newId = $nameData[0]['groupId'] + 1;
    	$newGroupName = 'Discovery Group #'.$newId;
    } else{
    	$newGroupName = 'Discovery Group #1';
    }	

	return $newGroupName;
}

function sendBackGroupDataJSON($groupId){

	require_once("../inc/config.php");
  	require(ROOT_PATH."inc/database.php");

	try {
	    $results = $db->prepare("SELECT groupId, groupName, numberOfMembers FROM groups WHERE groupId=?");
	    $results->execute(array($groupId));

    } catch(Exception $e){
        echo "Data selection error!";
        exit;
    }

    $groupData = $results->fetchAll(PDO::FETCH_ASSOC);

	$groupData=$groupData[0];
	$json = json_encode($groupData);
	echo $json;
	

}


?>