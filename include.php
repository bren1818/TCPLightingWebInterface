<?php
/*
 *
 * PHP includes
 *
 */

define("LIGTHING_URL", 	"192.168.1.TCP"); 			// IP address of TCP Bridge/Gateway
define("LIGHTING_PORT", "443");						// 443 for new firmware, 80 for legacy - If you don't know, leave it at 443

define("LOCAL_URL", 	"http://localhost");		// Address of your webserver running this - this is used in runSchedule to call the API
define("SCHEME", 		(LIGHTING_PORT == 80) ? 'http' : 'https'); //Don't modify


define("USER_EMAIL", "username@gmail.com"); 		// I think this is so you dont have to regenerate tokens if you run this script elsewhere
define("USER_PASSWORD", USER_EMAIL);				// can be anything
define("USE_TOKEN_FILE", 1); 						// store the token in a file vs hard coding it below otherwise fill in line 29

if( SCHEME == 'http'){
	define("TOKEN","NotRequired"); 
}else{
	if(USE_TOKEN_FILE){
		//load from file
		if( file_exists("tcp.token") ){
			$token = file_get_contents("tcp.token");
		}else{ $token = ""; }
		define("TOKEN", $token);
	}else{
		define("TOKEN", "PASTE_TOKEN_HERE_IF_NOT_USING_TOKEN_FILE");	// paste your token here once you get it - leave empty for 	legacy 
	}
}

define("SAVE_SCHEDULE", 	1); 						//saves schedule to a binary file on save sched.sched
define("LOG_ACTIONS", 		1); 						//saves completed actions to schedule.actioned
define("FORCE_FADE_ON", 	1);							//makes it so when lights are turned off they fade to 0 (Like Philips Bulbs)
define("FORCE_FADE_OFF", 	1);							//makes it so when lights are turned on they fade to their assigned value from 0 (Like Philips Bulbs)


define("API_PATH", "/gwr/gop.php");							//API Path on bridge - do not change
define("IMAGE_PATH", "https://".LIGTHING_URL."/gwr/"); 		//append urls to this eg: images/lighting/TCP/TCP-A19.png


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
	
	if ( isset( $DATA["rid"] ) ){ $DATA = array( $DATA ); }
	
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

function pageHeader($title){
	?>
	<!DOCTYPE html>
<html>
<head>
	<title>TCP Control Script</title>
	<link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" href="favicons/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="favicons/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="favicons/manifest.json">
	<link rel="mask-icon" href="favicons/safari-pinned-tab.svg" color="#5bbad5">
	<link rel="shortcut icon" href="favicons/favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="apple-mobile-web-app-title" content="TCP Lighting">
	<meta name="application-name" content="TCP Lighting">
	<meta name="msapplication-config" content="favicons/browserconfig.xml">
	<meta name="theme-color" content="#ffffff">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" href="style.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script src="js/jquery.ui.touch-punch.min.js"></script>
	<script src="scripts.js"></script>
	<title><?php echo $title; ?></title>
</head>
<body>
	<?php
}

function pageFooter(){
	?>
</body>
</html>	
	<?php
}

/* 
	Some Documentation, links, Repos of interest
	
	http://home.stockmopar.com/updated-connected-by-tcp-api/
	http://home.stockmopar.com/connected-by-tcp-unofficial-api/
	http://forum.micasaverde.com/index.php/topic,22555.0.html
	http://code.mios.com/trac/mios_tcplighting
	https://community.smartthings.com/t/any-interest-in-tcp-connected-hub-local-integration/51926/9
	https://github.com/hypergolic/greeenwave_firmware
	https://github.com/twack/TCP-Connect
	
*/

?>
