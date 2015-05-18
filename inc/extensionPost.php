<?php


require_once("config.php");
require_once("database.php");
require_once("helperFunctions.php");
require_once("notificationFunctions.php");


if (isset($_POST["action"])) {
	$action = $_POST["action"];
} else{
	$action = $_GET["action"];
}


actionSelector($action);

function actionSelector($action){
	if($action=='post'){
		$userId = $_POST['token'];
		$comment = $_POST['comment'];
		$url = $_POST['url'];
		$groups=$_POST['group'];
		if (count($groups)>0) {
			addNewPost($groups, $userId, $url, $comment);
		}
		

	} else if($action=='authenticate'){
		$userName=$_POST['username'];
		$password=$_POST['password'];
		$userId=getUserIdForName($userName);
		if(authenticateUser($userId,$password)){
			returnUserExtensionData($userId);
		} else{
			echo json_encode("no such user");
		}
	} 
}

function addNewPost($groups, $userId, $url, $comment){
  global $db;

  if ($comment=="++++++") {
    $comment ='';
  }

  $url = cleanURL($url);
  if ($url===false) {
    echo json_encode("failure");
  }
  else{
  	$userName = getUserNameForId($userId);
  	$groupIdList=array();
    foreach ($groups as $group) {

      $groupName = $group;
      $groupId=getGroupIdForName($groupName);
      array_push($groupIdList,$groupId);

      
      try {
      $results = $db->prepare("INSERT INTO `posts` (`posterName`, `posterId`, `groupId`, `url`, `comment`)
                                VALUES (?,?,?,?,?)
                                ");
      $results->execute(array($userName, $userId, $groupId, $url, $comment));
      $insertId = $db->lastInsertId();
      } catch(Exception $e){
          echo "Data loading error!".$e;
          exit;
      }
      addUserPostRelation($insertId,$userId, 'likes');
    } //end each*/

    //wrap in ob_flush
   
    ob_start();
    echo json_encode("success"); 
    header("Content-Length: ".ob_get_length());
    header("Connection: close");
    ob_end_flush();


    updateNotifications($groupIdList,"post");
      
  }
}

function returnUserExtensionData($userId){
	$groupList=getGroupListForUser($userId);
	$extensionData =array($userId);
	foreach ($groupList as $group) {
		array_push($extensionData, $group['groupName']);	
	}

	echo json_encode($extensionData);
	
}

?>