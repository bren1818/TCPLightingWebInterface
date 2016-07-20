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
				
					//$val = 0 - 100;
					$val = $val < 0 ? 0 : $val;
					$val = $val > 100 ? 100 : $val;
					
					$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$UID."</did><value>".$val."</value><type>level</type></gip>"; 
					$result = getCurlReturn($CMD);
					$array = xmlToArray($result);
					echo json_encode( array("dim" => $val, "device" => $UID, "return" => $array) );
				break;
				default:
				echo json_encode( array("error" => "unknown function, required: toggle | dim") );
			}
		}elseif($type == "room"){
			
			$CMD = "cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>RoomGetCarousel</gcmd><gdata><gip><version>1</version><token>".TOKEN."</token><fields>name,image,imageurl,control,power,product,class,realtype,status</fields></gip></gdata></gwrcmd></gwrcmds>&fmt=xml";
			$result = getCurlReturn($CMD);
			$array = xmlToArray($result);
			$DATA = $array["gwrcmd"]["gdata"]["gip"]["room"];
			$complete = 0;
			foreach($DATA as $room){
				if( $room["rid"] == $UID ){
					$complete = 1;
					$DEVICES = array();	
					if( ! is_array($room["device"]) ){
						$DEVICES[] = $room["device"]; //singular device in a room
					}else{
						$device = (array)$room["device"];
						for( $x = 0; $x < sizeof($device); $x++ ){
							if( isset($device[$x]) && is_array($device[$x]) && ! empty($device[$x]) ){
								$DEVICES[] = $device[$x];
							}
						}
					}
					
					if( sizeof($DEVICES) > 0 ){
						foreach($DEVICES as $device){
							if( $function == "toggle" ){
								$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$device['did']."</did><value>".$val."</value></gip>"; 
								$result = getCurlReturn($CMD);
							}elseif( $function == "dim"){
								$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$device['did']."</did><value>".$val."</value><type>level</type></gip>"; 
								$result = getCurlReturn($CMD);
							}
						}
					}else{
						echo json_encode( array("error" => "no devices in room") );
					}
				}
			}
			if($complete == 1){
				echo json_encode( array("room" => $UID, "fx" => $function, "val" => $val) );
			}else{
				echo json_encode( array("error" => "room not found") );
			}
		}else{
			echo json_encode( array("error" => "unknown type, required: device | room") );
		}
	}else{
		echo json_encode( array("error" => "argument empty or invalidm required: fx, type, UID, val") );
	}
?>	