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
               $response .= " 3. Check Account \n";


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
               $response = "CON Welcome to Garissa County, " . $userAvailable['name']  . ". Choose a service.\n";
               $response .= " 1. Customer\n";
               $response .= " 2. Agent \n";
               $response .= " 3. Check Account \n";




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
						 $sqlLvl9="UPDATE `session_levels` SET `level`=9 where `session_id`='".$sessionId."'";
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
             $response .= " 2. Market.\n";
             $response .= " 3. Check car payment status.\n";
	 //Update sessions to level 12
						 $sqlLvl12="UPDATE `session_levels` SET `level`=12 where `session_id`='".$sessionId."'";
						 $db->query($sqlLvl12);
						 // Print the response onto the page so that our gateway can read it
						 header('Content-type: text/plain');
						 echo $response;
					 }
						break;


							case "1*2":
								if($level==1){
									//9e. Ask how much and Launch the Mpesa Checkout to the user
								$response = "CON Proceed with payment.?\n";
								$response .= " 1. 17 Shilling.\n";
		//Update sessions to level 12
									$sqlLvl12="UPDATE `session_levels` SET `level`=7 where `session_id`='".$sessionId."'";
									$db->query($sqlLvl12);
									// Print the response onto the page so that our gateway can read it
									header('Content-type: text/plain');
									echo $response;
								}
								break;
								case "1*3":
									if($level==1){
										//9e. Ask how much and Launch the Mpesa Checkout to the user
									$response = "CON Proceed with payment.?\n";
									$response .= " 1. 20 Shilling.\n";
			//Update sessions to level 12
										$sqlLvl12="UPDATE `session_levels` SET `level`=8 where `session_id`='".$sessionId."'";
										$db->query($sqlLvl12);
										// Print the response onto the page so that our gateway can read it
										header('Content-type: text/plain');
										echo $response;
									}
									break;

									case "2*1":
										if($level==1){
											//9e. Ask how much and Launch the Mpesa Checkout to the user
										$response = "CON Proceed with payment.?\n";
										$response .= " 1. 1000 Shilling.\n";
				//Update sessions to level 12
											$sqlLvl12="UPDATE `session_levels` SET `level`=9 where `session_id`='".$sessionId."'";
											$db->query($sqlLvl12);
											// Print the response onto the page so that our gateway can read it
											header('Content-type: text/plain');
											echo $response;
										}
										break;
										case "2*2":
											if($level==1){
												//9e. Ask how much and Launch the Mpesa Checkout to the user
											$response = "CON Proceed with payment.?\n";
											$response .= " 1. 2000 Shilling.\n";
					//Update sessions to level 12
												$sqlLvl12="UPDATE `session_levels` SET `level`=10 where `session_id`='".$sessionId."'";
												$db->query($sqlLvl12);
												// Print the response onto the page so that our gateway can read it
												header('Content-type: text/plain');
												echo $response;
											}
											break;
											case "2*3":
												if($level==1){
													//9e. Ask how much and Launch the Mpesa Checkout to the user
												$response = "CON Proceed with payment.?\n";
												$response .= " 1. 3000 Shilling.\n";
						//Update sessions to level 12
													$sqlLvl12="UPDATE `session_levels` SET `level`=11 where `session_id`='".$sessionId."'";
													$db->query($sqlLvl12);
													// Print the response onto the page so that our gateway can read it
													header('Content-type: text/plain');
													echo $response;
												}
												break;
												case "2*4":
													if($level==1){
														//9e. Ask how much and Launch the Mpesa Checkout to the user
													$response = "CON Proceed with payment.?\n";
													$response .= " 1. 5000 Shilling.\n";
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
                            // Find the user in the db
                            $sql7 = "SELECT * FROM microfinance WHERE phoneNumber LIKE '%".$phoneNumber."%' LIMIT 1";
                            $userQuery=$db->query($sql7);
                            $userAvailable=$userQuery->fetch_assoc();
                              // Find the account
                            $sql7a = "SELECT * FROM microfinance WHERE phoneNumber LIKE '%".$phoneNumber."%' LIMIT 1";
                            $BalQuery=$db->query($sql7a);
                            $newBal = 0.00; $newLoan = 0.00;
                            if($BalAvailable=$BalQuery->fetch_assoc()){
                            $newBal = $BalAvailable['parking'];
                            }
                            //Respond with user Balance
                            $response = "END Your account statement.\n";
                            $response = "END You are registered:\n";
                            $response .= "Garissa County.\n";
                            $response .= "Name: ".$userAvailable['name']."\n";
                            $response .= "Id No: ".$userAvailable['plate_no']."\n";
                            $response .= "Car Plate No: ".$userAvailable['city']."\n";
                            $response .= "Parking Status: ".$newBal."\n";

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
						 case "1":
							 //End session

										 $response = "END You are paying 17/. Please do send 17/ to 0742588200\n";
							 // Print the response onto the page so that our gateway can read it
							 header('Content-type: text/plain');
							 echo $response;
							 $amount=17;
						 //Create pending record in checkout to be cleared by cronjobs
								 $sql12a = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
								 $db->query($sql12a);
							 break;
				 }
						 break;
						 case 8:
							 //12. Pay loan
						 switch ($userResponse) {
								 case "1":
									 //End session

												 $response = "END You are paying 20/. Please do send 20/ to 0742588200\n";
									 // Print the response onto the page so that our gateway can read it
									 header('Content-type: text/plain');
									 echo $response;
									 $amount=20;
								 //Create pending record in checkout to be cleared by cronjobs
										 $sql12a = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
										 $db->query($sql12a);
									 break;
						 }
								 break;
								 case 9:
									 //12. Pay loan
								 switch ($userResponse) {
										 case "1":
											 //End session
                       case "4":
                         //End session

                                    $response = "CON Please enter your car plate number";
                                    $sql12b = "UPDATE microfinance SET `plate_no`='".$userResponse."' WHERE `phonenumber` LIKE '%". $phoneNumber ."%'";
                                              $db->query($sql12b);
                                              //11c. We graduate the user to the city level
                                    $sqlLvl12="UPDATE `session_levels` SET `level`=20 where `session_id`='".$sessionId."'";
                                    $db->query($sqlLvl12);
                         // Print the response onto the page so that our gateway can read it
                         header('Content-type: text/plain');
                         echo $response;

											 break;

                       case "2":
                         //End session
                         $response = "CON Fees.?\n";
                         $response .= " 1. Stones 4050/.\n";
                         $response .= " 2. Timber 2530/.\n";
                         // Print the response onto the page so that our gateway can read it
                          // Print the response onto the page so that our gateway can read it
                          header('Content-type: text/plain');

                          $sqlLvl17="UPDATE `session_levels` SET `level`=17 where `session_id`='".$sessionId."'";
                          $db->query($sqlLvl17);
                          echo $response;
                         break;

                         case "3":
                           //End session
                         $response = "END Services currently unavailable\n";
                           // Print the response onto the page so that our gateway can read it
                            // Print the response onto the page so that our gateway can read it
                            header('Content-type: text/plain');
                            echo $response;
                           break;
								 }
										 break;

                     case 12:
                       //12. Pay loan
                     switch ($userResponse) {
                         case "1":
                           //End session

                                 $response = "END Send collected ammount to 0742588200\n";
                           // Print the response onto the page so that our gateway can read it
                           header('Content-type: text/plain');
                           echo $response;

                              break;
                              case "2":
                                //End session
                                 $response = "END Send collected ammount to 0742588200\n";
                                // Print the response onto the page so that our gateway can read it
                                 // Print the response onto the page so that our gateway can read it
                                 header('Content-type: text/plain');
                                 echo $response;
                                break;

                                case "3":
                                  //End session
                                   $response = "CON Please enter your car plate number";

                                   $sqlLvl12="UPDATE `session_levels` SET `level`=22 where `session_id`='".$sessionId."'";
                                   $db->query($sqlLvl12);
                        // Print the response onto the page so that our gateway can read it
                        header('Content-type: text/plain');
                        echo $response;
                                  break;

                              }
                              break;
										 case 16:
											 //12. Pay loan
										 switch ($userResponse) {
												 case "1":
													 //End session

																 $response = "END You are paying 150/. Please do send 150/ to 0742588200\n";



													 // Print the response onto the page so that our gateway can read it
													 header('Content-type: text/plain');
													 echo $response;

                           $parking=150;
                         //Create pending record in checkout to be cleared by cronjobs


                         $sql11b = "UPDATE microfinance SET `parking`='".$parking."' WHERE `phonenumber` LIKE '%". $phoneNumber ."%'";
                                     $db->query($sql11b);
										break;

                             case "2":
                               //End session

                                     $response = "END You are paying 100/. Please do send 100/ to 0742588200\n";
                               // Print the response onto the page so that our gateway can read it
                               header('Content-type: text/plain');
                               echo $response;

                               $parking=100;
                             //Create pending record in checkout to be cleared by cronjobs


                             $sql11b = "UPDATE microfinance SET `parking`='".$parking."' WHERE `phonenumber` LIKE '%". $phoneNumber ."%'";
                                         $db->query($sql11b);

                     break;

                     case "3":
                       //End session

                             $response = "END You are paying 100/. Please do send 100/ to 0742588200\n";
                       // Print the response onto the page so that our gateway can read it
                       header('Content-type: text/plain');
                       echo $response;

                       $parking=100;
                     //Create pending record in checkout to be cleared by cronjobs


                     $sql11b = "UPDATE microfinance SET `parking`='".$parking."' WHERE `phonenumber` LIKE '%". $phoneNumber ."%'";
                                 $db->query($sql11b);
                break;

                case "4":
                  //End session

                             $response = "CON Please enter your car plate number";

                             $sqlLvl12="UPDATE `session_levels` SET `level`=21 where `session_id`='".$sessionId."'";
                             $db->query($sqlLvl12);
                  // Print the response onto the page so that our gateway can read it
                  header('Content-type: text/plain');
                  echo $response;


           break;
 }

 break;

 case 20;
 $response = "CON Proceed with parking payment.?\n";
                    $response .= " 1. Town 150/.\n";

                    $response .= " 2. Province 100/.\n";
                     //  $sqlLvl17="UPDATE `session_levels` SET `level`=17 where `session_id`='".$sessionId."'";
                     //  $db->query($sqlLvl17);
                      $response .= " 3. Iftin 100/.\n";
                     //  $sqlLvl18="UPDATE `session_levels` SET `level`=18 where `session_id`='".$sessionId."'";
                     //  $db->query($sqlLvl18);
                      header('Content-type: text/plain');
                      $sqlLvl16="UPDATE `session_levels` SET `level`=16 where `session_id`='".$sessionId."'";
                      $db->query($sqlLvl16);
                      echo $response;
break;

                   case 21;
                      $response = "END The car  has already paid\n";
                      header('Content-type: text/plain');
                      echo $response;
break;

case 22;
   $response = "END The car  has already paid\n";
   header('Content-type: text/plain');
   echo $response;
break;
                     case 17:
                       //12. Pay loan
                     switch ($userResponse) {
                         case "1":
                           //End session

                                 $response = "END You are paying 4050/. Please do send 4050/ to 0742588200\n";
                           // Print the response onto the page so that our gateway can read it
                           header('Content-type: text/plain');
                           echo $response;

                           $stones=4050;
                         //Create pending record in checkout to be cleared by cronjobs


                         $sql11b = "UPDATE microfinance SET `stone`='".$stones."' WHERE `phonenumber` LIKE '%". $phoneNumber ."%'";
                                     $db->query($sql11b);


                           break;

                           case "2":
                             //End session

                                   $response = "END You are paying 2530/. Please do send 2030/ to 0742588200\n";
                             // Print the response onto the page so that our gateway can read it
                             header('Content-type: text/plain');
                             echo $response;

                             $timber=2530;
                           //Create pending record in checkout to be cleared by cronjobs

                           $sql11b = "UPDATE microfinance SET `timber`='".$timber."' WHERE `phonenumber` LIKE '%". $phoneNumber ."%'";
                                       $db->query($sql11b);

                             break;
                     }
                     break;
                     case 18:
                       //12. Pay loan
                     switch ($userResponse) {
                         case "1":
                           //End session

                                 $response = "END You are paying 100/. Please do send 100/ to 0742588200\n";
                           // Print the response onto the page so that our gateway can read it
                           header('Content-type: text/plain');
                           echo $response;
                           $amount=100;
                         //Create pending record in checkout to be cleared by cronjobs
                             $sql18a = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
                             $db->query($sql18a);
                           break;
                     }
                     break;

                     case 19:
                       //12. Pay loan
                     switch ($userResponse) {
                         case "1":
                           //End session

                                 $response = "END You are paying 100/. Please do send 100/ to 0742588200\n";
                           // Print the response onto the page so that our gateway can read it
                           header('Content-type: text/plain');
                           echo $response;
                           $amount=100;
                         //Create pending record in checkout to be cleared by cronjobs
                             $sql19a = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
                             $db->query($sql19a);
                           break;
                     }

												 break;
												 case 11:
													 //12. Pay loan
												 switch ($userResponse) {
														 case "1":
															 //End session

																		 $response = "END You are paying 3000/. Please do send 3000/ to 0742588200\n";
															 // Print the response onto the page so that our gateway can read it
															 header('Content-type: text/plain');
															 echo $response;
															 $amount=5000;
														 //Create pending record in checkout to be cleared by cronjobs
																 $sql12a = "INSERT INTO checkout (`status`,`amount`,`phoneNumber`) VALUES('pending','".$amount."','".$phoneNumber."')";
																 $db->query($sql12a);
															 break;
												 }
														 break;
														 case 11:
															 //12. Pay loan
														 switch ($userResponse) {
																 case "1":
																	 //End session

																				 $response = "END You are paying 5000/. Please do send 5000/ to 0742588200\n";
																	 // Print the response onto the page so that our gateway can read it
																	 header('Content-type: text/plain');
																	 echo $response;
																	 $amount=5000;
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

             case 3:
               //10f. Request for city again --- level has not changed...
             $response = "CON Car plate No not supposed to be empty. Please reply with your Car plate No \n";
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
