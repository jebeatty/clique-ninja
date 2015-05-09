<?php

//search program for autocomplete
require_once("../inc/config.php");
require(ROOT_PATH."inc/database.php");

$searchTerm = $_GET['term'];
$SQLQuery = "SELECT email FROM users WHERE email LIKE '%".$searchTerm."%' LIMIT 15";

try{
	$results = $db->prepare($SQLQuery);
    $results->execute();
}
catch(Exception $e){
	echo "Data loading error!";
	exit;

}

$dirtyResults = $results->fetchAll(PDO::FETCH_ASSOC);


$cleanResults = array_map(function($result){
	return $result['email'];
}, $dirtyResults);

$json = json_encode($cleanResults);
echo $json;


?>