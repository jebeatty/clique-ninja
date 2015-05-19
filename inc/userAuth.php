<?php

// User Authentification Protocols - Login, password recovery, etc.
// We'll start by taking in posted data on user, then calling the appropriate function


session_start();
require_once("../inc/config.php");
require(ROOT_PATH."inc/database.php");
require_once('helperFunctions.php');
require_once('emailHelper.php');


if (isset($_POST["action"])) {
  $action = $_POST["action"];
} else{
  $action = $_GET["action"];
}


if ($action==="recoverPassword") {
  $email=$_POST['email']; 
  $email=stripcslashes($email);
  if (checkForExistingEmail($email)) {
    $userId = getUserIdForEmail($email);
    $password=generateAndSetNewPasswordForUserId($userId);

    $username=getUserNameForId($userId);
    sendRecoveryEmail($email, $password, $username);
    
  }else{
    echo json_encode("No such email");
  }

} else if ($action==="login") {
  $userName=$_POST['username']; 
  $userName=stripcslashes($userName);

  $password=$_POST['password']; 
  $password=stripcslashes($password);

  logInUser($userName, $password);
      
} else if ($action==="signup") {
  $userName=$_POST['username']; 
  $userName=stripcslashes($userName);

  $password=$_POST['password']; 
  $password=stripcslashes($password);

  $email=$_POST['email']; 
  $email=stripcslashes($email);

  if (!checkForExistingUsername($userName)) {
    if (!checkForExistingEmail($email)) {
      signUpUser($userName, $password, $email);
    } else{
      echo json_encode("Email taken");
    }
  } else{
    echo json_encode("Username taken");
  }
  
  
  
} else if ($action==="logout") {
  session_unset();
  if(isset($_COOKIE[session_name()])) {
      setcookie(session_name(),'',time()-3600); # Unset the session id
  }
  session_destroy();
  $json = json_encode("Log out complete");
  echo $json;
}  else{
  $json = json_encode("Action Code Error");
  echo $json;
}


//simple user login function - returns true if the login is successful, false otherwise
function logInUser($username, $password){
	require_once("../inc/config.php");
 	require(ROOT_PATH."inc/database.php");


  //support both plain string and hashed passwords
  $userId=getUserIdForName($username);

  //check to see if we have a matching id
  if ($userId!='') {
    //see if the id has a hashed password
    if(saltExists($userId)){
      //see if the passwords match

      if (checkPassword($password, $userId)) {
        startUserSession($username, $userId);
        echo json_encode(true);
      } else{
        echo json_encode("No such user");
      }
      //else do an unhashed password check
    } else{
      try {
      $results = $db->prepare("SELECT userName, userId, firstName, lastName FROM users WHERE userName=? AND password=? ");
      $results->execute(array($username,$password));
      } catch(Exception $e){
          echo "Data loading error!";
          exit;
      }
      $user = $results->fetchAll(PDO::FETCH_ASSOC);

      //if we find a match, start the session
      if (count($user)>0) {
        startUserSession($username,$userId);
        echo json_encode(true);
      } else{
        echo json_encode("No such user");
      }
    }
  } else{
    echo json_encode("No such user");
  }
}

function startUserSession($username, $userId){
  $_SESSION['username'] = $username;
  $_SESSION['userId'] = $userId;
}


//simple user signup function - inserts the user into the DB then logs them in.
function signUpUser($username, $password, $email){
	require_once("../inc/config.php");
 	require(ROOT_PATH."inc/database.php");

  $salt = randString(12);
  $saltedPassword=$salt.$password;
  $hash = hash('sha256',$saltedPassword);

    try {
    $results = $db->prepare("INSERT INTO users 
                              (userName, password, salt, email)
                              VALUES(?,?,?,?)
                              ");
    $results->execute(array($username,$hash, $salt, $email));
    $insertId = $db->lastInsertId();

    } catch(Exception $e){
        echo "Data loading error!";
        exit;

    }
    $userId = $insertId;
    $userEmail = $email;

    updateInvites($userId, $userEmail);

    logInUser($username,$password);
}

function updateInvites($userId, $userEmail){
  require_once("../inc/config.php");
  require(ROOT_PATH."inc/database.php");

  try {
    $results = $db->prepare("SELECT groupId, inviterName, inviteMsg
                              FROM pendingInvites
                              WHERE userEmail=?
                              ");
    $results->execute(array($userEmail));
  } catch(Exception $e){
      echo "Invite update loading error!";
      exit;

  }
  $groupList = $results->fetchAll(PDO::FETCH_ASSOC);
  if (count($groupList)>0) {
    foreach ($groupList as $group) {
      //for each pending invite, move it to the group invites then delete
      try {
        $results = $db->prepare("INSERT INTO `groupInvites` (`groupId`, `userId`, `inviterName`,`accepted`,`inviteMsg`) VALUES (?,?,?,0,?)");
        $results->execute(array($group['groupId'], $userId, $group['inviterName'], $group['inviteMsg']));

      } catch(Exception $e){
          echo "User invite data insertion error!";
          exit;
      }

      try {
        $results = $db->prepare("DELETE FROM pendingInvites WHERE groupId=? AND userEmail=?");
        $results->execute(array($group['groupId'], $userEmail));

      } catch(Exception $e){
          echo "User invite data deletion error!";
          exit;
      }
    } 
  }
}

function generateAndSetNewPasswordForUserId($userId){

  $newPassword=randString(8);
  setPassword($newPassword,$userId);
  return $newPassword;
}










?>