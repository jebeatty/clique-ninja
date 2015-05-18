<?php
session_start();

require_once("../inc/config.php");
require(ROOT_PATH."inc/database.php");
require_once('helperFunctions.php');


if (isset($_GET['newName'])) {
        $userId=$_SESSION['userId'];
        $oldPassword = $_GET['token'];
        $newUsername=$_GET['newName'];
        changeUsername($newUsername, $oldPassword, $userId);

} else if(isset($_GET['newEmail'])){
        $userId=$_SESSION['userId'];
        $oldPassword = $_GET['token'];
        $newEmail = $_GET['newEmail'];
        changeEmail($newEmail, $oldPassword, $userId);

} else if(isset($_GET['newPassword'])) {
        $userId=$_SESSION['userId'];
        $oldPassword = $_GET['token'];
        $newPassword = $_GET['newPassword'];
        changePassword($newPassword, $oldPassword, $userId);
} else{
        getUserData();
}


function getUserData(){
        require_once("../inc/config.php");
        require(ROOT_PATH."inc/database.php");

        $userId=$_SESSION['userId'];
        

        try {
        $results = $db->prepare("SELECT userName, email FROM users WHERE userId=?");
        $results->execute(array($userId));

    } catch(Exception $e){
        echo "Data selection error!";
        exit;
    }

    $userData = $results->fetchAll(PDO::FETCH_ASSOC);
    $json=json_encode($userData);
    echo $json;

}

function changeUsername($newUsername,  $oldPassword, $userId){
        require_once("../inc/config.php");
        require(ROOT_PATH."inc/database.php");

        $authenticated = authenticateUser($userId, $oldPassword);
        if($authenticated){
        try {
        $results = $db->prepare("UPDATE users SET userName=? WHERE userId=?");
        $results->execute(array($newUsername,$userId));

        } catch(Exception $e){
        echo "Data selection error!";
        exit;
        }

        $json=json_encode("success");
        echo $json;
        }
        else{
            $json=json_encode($authenticated);
            echo $json;
        }
}       

function changeEmail($newEmail, $oldPassword, $userId){
        require_once("../inc/config.php");
        require(ROOT_PATH."inc/database.php");

        $authenticated = authenticateUser($userId, $oldPassword);
        if($authenticated){
                try {
        $results = $db->prepare("UPDATE users SET email=? WHERE userId=?");
        $results->execute(array($newEmail,$userId));

            } catch(Exception $e){
                echo "Data selection error!";
                exit;
            }
            
            $json=json_encode("success");
            echo $json;
        }
        else{
            $json=json_encode($authenticated);
            echo $json;
        }
}

function changePassword($newPassword, $oldPassword, $userId){
        require_once("../inc/config.php");
        require(ROOT_PATH."inc/database.php");

        $authenticated = authenticateUser($userId, $oldPassword);
        if($authenticated){
            setPassword($newPassword,$userId);
            $json=json_encode("success");
            echo $json;
        }
        else{
            $json=json_encode("invalid password");
            echo $json;
        }
}
            


?>