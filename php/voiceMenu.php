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
 //3. Explode the text to get the value of the latest interaction - think 1*1
 $textArray=explode('*', $text);
 $userResponse=trim(end($textArray));
 //4. Set the default level of the user
 $level=0;
 //5. Check the level of the user from the DB and retain default level if none is found for this session
 $sql = "select level from session_levels where session_id ='".$sessionId." '";
 $levelQuery = $db->query($sql);
 if($result = $levelQuery->fetch_assoc()) {
     $level = $result['level'];
 }
 //6. Create an account and ask questions later
 $sql6 = "SELECT * FROM account WHERE phoneNumber LIKE '%".$phoneNumber."%' LIMIT 1";
 $acQuery=$db->query($sql6);
 if(!$acAvailable=$acQuery->fetch_assoc()){
   $sql1A = "INSERT INTO account (`phoneNumber`) VALUES('".$phoneNumber."')";
   $db->query($sql1A);
 }
 //7. Check if the user is in the db
 $sql7 = "SELECT * FROM microfinance WHERE phoneNumber LIKE '%".$phoneNumber."%' LIMIT 1";
 $userQuery=$db->query($sql7);
 $userAvailable=$userQuery->fetch_assoc();
 //8. Check if the user is available (yes)->Serve the menu; (no)->Register the user
 if($userAvailable && $userAvailable['city']!=NULL && $userAvailable['name']!=NULL){
   //9. Serve the Services Menu (if the user is fully registered,
   //level 0 and 1 serve the basic menus, while the rest allow for financial transactions)
   if($level==0 || $level==1){
     //9a. Check that the user actually typed something, else demote level and start at home
     switch ($userResponse) {
         case "":
             if($level==0){
               //9b. Graduate user to next level & Serve Main Menu
               $sql9b = "INSERT INTO `session_levels`(`session_id`,`phoneNumber`,`level`) VALUES('".$sessionId."','".$phoneNumber."',1)";
               $db->query($sql9b);
               //Serve our services menu
           $response = "CON Welcome to Garissa County, " . $userAvailable['name']  . ". Choose a service.\n";
           $response .= " 1. Customer\n";
           $response .= " 2. Agent \n";


             // Print the response onto the page so that our gateway can read it
             header('Content-type: text/plain');
             echo $response;
             }
             break;
         case "0":
             if($level==0){
               //9b. Graduate user to next level & Serve Main Menu
               $sql9b = "INSERT INTO `session_levels`(`session_id`,`phoneNumber`,`level`) VALUES('".$sessionId."','".$phoneNumber."',1)";
               $db->query($sql9b);
               //Serve our services menu
           $response = "CON Welcome to Garissa County, " . $userAvailable['username']  . ". Choose a service.\n";

           $response .= " 1. Customer\n";
           $response .= " 2. Agent \n";


             // Print the response onto the page so that our gateway can read it
             header('Content-type: text/plain');
             echo $response;
             }
             break;

         case "1":
           if($level==1){
             //9e. Ask how much and Launch the Mpesa Checkout to the user
           $response = "CON Please choose service?\n";
           $response .= " 1. Parking.\n";
           $response .= " 2. Fees.\n";
           $response .= " 3. Tax.\n";
           //Update sessions to level 9
             $sqlLvl9="UPDATE `session_levels` SET `level`=7 where `session_id`='".$sessionId."'";
             $db->query($sqlLvl9);
             // Print the response onto the page so that our gateway can read it
             header('Content-type: text/plain');
             echo $response;
           }
             break;

         case "2":
           if($level==1){
             //9e. Ask how much and Launch the Mpesa Checkout to the user
            $response = "CON Agent.?\n";
            $response .= " 1. Animal Market.\n";
            $response .= " 2. Stock Market.\n";
           //Update sessions to level 12
             $sqlLvl12="UPDATE `session_levels` SET `level`=12 where `session_id`='".$sessionId."'";
             $db->query($sqlLvl12);
             // Print the response onto the page so that our gateway can read it
             header('Content-type: text/plain');
             echo $response;
           }
             break;

             case "3":
               if($level==1){
                 //9e. Ask how much and Launch the Mpesa Checkout to the user
                $response = "CON Tax.?\n";
                $response .= " 1. Service.\n";
                $response .= " 2. Community.\n";
                $response .= " 3. Unavailable.\n";

               //Update sessions to level 12
                 $sqlLvl12="UPDATE `session_levels` SET `level`=12 where `session_id`='".$sessionId."'";
                 $db->query($sqlLvl12);
                 // Print the response onto the page so that our gateway can read it
                 header('Content-type: text/plain');
                 echo $response;
               }
                 break;

             case "1*1":
               if($level==1){
                 //9e. Ask how much and Launch the Mpesa Checkout to the user
               $response = "CON Proceed with parking payment.?\n";
               $response .= " 1. Town 150/.\n";
               $response .= " 2. Province 100/.\n";
               $response .= " 3. Iftin 100/.\n";
               $response .= " 4. Other 100/.\n";
   //Update sessions to level 12
                 $sqlLvl12="UPDATE `session_levels` SET `level`=6 where `session_id`='".$sessionId."'";
                 $db->query($sqlLvl12);
                 // Print the response onto the page so that our gateway can read it
                 header('Content-type: text/plain');
                 echo $response;
               }
               break;

               case "1*1*1":
                 if($level==1){
                   //9e. Ask how much and Launch the Mpesa Checkout to the user
                 $response = "CON Proceed with payment.?\n";
                 $response .= " 1. 150 Shilling.\n";
     //Update sessions to level 12
                   $sqlLvl12="UPDATE `session_levels` SET `level`=7 where `session_id`='".$sessionId."'";
                   $db->query($sqlLvl12);
                   // Print the response onto the page so that our gateway can read it
                   header('Content-type: text/plain');
                   echo $response;
                 }
                 break;

                 case "1*1*2":
                   if($level==1){
                     //9e. Ask how much and Launch the Mpesa Checkout to the user
                   $response = "CON Proceed with payment.?\n";
                   $response .= " 1. 100 Shilling.\n";
       //Update sessions to level 12
                     $sqlLvl12="UPDATE `session_levels` SET `level`=7 where `session_id`='".$sessionId."'";
                     $db->query($sqlLvl12);
                     // Print the response onto the page so that our gateway can read it
                     header('Content-type: text/plain');
                     echo $response;
                   }
                   break;

                   case "1*1*3":
                     if($level==1){
                       //9e. Ask how much and Launch the Mpesa Checkout to the user
                     $response = "CON Proceed with payment.?\n";
                     $response .= " 1. 100 Shilling.\n";
         //Update sessions to level 12
                       $sqlLvl12="UPDATE `session_levels` SET `level`=7 where `session_id`='".$sessionId."'";
                       $db->query($sqlLvl12);
                       // Print the response onto the page so that our gateway can read it
                       header('Content-type: text/plain');
                       echo $response;
                     }
                     break;
                     case "1*1*4":
                       if($level==1){
                         //9e. Ask how much and Launch the Mpesa Checkout to the user
                       $response = "CON Proceed with payment.?\n";
                       $response .= " 1. 100 Shilling.\n";
           //Update sessions to level 12
                         $sqlLvl12="UPDATE `session_levels` SET `level`=7 where `session_id`='".$sessionId."'";
                         $db->query($sqlLvl12);
                         // Print the response onto the page so that our gateway can read it
                         header('Content-type: text/plain');
                         echo $response;
                       }
                       break;

               case "1*2":
                 if($level==1){
                   //9e. Ask how much and Launch the Mpesa Checkout to the user
                 $response = "CON Fees.?\n";
                 $response .= " 1. Stones 4050/.\n";
                 $response .= " 2. Timber 2530/.\n";

          //Update sessions to level 12
                   $sqlLvl12="UPDATE `session_levels` SET `level`=9 where `session_id`='".$sessionId."'";
                   $db->query($sqlLvl12);
                   // Print the response onto the page so that our gateway can read it
                   header('Content-type: text/plain');
                   echo $response;
                 }
                 break;

                 case "1*2*1":
                   if($level==1){
                     //9e. Ask how much and Launch the Mpesa Checkout to the user
                   $response = "CON Proceed with payment.?\n";
                   $response .= " 1. 4050 Shilling.\n";
       //Update sessions to level 12
                     $sqlLvl12="UPDATE `session_levels` SET `level`=8 where `session_id`='".$sessionId."'";
                     $db->query($sqlLvl12);
                     // Print the response onto the page so that our gateway can read it
                     header('Content-type: text/plain');
                     echo $response;
                   }
                   break;
                   case "1*2*2":
                     if($level==1){
                       //9e. Ask how much and Launch the Mpesa Checkout to the user
                     $response = "CON Proceed with payment.?\n";
                     $response .= " 1. 2530 Shilling.\n";
         //Update sessions to level 12
                       $sqlLvl12="UPDATE `session_levels` SET `level`=8 where `session_id`='".$sessionId."'";
                       $db->query($sqlLvl12);
                       // Print the response onto the page so that our gateway can read it
                       header('Content-type: text/plain');
                       echo $response;
                     }
                     break;
         case "4":
           if($level==1){
           // Find the user in the db
           $sql7 = "SELECT * FROM microfinance WHERE phoneNumber LIKE '%".$phoneNumber."%' LIMIT 1";
           $userQuery=$db->query($sql7);
           $userAvailable=$userQuery->fetch_assoc();
             // Find the account
           $sql7a = "SELECT * FROM account WHERE phoneNumber LIKE '%".$phoneNumber."%' LIMIT 1";
           $BalQuery=$db->query($sql7a);
           $newBal = 0.00; $newLoan = 0.00;
           if($BalAvailable=$BalQuery->fetch_assoc()){
           $newBal = $BalAvailable['balance'];
           $newLoan = $BalAvailable['loan'];
           }
           //Respond with user Balance
           $response = "END Your account statement.\n";
           $response .= "Garissa County.\n";
           $response .= "Name: ".$userAvailable['name']."\n";
           $response .= "City: ".$userAvailable['city']."\n";
           $response .= "Balance: ".$newBal."\n";
           $response .= "Loan: ".$newLoan."\n";
             // Print the response onto the page so that our gateway can read it
             header('Content-type: text/plain');
             echo $response;
           }
            break;
            default:
   					 if($level==1){
   							 // Return user to Main Menu & Demote user's level
   						 $response = "CON You have to choose a service.\n";
   						 $response .= "Press 0 to go back.\n";
   						 //demote
   						 $sqlLevelDemote="UPDATE `session_levels` SET `level`=0 where `session_id`='".$sessionId."'";
   						 $db->query($sqlLevelDemote);

   						 // Print the response onto the page so that our gateway can read it
   						 header('Content-type: text/plain');
   						 echo $response;
   					 }
   		 }
   	 }else{
   		 // Financial Services Delivery
   		 switch ($level){
   						 case 6:
   							 //12. Receive loan
   						 switch ($userResponse) {
   								 case "1":
   									 //End session
   									 $response = "END You are paying 15/. Please do send 15/ to 0742588200\n";
   									 // Print the response onto the page so that our gateway can read it
   									 header('Content-type: text/plain');
   									 echo $response;
   									 $balance=15;
   								 //Create pending record in checkout to be cleared by cronjobs
   								 $sql9a = "UPDATE account SET `balance`='".$balance."' WHERE `phonenumber` = '". $phoneNumber ."'";
   								$db->query($sql9a);
   			 //11f. Change level to 0
   			$sql9a = "INSERT INTO account (`balance`) VALUES('".$balance."',1)";
   										 $db->query($sql9a);
   								 break;
   						 }
   					 break;
   				 case 7:
   					 //12. Pay loan
   				 switch ($userResponse) {
   						 case "1*":
   							 //End session

   										 $response = "END You are paying 150/. Please do send 150/ to 0742588200\n";
   							 // Print the response onto the page so that our gateway can read it
   							 header('Content-type: text/plain');
   							 echo $response;
   							 $amount=100;
   						 //Create pending record in checkout to be cleared by cronjobs
   								 $sql12a = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
   								 $db->query($sql12a);
   							 break;
   				 }
   						 break;
   						 case 8:
   							 //12. Pay loan
   						 switch ($userResponse) {
   								 case "1*2*1":
   									 //End session

   												 $response = "END You are paying 4050/. Please do send 4050/ to 0742588200\n";
   									 // Print the response onto the page so that our gateway can read it
   									 header('Content-type: text/plain');
   									 echo $response;
   									 $amount=4050;
   								 //Create pending record in checkout to be cleared by cronjobs
   										 $sql12a = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
   										 $db->query($sql12a);
   									 break;

                     case "1*2*2":
                         //End session

                               $response = "END You are paying 2530/. Please do send 2530/ to 0742588200\n";
                         // Print the response onto the page so that our gateway can read it
                         header('Content-type: text/plain');
                         echo $response;
                         $amount=4050;
                       //Create pending record in checkout to be cleared by cronjobs
                           $sql12a = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
                           $db->query($sql12a);
                         break;
   						 }
   																 break;
   				 default:
   					 //11g. Request for city again
   		 }
   	 }
    } else{
   	 //10. Check that user response is not empty
   	 if($userResponse==""){
   		 //10a. On receiving a Blank. Advise user to input correctly based on level
   		 switch ($level) {
   				 case 0:
   					 //10b. Graduate the user to the next level, so you dont serve them the same menu
   						$sql10b = "INSERT INTO `session_levels`(`session_id`, `phoneNumber`,`level`) VALUES('".$sessionId."','".$phoneNumber."', 1)";
   						$db->query($sql10b);
   						//10c. Insert the phoneNumber, since it comes with the first POST
   						$sql10c = "INSERT INTO microfinance(`phonenumber`) VALUES ('".$phoneNumber."')";
   						$db->query($sql10c);
   						//10d. Serve the menu request for name
   						$response = "CON Please enter your name";
   					 // Print the response onto the page so that our gateway can read it
   					 header('Content-type: text/plain');
   					 echo $response;
   						 break;
   				 case 1:
   					 //10e. Request again for name - level has not changed...
   						 $response = "CON Name not supposed to be empty. Please enter your name \n";
   					 // Print the response onto the page so that our gateway can read it
   					 header('Content-type: text/plain');
   					 echo $response;
   						 break;
   				 case 2:
   					 //10f. Request for city again --- level has not changed...
   				 $response = "CON Id No not supposed to be empty. Please reply with your Id No \n";
   					 // Print the response onto the page so that our gateway can read it
   					 header('Content-type: text/plain');
   					 echo $response;
   						 break;
   				 default:
   					 //10g. End the session
   				 $response = "END Apologies, something went wrong...5 \n";
   					 // Print the response onto the page so that our gateway can read it
   					 header('Content-type: text/plain');
   					 echo $response;
   						 break;

   		 }
   	 }else{
   		 //11. Update User table based on input to correct level
   		 switch ($level) {
   				 case 0:
   					 //10b. Graduate the user to the next level, so you dont serve them the same menu
   						$sql10b = "INSERT INTO `session_levels`(`session_id`, `phoneNumber`,`level`) VALUES('".$sessionId."','".$phoneNumber."', 1)";
   						$db->query($sql10b);
   						//10c. Insert the phoneNumber, since it comes with the first POST
   						$sql10c = "INSERT INTO microfinance (`phonenumber`) VALUES ('".$phoneNumber."')";
   						$db->query($sql10c);
   						//10d. Serve the menu request for name
   						$response = "CON Please enter your name";
   					 // Print the response onto the page so that our gateway can read it
   					 header('Content-type: text/plain');
   						 echo $response;
   					 break;
   				 case 1:
   					 //11b. Update Name, Request for city
   						 $sql11b = "UPDATE microfinance SET `name`='".$userResponse."' WHERE `phonenumber` LIKE '%". $phoneNumber ."%'";
   						 $db->query($sql11b);
   						 //11c. We graduate the user to the city level
   						 $sql11c = "UPDATE `session_levels` SET `level`=2 WHERE `session_id`='".$sessionId."'";
   						 $db->query($sql11c);
   						 //We request for the city
   						 $response = "CON Please enter your Id No";
   					 // Print the response onto the page so that our gateway can read it
   					 header('Content-type: text/plain');
   						 echo $response;
   					 break;
   					 case 3:
   						//11b. Update City, Request for deposit
   							$sql11b = "UPDATE microfinance SET `deposit`='".$userResponse."' WHERE `phonenumber` LIKE '%". $phoneNumber ."%'";
   							$db->query($sql11b);
   							//11c. We graduate the user to the deposit level
   							$sql11c = "UPDATE `session_levels` SET `level`=2 WHERE `session_id`='".$sessionId."'";
   							$db->query($sql11c);
   							//We request for the city
   							$response = "CON Please deposit ammount";
   						// Print the response onto the page so that our gateway can read it
   						header('Content-type: text/plain');
   							echo $response;
   						break;
   			 case 2:
   					 //11d. Update city
   						 $sql11d = "UPDATE microfinance SET `city`='".$userResponse."' WHERE `phonenumber` = '". $phoneNumber ."'";
   						 $db->query($sql11d);
   						 //11d. Update deposit
   							 $sql11d = "UPDATE microfinance SET `balance`='".$userResponse."' WHERE `phonenumber` = '". $phoneNumber ."'";
   							 $db->query($sql11d);
   					 //11e. Change level to 0
   						 $sql11e = "INSERT INTO `session_levels`(`session_id`,`phoneNumber`,`level`) VALUES('".$sessionId."','".$phoneNumber."',1)";
   						 $db->query($sql11e);
   				 //11f. Serve the menu request for name
   				 $response = "END You have been successfully registered. Dial *384*032# to choose a service.";
   					 // Print the response onto the page so that our gateway can read it
   					 header('Content-type: text/plain');
   					 echo $response;
   					 break;
   				 default:
   					 //11g. Request for city again
   				 $response = "END Apologies, something went wrong...6 \n";
   					 // Print the response onto the page so that our gateway can read it
   					 header('Content-type: text/plain');
   					 echo $response;
   					 break;
   		 }
   	 }
    }
   }
   ?>
