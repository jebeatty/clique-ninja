<?php

//SOP Action Selector 

session_start();
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
  include('groupHelper.php');

  if ($action=="submitLike") {
  	$likeType = $_GET['likeType'];
  	$postId = $_GET['postId'];
  	$userId = $_SESSION['userId'];
  	addLikeToPost($postId, $userId, $likeType);

  }
  else if ($action=="postComment") {
    $comment = $_POST['comment'];
    $userId=$_SESSION['userId'];
    $postId = $_POST['postId'];
    addCommentToPost($postId,$userId,$comment);

  }
  else if ($action=="getComments"){
    $postId = $_GET['postId'];
    $json = getCommentsForPost($postId);

    foreach ($json as &$comment) {
      $userName = getUserNameForId($comment['userId']);
      $comment['userName'] = $userName;
    }

    $json = json_encode($json);
    echo $json;

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

function checkIfUserLikedPost($postId, $userId){
	require_once("../inc/config.php");
  	require(ROOT_PATH."inc/database.php");

  	try{
  		$results = $db->prepare("SELECT postId FROM userPostRelations WHERE postId=? AND userId=?");
  		$results->execute(array($postId, $userId));

  	} catch(Exception $e){
  		 echo "Like tabulation data error!";
        exit;
  	}

  	$results = $results->fetchAll(PDO::FETCH_ASSOC);
  	if (count($results)>0) {
  		return true;
  	} else{
		return false;
  	}


}

function addUserPostRelation($postId, $userId, $likeType){
	  require_once("../inc/config.php");
  	require(ROOT_PATH."inc/database.php");

  	try{
  		$results = $db->prepare("INSERT INTO `userPostRelations` (`postId`, `userId`, `responseType`) VALUES (?,?,?)");
  		$results->execute(array($postId, $userId, $likeType));

  	} catch(Exception $e){
  		 echo "User-post insertion data error!";
        exit;
  	}

}

function getLikesForPost($postId){
	require_once("../inc/config.php");
  	require(ROOT_PATH."inc/database.php");

  	try{
  		$results = $db->prepare("SELECT ehs, likes, loves FROM posts WHERE postId=? LIMIT 1");
  		$results->execute(array($postId));

  	} catch(Exception $e){
  		 echo "Like tabulation data error!";
        exit;
  	}

  	$likeData = $results->fetchAll(PDO::FETCH_ASSOC);
  	return $likeData;
}



//comments
function getCommentsForPost($postId){
    require_once("../inc/config.php");
    require(ROOT_PATH."inc/database.php");

    try{
      $results = $db->prepare("SELECT comment, userId FROM comments WHERE postId=?");
      $results->execute(array($postId));

    } catch(Exception $e){
       echo "Comment tabulation data error!";
       exit;
    }

    $commentData = $results->fetchAll(PDO::FETCH_ASSOC);
    return $commentData;

}

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

    echo "comment added";

}

function flagComment($commentId){


}

//responses
function getResponsesForPost($postId){



}

function addResponseToPost($postId, $userId, $comment, $url){



}


?>