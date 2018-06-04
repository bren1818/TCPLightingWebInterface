<?php
/*TCP Lighting Configuration Options*/

define("LIGTHING_BRIDGE_IP", 				"192.168.1.TCP"); 			// IP address of TCP Bridge/Gateway
define("LIGHTING_BRIDGE_PORT", 				"443");						// 443 for new firmware, 80 for legacy - If you don't know, leave it at 443
define("LOCAL_URL", 						"http://localhost");		// Address of your webserver running this - this is used in runSchedule to call the API

define("USER_EMAIL",    					"you@email.com"); 			// I think this is so you dont have to regenerate tokens if you run this script elsewhere
define("USER_PASSWORD", 					"can-be-anything");			// can be anything
define("USE_TOKEN_FILE", 					1); 						// store the token in a file vs hard coding it below otherwise fill in line 51

define("FORCE_FADE_ON", 					0);							//makes it so when lights are turned off they fade to 0 (Like Philips Bulbs)
define("FORCE_FADE_OFF", 					0);							//makes it so when lights are turned on they fade to their assigned value from 0 (Like Philips Bulbs)

define("SAVE_SCHEDULE", 					1); 						//saves schedule to a binary file on save schedule.sched
define("LOG_ACTIONS", 						1); 						//saves completed actions to schedule.actioned
define("LOG_API_CALLS", 					1);							//log issued API calls

/*
	IFTTT Integration - https://github.com/bren1818/TCPLightingWebInterface/wiki/IFTTT-Integration
	These settings  should be used in conjunction with your firewall and the .htaccess file.
*/

define("ALLOW_EXTERNAL_API_ACCESS", 		0); 						//Allow outside access (Non Lan) (1 = true, 0 = false)
define("EXTERNAL_DDNS_URL", 				"http://your-address.ddns.net");

define("REQUIRE_EXTERNAL_API_PASSWORD", 	1);							//require a password for external (non lan) use IE for IFTTT? (1 = true, 0 = false)
define("EXTERNAL_API_PASSWORD",  			"P@ssW0rd");				//set what the password should be
define("RESTRICT_EXTERNAL_PORT",			1);							//if request is an external (API) user, should they only be on a specific port? (1= yes, 2=no)
define("EXTERNAL_PORT",						443);						//if you wish to use an alternate external port change this number to the corresponding port number

define("SCHEME", 		(LIGHTING_BRIDGE_PORT == 80) ? 'http' : 'https'); //Don't modify
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


define("USE_LOCAL_API_IP", 					1); 				/*To do - hook in JS */
define("LOG_DIR",							dirname(__FILE__) . DIRECTORY_SEPARATOR . "logs");


date_default_timezone_set("America/New_York");                 //Ensure this matches your timezone so if you use scheduler the hours match

/*************************************START OF INSERTED CODE FOR SUNRISE SUNSET MOD *************************/
/* 
/* look up your own location at http://latlong.net
/* 
/* for LATITUDE, locations N of equator are positive values, S of equator are negative values
/* for LONGITUDE, locations E of the prime meridian are positive, W of prime meridian are negative
/* 
/* You *must* set your time zone correctly, refer to http://php.net/manual/en/timezones.php for the formal list
/* 
/* examples: 	Vancouver, Canada would be LATITUDE 49.28 LONGITUDE -123.12 timezone "America/Vancouver"
/*           	Paris, France would be LATITUDE 48.85 LONGITUDE 2.35 timezone "Europe/Paris"
/* 		Rio de Janeiro, Brazil would be LATITUDE -22.91 LONGITUDE -43.17 timezone "America/Sao_Paulo"
/* 
/* 
 */

define("LATITUDE", 43.59);
define("LONGITUDE", -80.24);
/************************************* END OF INSERTED CODE FOR SUNRISE SUNSET MOD **************************/

/*
	MQTT Services - Broker connections settings for subscribing and publishing 
*/
define("ENABLE_MQTT", 		1); 						//Enable MQTT State Publishing (1 = true, 0 = false)
$MQTTserver = "172.16.33.8";     // change if necessary
$MQTTport = 1883;                     // change if necessary
$MQTTusername = "admin";                   // set your username
$MQTTpassword = "password";                   // set your password
$MQTTsub_id = "tcp-subscriber"; // make sure this is unique for connecting to sever - you could use uniqid()
$MQTTpub_id = "tcp-publisher"; // make sure this is unique for connecting to sever - you could use uniqid()

?>
