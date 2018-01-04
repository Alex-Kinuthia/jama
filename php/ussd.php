<?php
//1. Ensure ths code runs only after a POST from AT
if(!empty($_POST) && !empty($_POST['phoneNumber'])){
	require_once('dbConnector.php');
	require_once('AfricasTalkingGateway.php');
	require_once('config.php');
	//2. receive the POST from AT
	$sessionId     =$_POST['sessionId'];
	$serviceCode   =$_POST['serviceCode'];
	$phoneNumber   =$_POST['phoneNumber'];
	$text          =$_POST['text'];
if ( $text == "" ) {
		 // This is the first request. Note how we start the response with CON
		 $response = "CON What would you want to check \n";
		 $response .= "1. My Account \n";
		 $response .= "2. My Phone Number \n";
		 $response .= "3. Register Account \n";

}
else if ( $text == "1" ) {
// Business logic for first level response
$response = "CON Choose account information you want to view \n";
$response .= "1. Account number \n";
$response .= "2. Account balance";

}
else if($text == "2") {
// Business logic for first level response
// This is a terminal request. Note how we start the response with END
$response = "END Your phone number is $phoneNumber";

}

else if($text == "3") {
// Business logic for first level response
// This is a terminal request. Note how we start the response with END
$response = "CON Please enter your name";
 $response = "CON Name not supposed to be empty. Please enter your name \n";

}

else if($text == "1*1") {
// This is a second level response where the user selected 1 in the first instance
$accountNumber = "ACC1001";
// This is a terminal request. Note how we start the response with END
$response = "END Your account number is $accountNumber";
}

else if ( $text == "1*2" ) {
		 // This is a second level response where the user selected 1 in the first instance
		 $balance = "KES 10,000";
		 // This is a terminal request. Note how we start the response with END
		 $response = "END Your balance is $balance";
}
// Print the response onto the page so that our gateway can read it
header('Content-type: text/plain');
echo $response;
}
// DONE!!!
?>
