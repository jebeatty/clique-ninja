<?php

session_start();

require_once("config.php");
require_once("database.php");
require_once("helperFunctions.php");
require_once("notificationFunctions.php");

if (isset($_SESSION['username'])) {
  if (isset($_POST["action"])) {
    $action = $_POST["action"];
  } else{
    $action = $_GET["action"];
  }

  actionSelector($action);
} else {
    echo "Invalid session data";
} 

define("GROUP_POSTS_LIMIT", '3');

function actionSelector($action){
  global $db;

  if ($action=="recent") {
    getRecent($_SESSION['userId']);
  }
  else if ($action=="library") {
    getLibrary($_SESSION['userId']);
  }
  else if ($action=="getAllGroupData"){
    getGroupDataForUser($_SESSION['userId']);
  } 
  else if ($action=="getGroupList"){
    $json = json_encode(getGroupListForUser($_SESSION['userId']));
    echo $json;
  } 
  else if ($action=="getGroupData"){
    //gets data for display
    $json = json_encode(getGroupData($_GET['groupId'],'25'));
    echo $json;
  }
  else if($action=="newPost"){
      addNewPost($_POST['group'],$_SESSION['userId'],$_POST['url'],$_POST['message']);
      
  } else if($action=="contactRequest"){
      addContactRequest($_POST['name'],$_POST['contactMessage'],$_SESSION['userId']);

  } else if($action=="editPost"){
      $editPostIds=$_POST['postIds'];
      $newURL=$_POST['url'];
      $newComment=$_POST['message'];
      editPost($editPostIds,$newURL,$newComment);

  } else if($action=="deletePost"){
      $deletePostIds=$_POST['postIds'];
      deletePost($deletePostIds);

  } else {
    $json = json_encode("Action Code Not Recognized");
    echo $json;
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
    foreach ($groups as $group) {
      $groupId = $group;
      if ($groupId=="library") {
        $groupId="0";
      }

      try {
      $results = $db->prepare("INSERT INTO `posts` (`posterName`, `posterId`, `groupId`, `url`, `comment`)
                                VALUES (?,?,?,?,?)
                                ");
      $results->execute(array($_SESSION['username'], $userId, $groupId, $url, $comment));
      $insertId = $db->lastInsertId();
      } catch(Exception $e){
          echo "Data loading error!";
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

    updateNotifications($groups,"post");
      
  }
}

function editPost($editIds, $newURL, $newComment){
  global $db;

  foreach ($editIds as $editId) {
    try {
    $results = $db->prepare("UPDATE posts SET url=?, comment=? WHERE postId=?");
    $results->execute(array($newURL,$newComment,$editId));
    } catch(Exception $e){
        echo "Data loading error!".$e;
        exit;
    }
  }
  echo json_encode("success");
}

function deletePost($deleteIds){
  global $db;

  foreach ($deleteIds as $deleteId) {
    try {
    $results = $db->prepare("DELETE FROM posts WHERE postId=?");
    $results->execute(array($deleteId));
    } catch(Exception $e){
        echo "Data loading error!".$e;
        exit;
    }
  }
  echo json_encode("success");

}

function addContactRequest($name,$message,$userId){
    global $db;

    try {
    $results = $db->prepare("INSERT INTO `contactRequests` (`name`, `message`, `userId`)
                              VALUES (?,?,?)
                              ");
    $results->execute(array($name, $message, $userId));
    } catch(Exception $e){
        echo "Data loading error!".$e;
        exit;
    }

    echo json_encode("success");
}

//Recent Functions
function getRecent($userId){
  global $db;
  
  //get the group list
  $groups = getGroupListForUser($userId);
  if (count($groups)>0) {
    //create an array of groupIds
    $groupIdList = array();
    foreach ($groups as $value) {
      array_push($groupIdList, $value["groupId"]);
    }

    //create a SQL query with WHERE posterid!=userId and (groupId=? or groupId=?)
    $SQLQuery = "SELECT posterId, groupId, url, postDate, postId, comment, ehs, likes, loves FROM posts WHERE posterId!=? AND (";

    //a For loop that concatenates groupId=? onto the SQL query, with ORs included except for the last iteration of the loop

    for ($i=0; $i < count($groupIdList); $i++) { 
      $SQLQuery .= "groupId=? ";
      $remainingLoops = (count($groupIdList)-$i)-1;
      if ($remainingLoops!==0) {
        $SQLQuery .= "OR ";
      }
    }

    array_unshift($groupIdList, $userId);
    $SQLQuery .= ") ORDER BY postId DESC LIMIT 15";

    //prepare $db call
    //execute with array of groupIds

    try {
      $results = $db->prepare($SQLQuery);
      $results->execute($groupIdList);

      } catch(Exception $e){
          echo "Data loading error!".$e;
          exit;

      }

      $recent = $results->fetchAll(PDO::FETCH_ASSOC);
      //need to overlay userPostRelation & like data
      foreach ($recent as &$recentPost) {
        $postLiked = checkIfUserLikedPost($recentPost['postId'], $userId);
        $recentPost['postLiked']=$postLiked;

        $commentData = getCommentsForPost($recentPost['postId']);
        $recentPost['commentData']=$commentData;

        $retrievedName = getUserNameForId($recentPost['posterId']);
        $recentPost['posterName']=$retrievedName;

        $recentPost['userPosted']=false;
      }

      resetPendingPosts($userId);
      resetPendingComments($userId);
     

      $json = json_encode($recent);
      echo $json;
  } else{
    echo json_encode(array());
  }
  

 }
  
//Library Functions  
function getLibrary($userId){
  global $db;

  try {
    $results = $db->prepare("SELECT groupId, url, postDate, postId, ehs, likes, loves, comment
                              FROM posts
                              WHERE posterId=?
                              ORDER BY postId DESC
                              ");
    $results->execute(array($userId));

    } catch(Exception $e){
        echo "Data loading error!";
        exit;

    }

    $library = $results->fetchAll(PDO::FETCH_ASSOC);

    // clean duplicates
    $url='';
    $cleanLibrary=array();
    
    for ($i=0; $i<count($library); $i=$i+1) {
        $newUrl = $library[$i]["url"];
        $newGroupName = getGroupNameForId($library[$i]['groupId']);
        $newId=$library[$i]['postId'];

        if ($url==$newUrl){
            $maxIndex = count($cleanLibrary)-1;
            $cleanLibrary[$maxIndex]['postCount']=$cleanLibrary[$maxIndex]['postCount']+1;
            array_push($cleanLibrary[$maxIndex]['groupList'], $newGroupName); 
            array_push($cleanLibrary[$maxIndex]['postIdList'], $newId); 
        
        } else{
            $library[$i]['postCount']=1;
            $library[$i]['groupList']=array($newGroupName);
            $library[$i]['postIdList']=array($newId);
            array_push($cleanLibrary, $library[$i]);
            $url=$newUrl;
        }
        
    
    }
    
    foreach ($cleanLibrary as &$libraryPost) {
        $commentData = getCommentsForPost($libraryPost['postId']);
        $libraryPost['commentData']=$commentData;
    }

    $json = json_encode($cleanLibrary);
    echo $json;
 }

//Group Data Functions


function getGroupDataForUser($userId){
    $groups = getGroupListForUser($userId);

    $groupDataArray = array();

    foreach ($groups as $value) {
    $newGroupData = array($value,getGroupData($value["groupId"],'3'));
    array_push($groupDataArray, $newGroupData); 
    }
    
    resetPendingPosts($userId);
    resetPendingComments($userId);

    $json = json_encode($groupDataArray);
    echo $json;
}




function getGroupData($groupId, $limit){
  global $db;
  
  if (!$limit) {
    $limit='15';
  }

  $query = "SELECT posterId, url, postDate, postId, ehs, likes, loves, comment FROM posts WHERE groupId=".$groupId." ORDER BY postId DESC LIMIT ".$limit;

  try {
    $results = $db->prepare($query);
    $results->execute(array());

    } catch(Exception $e){
        echo "Data loading error!";
        exit;

    }

    $groupPosts = $results->fetchAll(PDO::FETCH_ASSOC);

    //check for likes
    $userId=$_SESSION['userId'];

    foreach ($groupPosts as &$groupItem) {
      $postLiked = checkIfUserLikedPost($groupItem['postId'], $userId);
      $groupItem['postLiked']=$postLiked;

      $commentData = getCommentsForPost($groupItem['postId']);
      $groupItem['commentData']=$commentData;

      $retrievedName = getUserNameForId($groupItem['posterId']);
      $groupItem['posterName']=$retrievedName;

      if ($userId==$groupItem['posterId']) {
        $groupItem['userPosted']=true;
      }else{
        $groupItem['userPosted']=false;
      }

    }

    return $groupPosts;

}

function addUserPostRelation($postId, $userId, $likeType){
    global $db;

    try{
      $results = $db->prepare("INSERT INTO `userPostRelations` (`postId`, `userId`, `responseType`) VALUES (?,?,?)");
      $results->execute(array($postId, $userId, $likeType));

    } catch(Exception $e){
       echo "User-post insertion data error!";
        exit;
    }
}

?>