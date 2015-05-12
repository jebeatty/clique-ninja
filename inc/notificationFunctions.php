<?php


function UpdateNotifications($groupList, $type){

//for each group, get the list of members
	//create a immediate send array
	//for that set of members, get relevant notification info (SELECT userId, ? (either postFreq or commentFreq depending on ) FROM notifications WHERE userId=? OR userId=? ...)
		//if freq > 0, pending++;
		//else, add them to the immediate send array
	//}
	//sendNotificationToAliases("SENDER NAME made a post/comment in GROUP NAME", $urlforgroupLibrary[need name and id], $immediateSendList);
//}
//we've now gone through all the groups it has been posted to and updated all the notification counters for members registered for notification or sent out notifications

}


//takes in a msg, a url, and an array of aliases
function sendNotificationsToAliases($text, $url, $aliases){
	$data=array("alert"=>$text, "url"=>$url,"aliases"=>$aliases);
	$payload=json_encode($data);
	echo $payload;

	$req=curl_init("https://api.goroost.com/api/push");
	curl_setopt($req, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
	curl_setopt($req, CURLOPT_POSTFIELDS, $payload);  
	curl_setopt($req, CURLOPT_RETURNTRANSFER, true);  
	curl_setopt($req, CURLOPT_USERPWD, "pvneqf9i8wdjrh7yj0pxw000xy3ex3me:hcnizd5dwf9weml5wr4n48z99i701q4u"); 
	curl_setopt($req, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

	$result = curl_exec($req);
	echo $result;
}




?>