<?php
$data=array("alert"=>"API Test Push", "url"=>"https://www.google.com","aliases"=>["3"]);
$payload=json_encode($data);


$req=curl_init("https://api.goroost.com/api/push");
curl_setopt($req, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
curl_setopt($req, CURLOPT_POSTFIELDS, $payload);  
curl_setopt($req, CURLOPT_RETURNTRANSFER, true);  
curl_setopt($req, CURLOPT_USERPWD, "pvneqf9i8wdjrh7yj0pxw000xy3ex3me:hcnizd5dwf9weml5wr4n48z99i701q4u"); 
curl_setopt($req, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

$result = curl_exec($req);

?>