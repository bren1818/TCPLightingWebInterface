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
	
	if( $function != "" && $type != "" && $UID != "" && $val != ""){
		include "include.php";
		
		if( $type == "device"){
			switch ($function){
				case "toggle": //SAMPLE CALL: /api.php?fx=dim&type=device&uid=360187068559174100&val=80
				
					//$val = 1 | 0 - on | off
					$val = ($val > 0) ? 1 : 0;
					
					$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$UID."</did><value>".$val."</value></gip>"; 
					$result = getCurlReturn($CMD);
					$array = xmlToArray($result);
					echo json_encode( array("toggle" => $val, "device" => $UID, "return" => $array) );
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
				$CMD = "cmd=RoomSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><rid>".$UID."</rid><value>".$val."</value></gip>";
			}else{
				$CMD = "cmd=RoomSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><rid>".$UID."</rid><value>".$val."</value><type>level</type></gip>";
			}
			$result = getCurlReturn($CMD);
			$array = xmlToArray($result);
			echo json_encode( array("room" => $UID, "fx" => $function, "val" => $val,  "return" => $array) );
				
		}elseif($type == "all"){	

			$DEVICES = getDevices();

			if( sizeof($DEVICES) > 0 ){
				foreach($DEVICES as $device){
					if( $function == "toggle" ){
						//only toggle if it needs to be toggled
						if( isset($device['state']) && $device['state']  != $val ){
							$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$device['did']."</did><value>".$val."</value></gip>"; 
							$result = getCurlReturn($CMD);
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