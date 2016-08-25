<?php
/*
 *
 * PHP API CALLS
 *
 */	
	$function = isset($_REQUEST['fx']) ? $_REQUEST['fx'] : ""; 		//Toggle or Brightness
	$type = 	isset($_REQUEST['type']) ? $_REQUEST['type'] : "";		//Device or Room
	$UID = 		isset($_REQUEST['uid']) ? $_REQUEST['uid'] : "";		//DeviceID or Room ID
	$val = 		isset($_REQUEST['val']) ? $_REQUEST['val'] : "";		//DeviceID or Room ID
	
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
	
	
	
	if( $function != "" && $type != "" && $UID != "" && $val != ""){
		include "include.php";
		$DEVICES = getDevices();
		if( $type == "device"){
			switch ($function){
				case "toggle": //SAMPLE CALL: /api.php?fx=dim&type=device&uid=360187068559174100&val=80
				
					//$val = 1 | 0 - on | off
					$val = ($val > 0) ? 1 : 0;
					
					
					if( ($val == 1 && FORCE_FADE_ON) || ($val == 0 && FORCE_FADE_OFF) ){
						//get current lighitng value
						
						if( sizeof($DEVICES) > 0 ){
							foreach($DEVICES as $device){
								if( $device["did"] == $UID ){
									dimOnOff( $device, $val );
									echo json_encode( array("toggle" => $val, "device" => $UID, "return" => "DimFade") );
									break;
								}
								
							}
						}
						
					}else{
						$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$UID."</did><value>".$val."</value></gip>"; 
						$result = getCurlReturn($CMD);
						$array = xmlToArray($result);
						echo json_encode( array("toggle" => $val, "device" => $UID, "return" => $array) );
					}
					
					
				break;
				case "dim": //SAMPLE CALL: /api.php?fx=dim&type=device&uid=360187068559174100&val=80
				
					$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$UID."</did><value>".$val."</value><type>level</type></gip>"; 
					$result = getCurlReturn($CMD);
					$array = xmlToArray($result);
					echo json_encode( array("dim" => $val, "device" => $UID, "return" => $array) );
				break;
				default:
				echo json_encode( array("error" => "unknown function, required: toggle | dim") );
			}
		}elseif($type == "room"){
			
			if( $function == "toggle" ){
				//turn on | off
				$tval = ($val > 0) ? 1 : 0;
				if( ($tval == 1 && FORCE_FADE_ON) || ($tval == 0 && FORCE_FADE_OFF) ){
					
					//Get State of System Data
					$CMD = "cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>RoomGetCarousel</gcmd><gdata><gip><version>1</version><token>".TOKEN."</token><fields>name,image,imageurl,control,power,product,class,realtype,status</fields></gip></gdata></gwrcmd></gwrcmds>&fmt=xml";
					$result = getCurlReturn($CMD);
					$array = xmlToArray($result);
					$DATA = $array["gwrcmd"]["gdata"]["gip"]["room"];
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
			
				
				
			}else{
				// dim
				$CMD = "cmd=RoomSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><rid>".$UID."</rid><value>".$val."</value><type>level</type></gip>";
				
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
		echo json_encode( array("error" => "argument empty or invalid. Required: fx, type, UID, val", "recieved" => $_REQUEST) );
	}
?>	