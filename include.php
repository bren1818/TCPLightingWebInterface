<?php
/*
 *
 * PHP includes
 *
 */

define("LIGTHING_URL", "192.168.1.114"); //IP address of gateway
define("LIGHTING_PORT", "443");
define("API_PATH", "/gwr/gop.php");
define("IMAGE_PATH", "https://".LIGTHING_URL."/gwr/"); //append urls to this eg: images/lighting/TCP/TCP-A19.png
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

/* 
	Some Documentation links
	http://home.stockmopar.com/updated-connected-by-tcp-api/
	http://home.stockmopar.com/connected-by-tcp-unofficial-api/
	http://forum.micasaverde.com/index.php/topic,22555.0.html
	http://code.mios.com/trac/mios_tcplighting
*/

?>