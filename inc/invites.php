<?php

//manage groups, invites, etc.
// start with action selector + session management


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

  actionSelector($action);

} else {
  echo "Invalid session data";
} 


function actionSelector($action){
  
  if ($action=="createGroup") {
    $groupName = $_POST['groupName'];
    $groupDesc = $_POST['groupDesc'];
    $public = $_POST['public'];
    $invitedMembers = $_POST['members'];
    $inviteMsg=$_POST['groupInviteMsg'];
    createGroup($groupName,$groupDesc,$public,$invitedMembers,$inviteMsg);
  }
  else if ($action=="inviteFriends") {
    $invitedMembers = $_POST['members'];
    $groupId = $_POST['groupId'];
    $inviterName=$_SESSION['username'];
    $inviteMsg=$_POST['friendsInviteMsg'];
    sendInvites($groupId, $inviterName, $invitedMembers, $inviteMsg);   
          
  }
  else if($action=="getGroupInfo"){
    //gets information on members & invites
    $groupId = $_GET['groupId'];

    //the actual content
    $groupInfo=getGroupData($groupId);

    //the member & group desc. info
    $memberInfo=getAllMembersForGroup($groupId);
    array_push($groupInfo, $memberInfo);

    //the user's status code
    $userRights = getUserGroupStatus($_SESSION['userId'],$groupId);
    array_push($groupInfo, $userRights);

    $json = json_encode($groupInfo);
    echo $json;
  }
  else if($action=="changeGroupInfo"){
    $groupId=$_POST['groupId'];
    $groupName=$_POST['groupName'];
    $groupDesc=$_POST['groupDesc'];
    changeGroupInfo($groupId, $groupName, $groupDesc);
  }
  else if ($action=="getGroupInvites"){
    queryInvites($_SESSION['userId']);


  } else if ($action=="acceptInvite") {
    $groupId=$_GET['acceptedGroupId'];
    $userId = $_SESSION['userId'];
    acceptInvite($groupId,$userId);
    echo json_encode('success');
  }
  else if ($action=="rejectInvite"){
    $groupId=$_GET['rejectedGroupId'];
    $userId = $_SESSION['userId'];
    deleteInvite($groupId,$userId);
    echo json_encode('success');

  } else if ($action=="leaveGroup"){
    $groupId=$_GET['rejectedGroupId'];
    $userId = $_SESSION['userId'];
    removeUserFromGroup($groupId,$userId);
    echo json_encode('success');

  }
  else{
    echo "invalid action selector:";
    echo $action;

  }
}

//create groups
function createGroup($name,$desc,$public,$invites,$inviteMsg){
    global $db;
    
    if (!$public) {
      $public=false;
    }

    try {
      $results = $db->prepare("INSERT INTO `groups` (`groupName`, `groupDesc`,`public`) VALUES (?,?,?)");
      $results->execute(array($name,$desc,$public));
      $insertId = $db->lastInsertId();
    } catch(Exception $e){
      echo "Group creation data insertion error!";
      exit;
    }

    addUserToGroup($_SESSION['userId'],$insertId, $name);
    setUserGroupStatus($_SESSION['userId'],$insertId,1);

    if (count($invites)>0) {
      $inviterName=$_SESSION['username'];
      sendInvites($insertId, $inviterName, $invites,$inviteMsg); 
    } else{
      echo "success";
    }      
}



//send invites
function sendInvites($groupId, $inviterName, $invites, $inviteMsg){
  require_once("emailHelper.php");
  if (count($invites)>0) {
    foreach ($invites as $userInvite) {
      $userId=getUserIdForEmail($userInvite);
      if ($userId) {
        inviteUserToGroup($userId,$groupId, $inviterName,$inviteMsg);
      }
      else{
        createInviteForNonuser($userInvite,$groupId, $inviterName,$inviteMsg);
      }
    }
  }

    echo "success";

}

function createInviteForNonuser($userInvite,$groupId, $inviterName,$inviteMsg){
      global $db;
      try {
      $results = $db->prepare("INSERT INTO `pendingInvites` (`groupId`, `userEmail`, `inviterName`,`inviteMsg`) VALUES (?,?,?,?)");
      $results->execute(array($groupId, $userInvite, $inviterName, $inviteMsg));

      } catch(Exception $e){
          echo "User invite data insertion error!";
          exit;
      }

      //sendInviteEmail($userInvite,$inviterName, getGroupNameForId($groupId));
}



//accept invites
function acceptInvite($groupId,$userId){
        $groupName=getGroupNameForId($groupId);
        addUserToGroup($userId,$groupId, $groupName);
        deleteInvite($groupId, $userId);
}

//reject invites
function deleteInvite($groupId,$userId){
        global $db;
        
        try {
    $results = $db->prepare("DELETE FROM groupInvites WHERE groupId=? AND userId=?");
    $results->execute(array($groupId, $userId));

    } catch(Exception $e){
        echo "Data deletion error!";
        exit;
    }
   
}

//query invites, json return
function queryInvites($userId){
  global $db;

    try {
      $results = $db->prepare("SELECT groupId, inviterName, inviteMsg FROM groupInvites WHERE userId=?");
      $results->execute(array($userId));
    } catch(Exception $e){
        echo "Data selection error!";
        exit;
    }

    $groupData = $results->fetchAll(PDO::FETCH_ASSOC);

    foreach ($groupData as &$groupInvite) {
        $groupName = getGroupNameForId($groupInvite['groupId']);
        if (!$groupName) {
                $groupName="Unnamed Group";
        }
        $groupInvite["groupName"]=$groupName;
    }
  
    echo json_encode($groupData);

}

function inviteUserToGroup($userId,$groupId,$inviterName,$inviteMsg){
  $alreadyMember = checkUserGroupMembership($userId,$groupId);
  $alreadyInvited = checkUserGroupInviteStatus($userId,$groupId);

  if (!$alreadyMember && !$alreadyInvited) {
    global $db;

    try {
      $results = $db->prepare("INSERT INTO `groupInvites` (`groupId`, `userId`, `inviterName`,`accepted`,`inviteMsg`) VALUES (?,?,?,0,?)");
      $results->execute(array($groupId, $userId, $inviterName,$inviteMsg));
    } catch(Exception $e){
      echo "User invite data insertion error!";
      exit;
    }
  }
}


function addUserToGroup($userId,$groupId, $groupName){
        $alreadyMember = checkUserGroupMembership($userId,$groupId);

        if (!$alreadyMember) {

                global $db;

                try {
            $results = $db->prepare("INSERT INTO `userGroupRelations` (`groupId`, `groupName`,`userId`) VALUES (?,?,?)");
            $results->execute(array($groupId, $groupName, $userId));

            } catch(Exception $e){
                echo "User data insertion error!";
                exit;
            }
        }

}

function setUserGroupStatus($userId,$groupId,$statusCode){
  //status codes
  // 0: Default code. Member: Can see and make posts
  // 1: Administrator. Default code for group creator. Can see and make posts. Can edit group name and info. (Can remove users???)
  // 2: Subscriber. !Not yet implemented! . Can see posts. Public groups only.
  
  //check the status code
  
  if ($statusCode==0 || $statusCode==1 || $statusCode==2) {
    //make sure the user is already in the group 
    $alreadyMember = checkUserGroupMembership($userId,$groupId);
    if ($alreadyMember) {
      global $db;

      try {
        $results = $db->prepare("UPDATE userGroupRelations SET status=? WHERE userId=? AND groupId=?");
        $results->execute(array($statusCode, $userId, $groupId));

      } catch(Exception $e){
        echo "Error: User data insertion error!";
        exit;
      }   
    }
    else{
        echo "Error: User is not group member";
    }
  }
  else {
    echo "Error: Invalid status code";
  } 
}

function getUserGroupStatus($userId, $groupId){
  $alreadyMember = checkUserGroupMembership($userId,$groupId);
    if ($alreadyMember) {
      global $db;

      try {
        $results = $db->prepare("SELECT status FROM userGroupRelations WHERE userId=? AND groupId=?");
        $results->execute(array($userId, $groupId));

      } catch(Exception $e){
        echo "Error: User data insertion error!";
        exit;
      }   
    }
    else{
        echo "Error: User is not group member";
    }

   $statusData = $results->fetchAll(PDO::FETCH_ASSOC); 
   return $statusData;
}

function removeUserFromGroup($groupId, $userId){
        global $db;
        
        try {
    $results = $db->prepare("DELETE FROM `userGroupRelations` 
                                                WHERE userId=? 
                                                AND groupId=?");

    $results->execute(array($userId, $groupId));

    } catch(Exception $e){
        echo "User data insertion error!";
        exit;
    }

}

function getGroupData($groupId){
  global $db;  


  try {
    $results = $db->prepare("SELECT groupName, groupDesc FROM groups WHERE groupId=?");
    $results->execute(array($groupId));

  } catch(Exception $e){
    echo "Data selection error!";
    exit;
  }

  $results = $results->fetchAll(PDO::FETCH_ASSOC);
  return $results;

}

function changeGroupInfo($groupId, $groupName, $groupDesc){
  global $db;

  try {
    $results = $db->prepare("UPDATE groups SET groupName=?, groupDesc=? WHERE groupId=?");
    $results->execute(array($groupName, $groupDesc, $groupId));

  } catch(Exception $e){
    echo "Data selection error!";
    exit;
  }

  echo json_encode("success");

}

?>