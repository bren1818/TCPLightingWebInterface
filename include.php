<?php
/*
 *
 * PHP includes
 *
 */

define("LIGTHING_URL", "192.168.1.tcp"); 					    // IP address of TCP Bridge/Gateway
define("LIGHTING_PORT", "443");								      // 443 for new firmware, 80 for legacy
define("USER_EMAIL", "username@gmail.com"); 				// update this to your email - I think this is so you dont have to regenerate tokens if you run this script elsewhere
define("USER_PASSWORD", USER_EMAIL);						    // can be anything
define("USER_TOKEN", "");                           // paste your token here once you get it - leave empty for legacy 
define("LOCAL_URL", "http://localhost:82/"); 	      // Address of this webserver - this is used in runSchedule to call the API

# You should not need to edit below this line 
define("SAVE_SCHEDULE", 1); 								//saves schedule to a binary file on save sched.sched
define("LOG_ACTIONS", 1); 									//saves completed actions to schedule.actioned

define("API_PATH", "/gwr/gop.php");							//API Path on bridge - do not change
define("IMAGE_PATH", "http://".LIGTHING_URL."/gwr/"); 		//append urls to this eg: images/lighting/TCP/TCP-A19.png
define("SCHEME", (LIGHTING_PORT == 80) ? 'http' : 'https');
define("TOKEN", (SCHEME == 'http') ? "blahblah" : USER_TOKEN); 
define("TOKEN_STRING", "<gip><version>1</version><rc>200</rc><token>".TOKEN."</token></gip>"); //example of the token

date_default_timezone_set("America/New_York"); 				//Ensure this matches your timezone so if you use scheduler the hours match


/*Function to Print Array*/
function pa($array){
	echo '<pre>'.print_r($array,true).'</pre>';
}

function getCurlReturn($postDataString){
	$URL = SCHEME."://".LIGTHING_URL.":".LIGHTING_PORT.API_PATH;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataString);
	if (SCHEME == 'https') {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	}

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


function getDevices(){
	$CMD = "cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>RoomGetCarousel</gcmd><gdata><gip><version>1</version><token>".TOKEN."</token><fields>name,image,imageurl,control,power,product,class,realtype,status</fields></gip></gdata></gwrcmd></gwrcmds>&fmt=xml";
	$result = getCurlReturn($CMD);
	$array = xmlToArray($result);
	$DATA = $array["gwrcmd"]["gdata"]["gip"]["room"];
	$DEVICES = array();	
	foreach($DATA as $room){
		
		if( ! is_array($room["device"]) ){
			//$DEVICES[] = $room["device"]; //singular device in a room
		}else{
			$device = (array)$room["device"];
			if( isset($device["did"]) ){
				//item is singular device
				$DEVICES[] = $room["device"];
			}else{
				for( $x = 0; $x < sizeof($device); $x++ ){
					if( isset($device[$x]) && is_array($device[$x]) && ! empty($device[$x]) ){
						$DEVICES[] = $device[$x];
					}
				}
			}	
		}
	}
	
	return $DEVICES;
}

/* 
	Some Documentation links
	http://home.stockmopar.com/updated-connected-by-tcp-api/
	http://home.stockmopar.com/connected-by-tcp-unofficial-api/
	http://forum.micasaverde.com/index.php/topic,22555.0.html
	http://code.mios.com/trac/mios_tcplighting
*/

?>
