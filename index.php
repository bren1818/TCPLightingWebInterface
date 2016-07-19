<?php
/*
 *
 * TCP Ligthing Web UI Test Script - By Brendon Irwin
 * 
 */

define("LIGTHING_URL", "192.168.1.114"); //IP address of gateway
define("LIGHTING_PORT", "443");
define("API_PATH", "/gwr/gop.php");
define("USER_EMAIL", "bren1818@gmail.com"); //update this to yours
define("USER_PASSWORD", USER_EMAIL);

define("TOKEN", ""); //paste your token here once you get it 
define("TOKEN_STRING", "<gip><version>1</version><rc>200</rc><token>".TOKEN."</token></gip>");

/*Function to Print Array*/
function pa($array){
	echo '<pre>'.print_r($array,true).'</pre>';
}

function getCurlReturn($postDataString){
	
	$URL = "https://".LIGTHING_URL.":".LIGHTING_PORT.API_PATH;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataString);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$result = curl_exec($ch);
	
	curl_close($ch);
	
	return $result;
}

function xmlToArray($string){
	$xml = simplexml_load_string($string);
	$json = json_encode($xml);
	$array = json_decode($json,TRUE);
	
	return $array;
}


if( TOKEN != "" ){
	
	$URL = "https://".LIGTHING_URL.":".LIGHTING_PORT.API_PATH;
	
	//Get State of System Data
	$CMD = "cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>RoomGetCarousel</gcmd><gdata><gip><version>1</version><token>".TOKEN."</token><fields>name,image,imageurl,control,power,product,class,realtype,status</fields></gip></gdata></gwrcmd></gwrcmds>&fmt=xml";
	
	$result = getCurlReturn($CMD);
	
	
	$array = xmlToArray($result);
	


	$DATA = $array["gwrcmd"]["gdata"]["gip"]["room"];
	//Pages of interest which document some of the API Calls
	//http://home.stockmopar.com/updated-connected-by-tcp-api/
	//http://home.stockmopar.com/connected-by-tcp-unofficial-api/
	
	
	$ROOMS = array();
	$DEVICES = array();
	
	foreach($DATA as $room){
		$deviceCount = 0;
		if( ! is_array($room["device"]) ){
			$room["device"]["roomID"] = $room["rid"];
			$room["device"]["roomName"] = $room["name"];
			$DEVICES[] = $room["device"]; //singular device in a room
			$deviceCount++;
		}else{
			$device = (array)$room["device"];
			for( $x = 0; $x < sizeof($device); $x++ ){
				if( isset($device[$x]) && is_array($device[$x]) && ! empty($device[$x]) ){
					$device[$x]["roomID"] = $room["rid"];
					$device[$x]["roomName"] = $room["name"];
					$DEVICES[] = $device[$x];
					$deviceCount++;
				}
			}
		}
		
		$room["deviceCount"] = $deviceCount;
		unset($room["device"]);
		$ROOMS[] = $room;
	}
	
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
		
	
		*/
	
		
	}
	
	// echo "<h2>Command Result:</h2>";
	// echo "<pre>".print_r($array,true)."</pre>";
	
	
	echo '<h3>UserGetListDefaultRooms</h3>';
	
	$CMD = "cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>UserGetListDefaultRooms</gcmd><gdata><gip><version>1</version><token>".TOKEN."</token><fields>name,image,imageurl,control,power,product,class,realtype,status</fields></gip></gdata></gwrcmd></gwrcmds>&fmt=xml";
	
	$result = getCurlReturn($CMD);
	$array = xmlToArray($result);
	
	pa( $array );
	
	
	echo '<h3>UserGetListDefaultColors</h3>';
	
	$CMD = "cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>UserGetListDefaultColors</gcmd><gdata><gip><version>1</version><token>".TOKEN."</token><fields>name,image,imageurl,control,power,product,class,realtype,status</fields></gip></gdata></gwrcmd></gwrcmds>&fmt=xml";
	
	$result = getCurlReturn($CMD);
	$array = xmlToArray($result);
	
	pa( $array );
	
	
	
	
}else{
	echo "<h1>If you haven't press the sync button on the modem and re-run this script</h1>";

	
	$CMD = "cmd=GWRLogin&data=<gip><version>1</version><email>".USER_EMAIL."</email><password>".USER_PASSWORD."</password></gip>&fmt=xml";
		
	$result = getCurlReturn($CMD);
	
	//echo "URL: ".$URL.$REQUEST_STRING."<br />";
	echo "Result Token: | ".htmlentities($result)." | - note this has been turned to html entities for legibility. If you do not see a long string within <token></token> you need to ensure you have hit the sync button";
	
} 


?>