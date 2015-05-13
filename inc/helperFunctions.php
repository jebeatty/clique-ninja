<?php


//"exchange"/getFor functions, exchanging one set/item of data for others

function getUserNameForId($userId){
    global $db;

    try{
      $results = $db->prepare("SELECT userName FROM users WHERE userId=?");
      $results->execute(array($userId));

    } catch(Exception $e){
       echo "Username selection data error!";
        exit;
    }

    $results = $results->fetchAll(PDO::FETCH_ASSOC);
    if (count($results)>0) {
      return $results[0]['userName'];
    }
    else{
      return 'Anonymous';
    }    
}



function getUserIdForEmail($email){
        global $db;

        try {
    $results = $db->prepare("SELECT userId FROM users WHERE email = ?");
    $results->execute(array($email));

    } catch(Exception $e){
        echo "Data selection error!";
        exit;
    }

    $userData = $results->fetchAll(PDO::FETCH_ASSOC);

    if (count($userData)>0) {
      return $userData[0]['userId'];
    }
    else{
      return false;
    }   
}


function getLikesForPost($postId){
        global $db;

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

function getCommentsForPost($postId){
    global $db;

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

function getGroupNameForId($groupId){
        global $db;

  if($groupId>0){
  
        
    try {
    $results = $db->prepare("SELECT groupName FROM groups WHERE groupId = ?");
    $results->execute(array($groupId));

    } catch(Exception $e){
        echo "Data selection error!";
        exit;
    }

    $groupData = $results->fetchAll(PDO::FETCH_ASSOC);

    return $groupData[0]["groupName"];
    }
  else{
    return "Library";   
  }
}

function getGroupListForUser($userId){
  global $db;
  
  try {
    $results = $db->prepare("SELECT groupId, groupName
                              FROM userGroupRelations
                              WHERE userId=? 
                              ");
    $results->execute(array($userId));

    } catch(Exception $e){
        echo "Data loading error!";
        exit;

    }

    $groupList = $results->fetchAll(PDO::FETCH_ASSOC);
    return $groupList;
}

function getAllMembersForGroup($groupId){
  global $db;


  try {
    $results = $db->prepare("SELECT userId, status FROM userGroupRelations WHERE groupId = ?");
    $results->execute(array($groupId));

    } catch(Exception $e){
        echo "Data selection error!";
        exit;
    }

    $results = $results->fetchAll(PDO::FETCH_ASSOC);
    foreach ($results as &$memberInfo) {
      $userName = getUserNameForId($memberInfo['userId']);
      $memberInfo['userName']=$userName;
    }

    return $results;
}

function getMemberIdsForGroup($groupId){
  global $db;

  try {
    $results = $db->prepare("SELECT userId FROM userGroupRelations WHERE groupId = ?");
    $results->execute(array($groupId));

    } catch(Exception $e){
        echo "Data selection error!";
        exit;
    }

    $results = $results->fetchAll(PDO::FETCH_ASSOC);

    return $results;
}

function getGroupIdForPostId($postId){
  require_once("../inc/config.php");
  require(ROOT_PATH."inc/database.php");

  try{
    $results = $db->prepare("SELECT groupId FROM posts WHERE postId=?");
    $results->execute(array($postId));

  } catch(Exception $e){
     echo "GroupId/PostId data error!";
      exit;
  }

  $results = $results->fetchAll(PDO::FETCH_ASSOC);
  if (count($results)>0) {
    return $results[0]['groupId'];
  }
  else{
    return '0';
  }

}
//check functions, checking if certain data states are true or false
function checkIfUserLikedPost($postId, $userId){
        global $db;

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

function checkUserGroupMembership($userId, $groupId){
        global $db;

        try {
        $results = $db->prepare("SELECT relationId FROM userGroupRelations WHERE userId=? AND groupId=? ");
        $results->execute(array($userId, $groupId));

    } catch(Exception $e){
        echo "User membership data  error!";
        exit;
    }

    $resultCount = $results->rowCount();
    if ($resultCount>0) {
        return true;
    }
    else{
        return false;
    }
}

function checkUserGroupInviteStatus($userId, $groupId){
        global $db;

        try {
        $results = $db->prepare("SELECT groupId FROM groupInvites WHERE userId=? AND groupId=? ");
        $results->execute(array($userId, $groupId));

    } catch(Exception $e){
        echo "User membership data  error!";
        exit;
    }

    $resultCount = $results->rowCount();
    if ($resultCount>0) {
        return true;
    }
    else{
        return false;
    }
}

//General utility functions
function cleanURL($inputURL){
  //clean it
  $outputURL = filter_var($inputURL, FILTER_SANITIZE_URL);

  //check it
  if(parse_url($outputURL, PHP_URL_SCHEME) === null){
    //add the scheme, then filter validate
    $outputURL = "http://" . $outputURL;

    //if the filtering doesn't work, throw an error. Else just the new URL
    if(filter_var($outputURL, FILTER_VALIDATE_URL) === false){
      return false;
    }
    else{
      return $outputURL;
    }
  }
  else{
    return $outputURL;
  } 
}

function getNumDays($time_posted){
        $now = time();
        $your_date = strtotime($time_posted);
        $post_date=date("M j, Y", $your_date);
        $datediff = $now - $your_date;
        $num_sec=$datediff;
        $num_min=floor($datediff/(60));
        $num_hour=floor($datediff/(60*60));
        $num_day=floor($datediff/(60*60*24));   
        $num_week=floor($datediff/(60*60*24*7));
        $num_month=floor($datediff/(60*60*24*7*4.38));

        if($num_sec<0)
                $num_sec=0;
                
        if($num_sec<60)
                return $num_sec." sec ago";
                
        if($num_min>0 && $num_min<60)
                return $num_min." min ago";

        if($num_hour>1 && $num_hour<24)
                return $num_hour." hours ago";
        else if($num_hour==1)
                return "1 hour ago";

        if($num_day>1 && $num_day<14)
                return $num_day." days ago";
        if($num_day==1)
                return "1 day ago";

        if($num_week>1 && $num_week<5)
                return $num_week." weeks ago";
        else if($num_week==1)
                return '1 week ago';

        if($num_month>1 && $num_month<3)
                return $num_month." months ago";
        else if($num_month==1)
                return "1 month ago";
        else
                return $post_date;
        }

?>