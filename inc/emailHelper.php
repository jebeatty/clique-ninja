<?php

require_once("../vendor/autoload.php");
use phpmailer\PHPMailerAutoload;

function sendRecoveryEmail($email, $password, $username){
	$mail = new PHPMailer;

	$mail->SMTPDebug = 1;                               // Enable verbose debug output

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'mail.discoverclique.com';  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'recovery@discoverclique.com';                 // SMTP username
	$mail->Password = 'dx4tpWhm';                           // SMTP password
	$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 587;                                    // TCP port to connect to

	$mail->From = 'recovery@discoverclique.com';
	$mail->FromName = 'Clique';
	$mail->addAddress($email);     // Add a recipient

	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = 'Your Clique Password';
	

	$body = "We received a request to recover your Clique account information. <br><br>";
	$body .= "Your Username:".$username."<br>";
	$body .= "Your New Password:".$password."<br><br>";
	$body .= "You can change your password through the Settings button after logging in.";
	$body .=" Let us know if you have any questions, <br>";
	$body .=" The Clique Team";

	$mail->Body    = $body;
	$mail->AltBody = $body;

	if(!$mail->send()) {
	    echo 'Message could not be sent.';
	    echo 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
	    echo 'Message has been sent';
	}
}

function sendShareEmail($recipients, $subject, $body){
	$mail = new PHPMailer;

	$mail->SMTPDebug = 1;                               // Enable verbose debug output

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'mail.discoverclique.com';  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'shared@discoverclique.com';                 // SMTP username
	$mail->Password = 'dx4tpWhm';                           // SMTP password
	$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 587;                                    // TCP port to connect to

	$mail->From = 'shared@discoverclique.com';
	$mail->FromName = 'Clique';

	foreach ($recipients as $email) {
		$mail->addAddress($email); 
	}

	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = $subject;

	$mail->Body    = $body;
	$mail->AltBody = $body;

	if(!$mail->send()) {
	    echo 'Message could not be sent.';
	    echo 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
	    echo 'Message has been sent';
	}
}

function sendInviteEmail($userInvited,$inviterName, $groupName){

	$mail = new PHPMailer;

	$mail->SMTPDebug = 3;                               // Enable verbose debug output

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'mail.discoverclique.com';  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'invitation@discoverclique.com';                 // SMTP username
	$mail->Password = 'dx4tpWhm';                           // SMTP password
	$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 587;                                    // TCP port to connect to

	$mail->From = 'invitation@discoverclique.com';
	$mail->FromName = 'Clique';
	$mail->addAddress($userInvited);     // Add a recipient

	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = 'Clique Invitation from '.$inviterName;
	

	$body = "You've been invited to join a group on Clique by ".$inviterName." as part of our private beta! <br><br>";
	$body .= " Clique is the best way to share and find recommendations about what to read, watch, or enjoy from around the web directly with your friends. <br><br>";
	$body .=" Go to https://www.discoverclique.com/doublesecretbeta/ and sign up using this email (".$userInvited."). Once you've joined, you should see a pending invite for the &#34;".$groupName."&#34; group. Join up and start sharing the most interesting things you see from around the web! <br><br>";
	$body .=" Let us know if you have any questions, <br>";
	$body .=" The Clique Team";

	$mail->Body    = $body;
	$mail->AltBody = $body;

	if(!$mail->send()) {
	    echo 'Message could not be sent.';
	    echo 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
	    echo 'Message has been sent';
	}


}


?>