<?php
session_start();

require_once("../vendor/autoload.php");
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphUser;


/*
require_once("vendor/autoload.php");
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;

FacebookSession::setDefaultApplication('432912816865715', '8e7e5fc1b821813c0e341b9385d9f3b9');

$helper = new FacebookRedirectLoginHelper('inc/fbLogin.php');
echo '<a href="' . $helper->getLoginUrl() . '">Login with Facebook</a>'
*/
FacebookSession::setDefaultApplication('432912816865715', '8e7e5fc1b821813c0e341b9385d9f3b9');
$helper = new FacebookRedirectLoginHelper('https://www.discoverclique.com/doublesecretbeta/inc/fbLogin.php');
try {
    $session = $helper->getSessionFromRedirect();
   
    } catch( FacebookRequestException $ex ) {
      	
      	echo "FB Login Failure:";
      	$FBerror = $ex->getErrorType();
      	echo $FBerror;
    } catch( Exception $ex ) {
      echo "Local failure:";
      echo $ex->getMessage();
    }
if ($session) {
	//echo "facebook session login success...";
	try {
		$user_profile = (new FacebookRequest($session, 'GET', '/me'))->execute()->getGraphObject(GraphUser::className());

		} catch(FacebookRequestException $e) {

			echo "Exception occured, code: " . $e->getCode();
			echo " with message: " . $e->getMessage();

		}   
	loginFBUser($user_profile, FALSE); //now that we have the user, lets log them in
}
else{
  //echo "FB login failure...session not found...";
}

function loginFBUser($user_profile, $loop){
	require_once("../inc/config.php");
 	require(ROOT_PATH."inc/database.php");
  //echo "attempting clique login...";
	try {
    	$results = $db->prepare("SELECT userName, userId, firstName, lastName
                              FROM users
                              WHERE FBId=?
                              ");
	    $results->execute(array($user_profile->getProperty('id')));

	    } catch(Exception $e){
	        echo "Data loading error!";
	        exit;

	    }
	$user = $results->fetchAll(PDO::FETCH_ASSOC);
	if (count($user)>0) {
		//we found the right user
		setSessionParams($user);
		header("Location: ../recent.php");
    	}
    else {
    	//no such user - let's sign them up! But only if this isn't going to start an infinite loop...
    	if ($loop) {
    		exit;
    	}
    	else{
    		signupFBUser($user_profile);
    	}
    	
    }
}

function signupFBUser($user_profile){
	require_once("../inc/config.php");
 	require(ROOT_PATH."inc/database.php");

	try {
		$results = $db->prepare("INSERT INTO `users` 
                          (`userName`, 
                          `FBId`, 
                          `firstName`, 
                          `lastName`, 
                          `email`)
                          VALUES(?,?,?,?,?)
                          ");
	
		$results->execute(array($user_profile->getProperty('name'),
								$user_profile->getProperty('id'),
								$user_profile->getProperty('first_name'),
								$user_profile->getProperty('last_name'),
								$user_profile->getProperty('email')));
    $insertId = $db->lastInsertId();
  } catch(Exception $e){
      echo "Data loading error!";
      exit;

  }

  $userId = $insertId;
  $userEmail = $user_profile->getProperty('email');

  updateInvites($userId, $userEmail);
  loginFBUser($user_profile, TRUE);

}

function setSessionParams($user){
 // echo "setting session parameters";
	$_SESSION['username'] = $user[0]['userName'];
	$_SESSION['userId'] = $user[0]['userId'];
	session_regenerate_id();
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

<html>
         <head>
         	<title>Clique</title>
         </head>
         <body>
         	If you are seeing this text, something has gone terribly, terribly wrong with the Facebook login. Please try again or use the sign-up facebook method.
         </body>
         </html>