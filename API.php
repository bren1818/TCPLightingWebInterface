<?php
/*
 *
 * PHP API CALLS
 *
 */	
 	include "include.php";
	
	$function = ""; //Toggle or Brightness
	$type = "";		//Device or Room
	$UID = "";		//DeviceID or Room ID
	
	
	/*
	echo "<p>Found ".sizeof($ROOMS)." rooms and ".sizeof($DEVICES)." devices.</p>";
	
	foreach($ROOMS as $room){
		echo "<h4>Room - ".$room["name"]."</h4>";
		pa($room);
	}
	
	foreach( $DEVICES as $device){
		
		echo "<h4>Device - ".(isset($device["name"]) ? $device["name"] : "unknown")."</h4>";
		pa( $device);
		
		/*
		
		Play with some Commands on the devices
		
		
		// Brighten or dim Device (set value 0 - 100) 
		//$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$device["did"]."</did><value>100</value><type>level</type></gip>"; 
		
		// Turn Bulb On or Off (set value 0 - 1)
		//$CMD = "cmd=DeviceSendCommand&data=<gip><version>1</version><token>".TOKEN."</token><did>".$device["did"]."</did><value>1</value></gip>"; 
		
		$result = getCurlReturn($CMD);

	}
	*/
	
?>	