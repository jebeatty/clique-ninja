<?php
session_start();


if (isset($_GET['newName'])) {
        setUsername($_GET['newName']);

} else if(isset($_GET['newEmail'])){
        setEmail($_GET['newEmail']);

} else if(isset($_GET['newPassword'])) {
        setPassword($_GET['newPassword']);
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

function setUsername($newUsername){
        require_once("../inc/config.php");
        require(ROOT_PATH."inc/database.php");

        $userId=$_SESSION['userId'];
        $password = $_GET['token'];
        $passwordResponse = checkPassword($userId, $password);
        if($passwordResponse=="success"){
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
                $json=json_encode($passwordResponse);
            echo $json;
        }
}       

function setEmail($newEmail){
        require_once("../inc/config.php");
        require(ROOT_PATH."inc/database.php");

        $userId=$_SESSION['userId'];
        $password = $_GET['token'];
        $passwordResponse = checkPassword($userId, $password);
        if($passwordResponse=="success"){
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
                $json=json_encode($passwordResponse);
            echo $json;
        }
}

function setPassword($newPassword){
        require_once("../inc/config.php");
        require(ROOT_PATH."inc/database.php");

        $userId=$_SESSION['userId'];
        $password = $_GET['token'];
        $passwordResponse = checkPassword($userId, $password);
        if($passwordResponse=="success"){
                try {
        $results = $db->prepare("UPDATE users SET password=? WHERE userId=?");
        $results->execute(array($newPassword,$userId));

            } catch(Exception $e){
                echo "Data selection error!";
                exit;
            }
            
            $json=json_encode("success");
            echo $json;
        }
        else{
                $json=json_encode($passwordResponse);
            echo $json;
        }
}

function checkPassword($userId, $password){
        require_once("../inc/config.php");
        require(ROOT_PATH."inc/database.php");

        if ($userId) {
                try {
                $results = $db->prepare("SELECT userName FROM users WHERE userId=? AND password=?");
                $results->execute(array($userId,$password));

            } catch(Exception $e){
                return "data error";
                exit;
            }
            $results=$results->fetchAll(PDO::FETCH_ASSOC);
            if (count($results)>0) {
                return "success";
            }
            else{
                return "invalid password";
            }
            
        }
        else{
                return "invalid userId";
        }
        
}               


?>