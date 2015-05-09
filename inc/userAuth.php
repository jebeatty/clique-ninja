<?php

// User Authentification Protocols - Login, password recovery, etc.
// We'll start by taking in posted data on user, then calling the appropriate function


session_start();
require_once("../inc/config.php");
require(ROOT_PATH."inc/database.php");

$action = $_POST['action'];


    $userName=$_POST['username']; 
    $userName=stripcslashes($userName);
    $userName=mysqli_real_escape_string($con,$userName);


    $password=$_POST['password']; 
    $password=stripcslashes($password);
    $password=mysqli_real_escape_string($con,$password);

if($userName !='' && $password !=''){

    if ($action==="login") {
        logInUser($userName, $password);
       

    } else if ($action==="signup") {
        $email=$_POST['email']; 
        $email=stripcslashes($email);
        $email=mysqli_real_escape_string($con,$email);
        if($email!=''){
        	signUpUser($userName, $password, $email);
        } else{
        	$json = json_encode("Missing Signup Data");
       		 echo $json;
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

}else{
	$json = json_encode("Missing Login Data");
        echo $json;

}




//simple user login function - returns true if the login is successful, false otherwise
function logInUser($username, $password){
	require_once("../inc/config.php");
 	require(ROOT_PATH."inc/database.php");

 	try {
    $results = $db->prepare("SELECT userName, userId, firstName, lastName
                              FROM users
                              WHERE userName=? AND password=?
                              ");
    $results->execute(array($username,$password));

    } catch(Exception $e){
        echo "Data loading error!";
        exit;

    }

    $user = $results->fetchAll(PDO::FETCH_ASSOC);

    if (count($user)>0) {
    	$json = json_encode(true);
    	$_SESSION['username'] = $username;
    	$_SESSION['userId'] = $user[0]['userId'];
        session_regenerate_id();
    }
    else{
    	$json = json_encode("No such user");

    }
    echo $json;
    
}

//simple user signup function - inserts the user into the DB then logs them in.
function signUpUser($username, $password, $email){
	require_once("../inc/config.php");
 	require(ROOT_PATH."inc/database.php");

    try {
    $results = $db->prepare("INSERT INTO users 
                              (userName, password,email)
                              VALUES(?,?,?)
                              ");
    $results->execute(array($username,$password,$email));
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
    $results = $db->prepare("SELECT groupId, inviterName
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
        $results = $db->prepare("INSERT INTO `groupInvites` (`groupId`, `userId`, `inviterName`,`accepted`) VALUES (?,?,?,0)");
        $results->execute(array($group['groupId'], $userId, $group['inviterName']));

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



?>