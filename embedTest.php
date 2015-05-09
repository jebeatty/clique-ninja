<?php

require_once("inc/config.php");
require(ROOT_PATH."inc/database.php");

echo DB_USER;
echo DB_PASS;


 	try {
	    $results = $db->prepare("SELECT userName FROM users");
	    $results->execute();
	
	    } catch(Exception $e){
	        echo "Data selection error!";
	        exit;
	    }

    $groupData = $results->fetchAll(PDO::FETCH_ASSOC);

    echo var_dump($groupData);



?>