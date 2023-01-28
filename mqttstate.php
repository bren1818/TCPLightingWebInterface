<?php
include "include.php";
require("phpMQTT/phpMQTT.php");


/*
 *
 * TCP Ligthing Web UI 
 * 
 */


 if( TOKEN != "" ){
	if($ENABLE_MQTT == 0){
		echo "MQTT is not Enabled";
	} else {

	$mqtt = new phpMQTT($MQTTserver, $MQTTport, $MQTTpub_id);
	
	//Get State of System Data
	$CMD = "cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>RoomGetCarousel</gcmd><gdata><gip><version>1</version><token>".TOKEN."</token><fields>name,image,imageurl,control,power,product,class,realtype,status</fields></gip></gdata></gwrcmd></gwrcmds>&fmt=xml";
	$result = getCurlReturn($CMD);
	
	$array = xmlToArray($result);
	
	//check if token is expired 
	if( !isset($array["gwrcmd"]) ){
		echo '<p>GWR Command not returned, this likely indicates your token is expired, or invalid.<p>';
		echo '<p>Remove token and try regenerating a new one.</p>';
		
		//unlink old token file
		if( file_exists("tcp.token") ){
			if( unlink("tcp.token") ){
				echo "<p>Successfully deleted expired token file</p>";
			}
		}
		
		if(USE_TOKEN_FILE){
			echo '<p>If you are continuously seeing this message, ensure the folder is writeable or that tcp.token is writeable</p>';
		}
		
		pageFooter();
		exit;
	}
	
	if( isset( $array["gwrcmd"]["gdata"]["gip"]["room"] ) ){
		$DATA = $array["gwrcmd"]["gdata"]["gip"]["room"];
	}else{
			echo "No Room Data";
			pa( $array );
			$DATA =  array();
			pageFooter();
			exit;
	}
	
	$deviceCount = 0;
	
	if( sizeof($DATA) > 0 ){
		if ( isset( $DATA["rid"] ) ){ $DATA = array( $DATA ); }
		
		foreach($DATA as $room){
			$RoomName = str_replace(' ', '', $room['name']);
			if( isset($room['rid'] ) ){
				$DEVICES = array();
			
				if( ! is_array($room["device"]) ){
					
				}else{
					$device = (array)$room["device"];
					if( isset($device["did"]) ){
						//item is singular device
						$DEVICES[] = $room["device"];
						$deviceCount++;
					}else{
					
						for( $x = 0; $x < sizeof($device); $x++ ){
							if( isset($device[$x]) && is_array($device[$x]) && ! empty($device[$x]) ){
								$DEVICES[] = $device[$x];
								$deviceCount++;
							}
						}
					}
				}
				if( sizeof($DEVICES) > 0 ){
						$unplugged = 0;
						$roomBrightness = 0;
						$roomDevices = 0;
						if ($mqtt->connect(true, NULL, $MQTTusername, $MQTTpassword)) {
							foreach($DEVICES as $device){
								$DeviceName = str_replace(' ', '', $device['name']);
								if( isset($device['offline']) && $device['offline'] == 1){ $LWT = "offline"; } else {$LWT = "online";}
								$mqtt->publish($MQTT_prefix.'/'.$RoomName.'/'.$DeviceName.'/'.$device['did'].'/status', $device['state']);
								$mqtt->publish($MQTT_prefix.'/'.$RoomName.'/'.$DeviceName.'/'.$device['did'].'/brightness', $device['level']);
								$mqtt->publish($MQTT_prefix.'/'.$RoomName.'/'.$DeviceName.'/'.$device['did'].'/LWT', $LWT);
								sleep(1);
								echo $DeviceName.': On-Off State: '.$device["state"].'  Brightness:'.$device["level"].'  Online Status '.$LWT.'<br/>';
							}
							$mqtt->close();
						} else {
							echo "Time out!\n";
						}
				}else{
					echo 'No devices?';
					pa( $room );
				}
			}
		}
	}
} 
}
?>

