<?php

try {
	$db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";port=".DB_PORT,DB_USER,DB_PASS);
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	$db -> exec("SET NAMES 'utf8'");


} catch (Exception $e) {
	echo "Could not connect to the database.";
	exit;
}

$con=mysqli_connect("localhost",DB_USER,DB_PASS,DB_NAME);

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

?>
