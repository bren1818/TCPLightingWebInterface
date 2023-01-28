<?php
/*
 *
 * PHP API CALLS
 *
 */	

	include "include.php";
 
	global $REMOTE_IP;
	
	if( REQUIRE_EXTERNAL_API_PASSWORD && ! isLocalIPAddress($REMOTE_IP)){
		$password = 		isset($_REQUEST['password']) ? $_REQUEST['password'] : "";		//passed password
		if( $password != EXTERNAL_API_PASSWORD ){
			//invalid password
			echo "Invalid API Password";
			APILog("Attempted API Access, invalid or no password provided.");
			exit;
		}
	}
	
	if( RESTRICT_EXTERNAL_PORT == 1 && ! isLocalIPAddress($REMOTE_IP) ){
		if( $_SERVER['SERVER_PORT'] != EXTERNAL_PORT ){
			echo "Invalid Port";
			APILog("Attempted API Access on invalid port");			
			exit;
		}
	}
 
	$function = isset($_REQUEST['fx']) ? $_REQUEST['fx'] : ""; 		//Toggle or Brightness
	$type = 	isset($_REQUEST['type']) ? $_REQUEST['type'] : "";		//Device or Room
	$UID = 		isset($_REQUEST['uid']) ? $_REQUEST['uid'] : "";		//DeviceID or Room ID
	$val = 		isset($_REQUEST['val']) ? $_REQUEST['val'] : "";		//DeviceID or Room ID
	
	
	APILog('- Function: '.$function." Type: " . $type . " ID : " . $UID . " Value: " . $val);
	
	$val = $val < 0 ? 0 : $val;
	$val = $val > 100 ? 100 : $val;
	
	/*Function to facilitate device dimming on / off */
	function dimOnOff($deviceInfoArray, $onOff ){
		//store original value
		$olevel = $deviceInfoArray["level"];
		//check state?
		if( $onOff == 1){
			//fade on,
			$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$deviceInfoArray['did']."</did><value>0</value><type>level</type></gip>"; 
			$result = getCurlReturn($CMD);
			//set on
			$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$deviceInfoArray['did']."</did><value>1</value></gip>"; 
			$result = getCurlReturn($CMD);
			//dim to olevel
			$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$deviceInfoArray['did']."</did><value>".$olevel."</value><type>level</type></gip>"; 
			$result = getCurlReturn($CMD);
		}else{
			//fade off
			$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$deviceInfoArray['did']."</did><value>0</value><type>level</type></gip>";
			$result = getCurlReturn($CMD);
			//set off
			$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$deviceInfoArray['did']."</did><value>0</value></gip>"; 
			$result = getCurlReturn($CMD);
			//dim to olevel
			$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$deviceInfoArray['did']."</did><value>".$olevel."</value><type>level</type></gip>"; 
			$result = getCurlReturn($CMD);
			
		}
		return $result;
	}
	
	function deviceOn($UID){
		$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$UID."</did><value>1</value></gip>"; 
		$result = getCurlReturn($CMD);
		$array = xmlToArray($result);
	}
	
	if( $function != "" && $type != "" && $UID != "" && $val != ""){
		
		$DEVICES = getDevices();
		if( $type == "device"){
			
			$THE_DEVICE = null;
			if( sizeof($DEVICES) > 0 ){
				foreach($DEVICES as $device){
					if( $device["did"] == $UID ){
						$THE_DEVICE = $device;
						break;
					}
				}	
			}
			
			switch ($function){
				case "toggle": 
				
					//$val = 1 | 0 - on | off
					$val = ($val > 0) ? 1 : 0;
					
					
					if( ($val == 1 && FORCE_FADE_ON) || ($val == 0 && FORCE_FADE_OFF) ){
						//get current lighitng value
						
						//if( sizeof($DEVICES) > 0 ){
						//	foreach($DEVICES as $device){
						//		if( $device["did"] == $UID ){
									dimOnOff($THE_DEVICE, $val );
									echo json_encode( array("toggle" => $val, "device" => $UID, "return" => "DimFade") );
									//break;
								//}
								
							//}
						//}
						
					}else{
						$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$UID."</did><value>".$val."</value></gip>"; 
						$result = getCurlReturn($CMD);
						$array = xmlToArray($result);
						echo json_encode( array("toggle" => $val, "device" => $UID, "return" => $array) );
					}
					
					
				break;
				case "dim": 
					if( $THE_DEVICE['state'] == 0){
						//turn light on
						deviceOn( $UID );
					}
				
					$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$UID."</did><value>".$val."</value><type>level</type></gip>"; 
					$result = getCurlReturn($CMD);
					$array = xmlToArray($result);
					echo json_encode( array("dim" => $val, "device" => $UID, "return" => $array) );
				break;
				
				
				case "dimby":
					$darkenTo = $THE_DEVICE['level'] - $val;
					if( $darkenTo <= 0 ){ $darkenTo = 0; }
					$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$UID."</did><value>".$darkenTo."</value><type>level</type></gip>"; 
					$result = getCurlReturn($CMD);
					$array = xmlToArray($result);
					echo json_encode( array("dimby" => $val, "device" => $UID, "return" => $array) );
				
				
				break;
				case "brightenby":
					
					if( $THE_DEVICE['state'] == 0){
						//turn light on
						deviceOn( $UID );
					}
				
					$brightenTo = $THE_DEVICE['level'] + $val;
					if( $brightenTo > 100 ){ $brightenTo = 100; }
					$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$UID."</did><value>".$brightenTo."</value><type>level</type></gip>"; 
					$result = getCurlReturn($CMD);
					$array = xmlToArray($result);
					echo json_encode( array("brightenby" => $val, "device" => $UID, "return" => $array) );
					
				
				break;
				default:
				echo json_encode( array("error" => "unknown function, required: toggle | dim") );
			}
		}elseif($type == "room"){
			
			$THE_ROOM = null;
			
			//Get State of System Data
			$CMD = "cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>RoomGetCarousel</gcmd><gdata><gip><version>1</version><token>".TOKEN."</token><fields>name,image,imageurl,control,power,product,class,realtype,status</fields></gip></gdata></gwrcmd></gwrcmds>&fmt=xml";
			$result = getCurlReturn($CMD);
			$array = xmlToArray($result);
			$DATA = $array["gwrcmd"]["gdata"]["gip"]["room"];
			
				foreach($DATA as $room){
						if(  is_array($room["device"]) && $room["rid"] == $UID ){
							$device = (array)$room["device"];
							$THE_ROOM == $room;

							
							if( isset($device["did"]) ){
								//item is singular device
								//$room['device']["roomID"] = $room["rid"];
								//$room['device']["roomName"] = $room["name"];
								$THE_ROOM['brightness'] = $room['device']['level'];
								//$DEVICES[] = $room["device"];
							}else{
								for( $x = 0; $x < sizeof($device); $x++ ){
									if( isset($device[$x]) && is_array($device[$x]) && ! empty($device[$x]) ){
										//$device[$x]["roomID"] = $room["rid"];
										//$device[$x]["roomName"] = $room["name"];
										//$DEVICES[] = $device[$x];
										$THE_ROOM['brightness'] += $device[$x]['level'];


									}
								}
								$THE_ROOM['brightness'] = $THE_ROOM['brightness'] / sizeof($device);

							}
						break;	
						}
					}	
		
			

			if( $function == "toggle" ){
				//turn on | off
				$tval = ($val > 0) ? 1 : 0;
				if( ($tval == 1 && FORCE_FADE_ON) || ($tval == 0 && FORCE_FADE_OFF) ){
					
					$DEVICES = array();
					foreach($DATA as $room){
						if(  is_array($room["device"]) && $room["rid"] == $UID ){
							$device = (array)$room["device"];
							
							if( isset($device["did"]) ){
								//item is singular device
								$room['device']["roomID"] = $room["rid"];
								$room['device']["roomName"] = $room["name"];
								$DEVICES[] = $room["device"];
							}else{
								for( $x = 0; $x < sizeof($device); $x++ ){
									if( isset($device[$x]) && is_array($device[$x]) && ! empty($device[$x]) ){
										$device[$x]["roomID"] = $room["rid"];
										$device[$x]["roomName"] = $room["name"];
										$DEVICES[] = $device[$x];
									}
								}
							}
							
						}
					}
					
					//get current room lighitng value??
					if( sizeof($DEVICES) > 0 ){
						$dcount = 0;
						$roomBrightness = 0;
						foreach($DEVICES as $device){
							if( $device["roomID"] == $UID ){
								$roomBrightness += $device["level"];
								$dcount++;
							}
						}
						if( $dcount > 0){
							$roomBrightness = ($roomBrightness/$dcount);
						}else{
							$roomBrightness = 100;
						}
					}else{
						$roomBrightness = 100;
					}
						
					$result = "";
					
					if( $tval == 1){
						//fade on --ensure off?
						$CMD = "cmd=RoomSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><rid>".$UID."</rid><value>0</value><type>level</type></gip>";
						$result = getCurlReturn($CMD);
						//turn on
						$CMD = "cmd=RoomSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><rid>".$UID."</rid><value>1</value></gip>";
						$result.= getCurlReturn($CMD);
						//fade to 100
						$CMD = "cmd=RoomSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><rid>".$UID."</rid><value>".$roomBrightness."</value><type>level</type></gip>";
						$result.= getCurlReturn($CMD);
						
					}else{
						//fade off -- ensure already off
						$CMD = "cmd=RoomSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><rid>".$UID."</rid><value>0</value><type>level</type></gip>";
						$result = getCurlReturn($CMD);
						//turn off
						$CMD = "cmd=RoomSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><rid>".$UID."</rid><value>0</value></gip>";
						$result.= getCurlReturn($CMD);
						//reset brightness to 100
						$CMD = "cmd=RoomSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><rid>".$UID."</rid><value>".$roomBrightness."</value><type>level</type></gip>";
						$result.= getCurlReturn($CMD);
					}
					
					
					$val = $roomBrightness;
					$array = $result;
					
				}else{
					$CMD = "cmd=RoomSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><rid>".$UID."</rid><value>".$val."</value></gip>";					
					$result = getCurlReturn($CMD);
					$array = xmlToArray($result);
				}
			
				
				
			}else if( $function == "dim"){
				
				//turn room on if by chance it is off
				$CMD = "cmd=RoomSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><rid>".$UID."</rid><value>1</value></gip>";					
				$result = getCurlReturn($CMD);
				$array = xmlToArray($result);
				
				
				// dim
				$CMD = "cmd=RoomSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><rid>".$UID."</rid><value>".$val."</value><type>level</type></gip>";
				
				$result = getCurlReturn($CMD);
				$array = xmlToArray($result);
			
			}elseif( $function == "dimby" ){
				
				$roomBrightness = $THE_ROOM['brightness'];
				$roomBrightness -= $val;
				if( $roomBrightness < 0 ){ $roomBrightness = 0; }

				
				$CMD = "cmd=RoomSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><rid>".$UID."</rid><value>".$roomBrightness."</value><type>level</type></gip>";
				
				$result = getCurlReturn($CMD);
				$array = xmlToArray($result);
				
										
			}elseif( $function == "brightenby" ){
				
				
				
						
				$roomBrightness = $THE_ROOM['brightness'];			
				$roomBrightness += $val;

				if( $roomBrightness > 100 ){ $roomBrightness = 100; }


				
				$CMD = "cmd=RoomSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><rid>".$UID."</rid><value>".$roomBrightness."</value><type>level</type></gip>";
				
				$result = getCurlReturn($CMD);
				$array = xmlToArray($result);		
				
				
			}
			
			
			echo json_encode( array("room" => $UID, "fx" => $function, "val" => $val,  "return" => $array) );
				
		}elseif($type == "all"){	


			if( sizeof($DEVICES) > 0 ){
				foreach($DEVICES as $device){
					
					if( $function == "toggle" ){
						//only toggle if it needs to be toggled
						if( isset($device['state']) && $device['state']  != $val ){
							
							$tval = ($val > 0) ? 1 : 0;
							if( ($tval == 1 && FORCE_FADE_ON) || ($tval == 0 && FORCE_FADE_OFF) ){
								$result = dimOnOff( $device, $tval );
									
							}else{
								
								$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$device['did']."</did><value>".$val."</value></gip>"; 
								$result = getCurlReturn($CMD);
							}
							
							
							
						}
						
					}elseif( $function == "dim"){

						$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$device['did']."</did><value>".$val."</value><type>level</type></gip>"; 
						$result = getCurlReturn($CMD);
						
						//turn light on if it is not in order to dim it
						if( isset($device['state']) && $device['state']  == 0 ){
							$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$device['did']."</did><value>1</value></gip>"; 
							$result = getCurlReturn($CMD);
						}
						
					
					}elseif ( $function == "dimby" ){
						
					
						
						$dBrightness = isset($device['level']) ? $device['level'] : 100;
						$dBrightness -= $val;
						if( $dBrightness < 0 ){ $dBrightness = 0; }
						
						$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$device['did']."</did><value>".$dBrightness."</value><type>level</type></gip>"; 
						
						$result = getCurlReturn($CMD);
						$array = xmlToArray($result);		
						
						//turn light on if it is not in order to dim it
						if( isset($device['state']) && $device['state']  == 0 ){
							$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$device['did']."</did><value>1</value></gip>"; 
							$result = getCurlReturn($CMD);
						}
						
						
					}elseif( $function == "brightenby" ){
						
						//turn light on if it is not in order to dim it
						if( isset($device['state']) && $device['state']  == 0 ){
							$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$device['did']."</did><value>1</value></gip>"; 
							$result = getCurlReturn($CMD);
						}
						
						
						$dBrightness = isset($device['level']) ? $device['level'] : 0;
						$dBrightness += $val;
						if( $dBrightness > 100 ){ $dBrightness = 100; }
						
						$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$device['did']."</did><value>".$dBrightness."</value><type>level</type></gip>"; 
						
						$result = getCurlReturn($CMD);
						$array = xmlToArray($result);		
						
						
					}
					
					
				}
				
				echo json_encode( array("success" => 1, "devices" => sizeof($DEVICES), "fx" => $function, "val" => $val) );
				
			}else{
				echo json_encode( array("error" => "no devices in home") );
			}
			
			
		}else{
			echo json_encode( array("error" => "unknown type, required: device | room") );
		}
	}else{
		
		if( $function == "scene" && $type != "" && $UID != "" ){
			//Run scene
			$CMD = "";
			if($type == "run"){
				$CMD = "cmd=SceneRun&data=<gip><version>1</version><token>".TOKEN."</token><sid>".$UID."</sid></gip>";
			}elseif( $type == "0" ){
				$CMD = "cmd=SceneRun&data=<gip><version>1</version><token>".TOKEN."</token><sid>".$UID."</sid><val>0</val></gip>";
			}elseif( $type == "1"){
				$CMD = "cmd=SceneRun&data=<gip><version>1</version><token>".TOKEN."</token><sid>".$UID."</sid><val>1</val></gip>";
			}elseif( $type == "delete" ){
				//$CMD = "cmd=SceneDelete&data=<gip><version>1</version><token>".TOKEN."</token><sid>".$UID."</sid></gip>"; 
			}
			
			if( $CMD != "" ){
				$result = getCurlReturn($CMD);
				$array = xmlToArray($result);
				echo json_encode( array("success" => 1, "scene" => $UID, "fx" => $function, "resp" => $array) );
			}else{
				echo json_encode( array("error" => "No Scene mode specified") );
			}
			exit;
		}
		
		$sceneDevices = array("rooms"=> array(), "devices" => array(), "count" => 0);
		$sceneList = array();
		
		
		if( $function == "getSceneState" || $function == "getState"){
			if( $UID != "" || $function == "getState" ){
				
				$CMD = "cmd=SceneGetListDetails&data=<gip><version>1</version><token>".TOKEN."</token><bigicon>1</bigicon></gip>";
				$result = getCurlReturn($CMD);
				$array = xmlToArray($result);
				$scenes = $array["scene"];
				if( is_array($scenes) ){
					$sceneItemCount = 0;
					for($x = 0; $x < sizeof($scenes); $x++){
						$sceneList[] = array("id" => $scenes[$x]["sid"], "name" => $scenes[$x]["name"], "icon" => $scenes[$x]["icon"], "active" =>  $scenes[$x]["active"] );
						if( $scenes[$x]["sid"] == $UID ){
							
							//echo '<pre>'.print_r( $scenes[$x], true ).'</pre>';
							
							if( isset( $scenes[$x]["device"]["id"] ) ){
								
								$sceneItemCount = $sceneItemCount + 1;
								//one item in scene
								
								$item = $scenes[$x]["device"];
								if( $item["type"] == "D" ){
									$sceneDevices["devices"][] = $item["id"];
								}elseif( $item["type"] == "R" ){
									$sceneDevices["rooms"][] = $item["id"];
								}
								
								
							}elseif( is_array(  $scenes[$x]["device"] ) ){
								foreach( $scenes[$x]["device"] as $d ){
									if( isset( $d["id"] ) ){
										
										$sceneItemCount = $sceneItemCount + 1;
										if( $d["type"] == "D" ){
											$sceneDevices["devices"][] = $d["id"];
										}elseif( $d["type"] == "R" ){
											$sceneDevices["rooms"][] = $d["id"];
										}
										
									}
								}
							}
							
							$sceneDevices["count"] = $sceneItemCount;
							//echo '<pre>'.print_r( $sceneDevices, true ).'</pre>';
							//exit;
						}
					}
					
					
				}	
				
				
			}else{
				echo json_encode( array("error" => "No Scene ID specified") );
			}
		}
		
		
		if( $function == "getState" || $function == "getDeviceState" || $function == "getRoomState" || $function == "getSceneState" ){
			
			$sceneDeviceObjectsON = 0;
			
			$CMD = "cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>RoomGetCarousel</gcmd><gdata><gip><version>1</version><token>".TOKEN."</token><fields>name,image,imageurl,control,power,product,class,realtype,status</fields></gip></gdata></gwrcmd></gwrcmds>&fmt=xml";
			$result = getCurlReturn($CMD);
			$array = xmlToArray($result);
			if( !isset($array["gwrcmd"]) ){
				exit;
			}
			
			$DEVICES = array();
			if( isset( $array["gwrcmd"]["gdata"]["gip"]["room"] ) ){
				$DATA = $array["gwrcmd"]["gdata"]["gip"]["room"];
			}else{
				exit;
			}
			$ROOMS  = array();
			$BRIDGE = array();
			if( sizeof($DATA) > 0 ){
				if ( isset( $DATA["rid"] ) ){ $DATA = array( $DATA ); }
				
				
				foreach($DATA as $room){
					$thisRoom = array();
					
					
					if( isset($room['rid'] ) ){
						$thisRoom["room_id"] = $room['rid'];
						$thisRoom["name"] = $room['name'];
						$thisRoom["color"] = $room['color'];
						$thisRoom["colorid"] = $room['colorid'];
						$thisRoom["brightness"] = 0;
						//$thisRoom["data"] = $room;
						$thisRoom["state"] = 0;
						
						
						
						
						if( ! is_array($room["device"]) ){
						
						}else{
							
							$device = (array)$room["device"];
							if( isset($device["did"]) ){
								$rd = array();
								$rd["id"] = $device["did"];
								$rd["name"] = $device["name"];
								$rd["level"] = ($device["level"] != null ? (int)$device["level"] : 0);
								$rd["state"] = $device["state"];
								$rd["online"] = (isset($device['offline']) && $device['offline'] == 1) ? 1 : 0;
								if( isset($device["other"]) && isset( $device["other"]["rcgroup"] ) && $device["other"]["rcgroup"] != null ){
									$rd["buttonNum"] = $device["other"]["rcgroup"];
								}
								$thisRoom["brightness"] += $rd["level"];
								$thisRoom["devices"][] = $rd;
								
								if( $device["state"] > 0 ){
									$thisRoom["state"] = (int)$thisRoom["state"] + 1;
								}
								
								//
								
								if( $function == "getSceneState" && in_array( $rd["id"], $sceneDevices["devices"] ) && $rd["state"] > 0 ){
									$sceneDeviceObjectsON++;
								}
								
									
								if( $function == "getDeviceState" && $UID ==  $device["did"] ){
									ob_clean();
									echo trim($device["state"]);
									exit;
								}	
									
							}else{
								for( $x = 0; $x < sizeof($device); $x++ ){
									if( isset($device[$x]) && is_array($device[$x]) && ! empty($device[$x]) ){
										$rd = array();
										
										$rd["id"] = $device[$x]["did"];
										$rd["name"] = $device[$x]["name"];
										$rd["level"] = ( $device[$x]["level"] != null ? (int)$device[$x]["level"] : 0);
										$rd["state"] = $device[$x]["state"];
										$rd["online"] = (isset($device[$x]['offline']) && $device[$x]['offline'] == 1) ? 1 : 0;
										if( isset($device[$x]["other"]) && isset( $device[$x]["other"]["rcgroup"] ) && $device[$x]["other"]["rcgroup"] != null ){
											$rd["buttonNum"] = $device[$x]["other"]["rcgroup"];
										}
								
										$thisRoom["brightness"]+= $rd["level"];
										$thisRoom["devices"][] = $rd;
										
										if( $device[$x]["state"] > 0 ){
											$thisRoom["state"] = (int)$thisRoom["state"] + 1;
										}
										
										//
										
										if( $function == "getSceneState" && in_array( $rd["id"], $sceneDevices["devices"] ) && $rd["state"] > 0 ){
											$sceneDeviceObjectsON++;
										}
										
								
										if( $function == "getDeviceState" && $UID == $device[$x]["did"] ){
											ob_clean();
											echo trim($device[$x]["state"]);
											exit;
										}	
										
									}
								}
							}
						}
					
						
						if( $function == "getRoomState" && $UID ==  $room['rid'] ){
							ob_clean();
							echo ( $thisRoom["state"] > 0 ) ? 1 : 0;
							exit;
						}
					
						if( $function == "getSceneState" && in_array( $room['rid'], $sceneDevices["rooms"] ) && $thisRoom["state"] > 0 ){
							$sceneDeviceObjectsON++;
						}
						
						
						$thisRoom["devicesCount"] = sizeof( $thisRoom["devices"] );
						$thisRoom["brightness"] = (int)($thisRoom["brightness"] / $thisRoom["devicesCount"]);
						$thisRoom["state"] = ( $thisRoom["state"] > 0 ) ? ( $thisRoom["state"] / sizeof( $thisRoom["devices"] ) ) : 0; 
						
						$ROOMS[] = $thisRoom;
					}
				}
				
			}
			
			$BRIDGE["rooms"] = $ROOMS;
			$BRIDGE["roomCount"] = sizeof($ROOMS);
			$BRIDGE["scenes"] = $sceneList;
			$BRIDGE["sceneCount"] = sizeof( $sceneList );
			
			
			if( $function == "getState"  ){
				header('Content-Type: application/json');
				echo json_encode( $BRIDGE );
				exit;
			}elseif( $function == "getSceneState" ){
				
				if( $sceneDeviceObjectsON == 0){
					echo 0;
				}elseif( $sceneDeviceObjectsON == $sceneDevices["count"] ){
					echo 1;
				}else{
					echo $sceneDeviceObjectsON / $sceneDevices["count"];
				}
				exit;
			}else{
				echo '-1';
				exit;
			}
		}
		
		echo json_encode( array("error" => "argument empty or invalid. Required: fx, type, UID, val", "recieved" => $_REQUEST) );
		
	}
?>	