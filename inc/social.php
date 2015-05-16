<?php

//SOP Action Selector 

session_start();

require_once("config.php");
require_once("database.php");
require_once("helperFunctions.php");
require_once("notificationFunctions.php");
require_once("emailHelper.php");

if (isset($_SESSION['username'])) {
	if (isset($_POST["action"])) {
		$action = $_POST["action"];
	} else{
		$action = $_GET["action"];
	}

  socialActionSelector($action);

} else {
  echo "Invalid session data";
} 

 
function socialActionSelector($action){
  //include('groupHelper.php');

  if ($action=="submitLike") {
  	$likeType = $_GET['likeType'];
  	$postId = $_GET['postId'];
  	$userId = $_SESSION['userId'];
  	addLikeToPost($postId, $userId, $likeType);

  } else if ($action=="undoLike"){
    $postId = $_GET['postId'];
    $userId = $_SESSION['userId'];
    removeLikeFromPost($postId, $userId);


  } else if ($action=="postComment") {
    $comment = $_POST['comment'];
    $userId=$_SESSION['userId'];
    $postId = $_POST['postId'];
    addCommentToPost($postId,$userId,$comment);

  } else if ($action=="postGroupComment") {
    $comment = $_POST['comment'];
    $userId=$_SESSION['userId'];
    $groupId = $_POST['groupId'];
    addGroupCommentToChat($groupId,$userId,$comment);

  } else if ($action=="getComments"){
    $postId = $_GET['postId'];
    $json = getCommentsForPost($postId);

    foreach ($json as &$comment) {
      $userName = getUserNameForId($comment['userId']);
      $comment['userName'] = $userName;
    }

    $json = json_encode($json);
    echo $json;

  } else if ($action=="getGroupChat"){
    $groupId = $_GET['groupId'];
    getGroupChat($groupId);

  } else if ($action=="shareEmail"){
    
    $recipients=$_POST['shareMembers'];
    $subject=$_POST['emailSubject'];
    $body=$_POST['emailBody'];
    if (count($recipients)>0) {
      sendShareEmail($recipients, $subject, $body);
    }
  }
  else{
    echo 'invalid action code!';
  }

}

//social dynamics handling
//likes, comments, and responses!!!!



//likes
function addLikeToPost($postId, $userId, $likeType){
	$alreadyLiked = checkIfUserLikedPost($postId, $userId);
	if (!$alreadyLiked) {
		require_once("../inc/config.php");
	  	require(ROOT_PATH."inc/database.php");

	  	$SQLQuery = "UPDATE posts SET ".$likeType." = ".$likeType."+1 WHERE postId = ".$postId;
	  	
		try {
		    $results = $db->prepare($SQLQuery);
		    $results->execute();

	    } catch(Exception $e){
	        echo "Like addition data error!";
	        exit;
	    }

	    addUserPostRelation($postId, $userId, $likeType);
	    $likeData = getLikesForPost($postId);
	    $json = json_encode($likeData);
	    echo $json;
	}
}


function removeLikeFromPost($postId, $userId){
  global $db;

  //get the like type
  try{
    $results = $db->prepare("SELECT responseType FROM userPostRelations WHERE postId=? AND userId=? LIMIT 1");
    $results->execute(array($postId, $userId));

  } catch(Exception $e){
    echo "User-post selection data error!".$e;
    exit;
  }

  $likeResults = $results->fetchAll(PDO::FETCH_ASSOC);
 
  $likeType = $likeResults[0]['responseType'];
 
  //delete the userpostrelation
  try{
    $results = $db->prepare("DELETE FROM userPostRelations WHERE postId=? AND userId=? AND responseType=? LIMIT 1");
    $results->execute(array($postId, $userId, $likeType));

  } catch(Exception $e){
    echo "User-post deletion data error!".$e;
    exit;
  }

  //decrement the like type count
  $SQLQuery = "UPDATE posts SET ".$likeType." = ".$likeType."-1 WHERE postId = ".$postId;
  try{
    $results = $db->prepare($SQLQuery);
    $results->execute(array());

  } catch(Exception $e){
    echo "User-post decrementation data error!".$e;
    exit;
  }

  echo json_encode("success");

}

function addUserPostRelation($postId, $userId, $likeType){
	  require_once("../inc/config.php");
  	require(ROOT_PATH."inc/database.php");

  	try{
  		$results = $db->prepare("INSERT INTO `userPostRelations` (`postId`, `userId`, `responseType`) VALUES (?,?,?)");
  		$results->execute(array($postId, $userId, $likeType));

  	} catch(Exception $e){
  		 echo "User-post insertion data error!".$e;
      exit;
  	}

}


//comments

function addCommentToPost($postId, $userId, $comment){

    require_once("../inc/config.php");
    require(ROOT_PATH."inc/database.php");


    try{
      $results = $db->prepare("INSERT INTO `comments` (`postId`, `userId`, `comment`) VALUES (?,?,?)");
      $results->execute(array($postId, $userId, $comment));

    } catch(Exception $e){
       echo "Comment data insertion error!";
        exit;
    }

    ob_start();
    echo json_encode("success");
    header("Content-Length: ".ob_get_length());
    header("Connection: close");
    ob_end_flush();

    $group = array(getGroupIdForPostId($postId));
    updateNotifications($group,"comment");
    
}

function addGroupCommentToChat($groupId, $userId, $comment){

    require_once("../inc/config.php");
    require(ROOT_PATH."inc/database.php");

    $username=getUserNameForId($userId);
    
    try{
      $results = $db->prepare("INSERT INTO `groupChatMessages` (`groupId`, `commenterId`, `commenterName`, `comment`) VALUES (?,?,?,?)");
      $results->execute(array($groupId, $userId, $username, $comment));

    } catch(Exception $e){
       echo "Comment data insertion error!".$e;
        exit;
    }

    echo json_encode("success");
}


function getGroupChat($groupId){
 
    require_once("../inc/config.php");
    require(ROOT_PATH."inc/database.php");

    try{
      $results = $db->prepare("SELECT commenterName, comment, timePosted FROM `groupChatMessages` WHERE groupId=? LIMIT 30");
      $results->execute(array($groupId));

    } catch(Exception $e){
       echo "Chat data selection error:".$e;
      exit;
    }

    $chatData = $results->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($chatData);

}

function flagComment($commentId){


}

//responses
function getResponsesForPost($postId){



}

function addResponseToPost($postId, $userId, $comment, $url){



}


?>