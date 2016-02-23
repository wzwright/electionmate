<?php
session_start();
$id=$_GET['id'];
$respondents=json_decode($_GET['mailTo'],true);
foreach($respondents as $email => $value){
	$email=str_replace("|",".",$email);
	$mailText="Vote <a href='http://localhost/electionmate/vote.php?id=".$value['id']."&owner=".$_SESSION['user']."&poll=".$id."&email=".$email."'>here</a>";
	$postFields = array(
		'api_user'=>'username',
		'api_key'=>'password',
		'to'=>$email, 
		'from'=>$_SESSION['email'], 
		'subject'=>'Time to vote! '.$_GET['title'], 
		'html'=>$mailText
		);
	$ch = curl_init("https://api.sendgrid.com/api/mail.send.json");
	curl_setopt($ch, CURLOPT_SSLVERSION, 6);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
	$result = curl_exec($ch);
	print_r($result);
}
?>