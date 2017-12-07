<?php
/*
 *
 * PHP includes
 *
 */
include "config.inc.php";


function isLocalIPAddress($IPAddress){
    if($IPAddress == '127.0.0.1'){return true;} 
    return ( !filter_var($IPAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) );
}

$REMOTE_IP = getenv('HTTP_CLIENT_IP')?:
getenv('HTTP_X_FORWARDED_FOR')?:
getenv('HTTP_X_FORWARDED')?:
getenv('HTTP_FORWARDED_FOR')?:
getenv('HTTP_FORWARDED')?:
getenv('REMOTE_ADDR');


/*Function to Print Array*/
function pa($array){
	echo '<pre>'.print_r($array,true).'</pre>';
}

function getCurlReturn($postDataString){
	$URL = SCHEME."://".LIGTHING_BRIDGE_IP.":".LIGHTING_BRIDGE_PORT."/gwr/gop.php";
	
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
<html lang="en">
<head>
	<title>TCP Control Script</title>
	<link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
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
	<link rel="stylesheet" href="css/jquery-ui.min.css" />
	<link rel="stylesheet" href="style.css" />
	<script src="js/libs.js"></script>
	<script>
		//local IP
		<?php
			if( USE_LOCAL_API_IP ){
				global $REMOTE_IP;
				if( isLocalIPAddress($REMOTE_IP) ){
					?>
					var API_IP = '<?php echo LOCAL_URL; ?>';
					<?php
				}else{
				?>
				var API_IP = '<?php echo $_SERVER['REQUEST_URI']; ?>';
				<?php
				}
			}else{
			?>
			var API_IP = '<?php echo $_SERVER['REQUEST_URI']; ?>';
			<?php
			}
		?>
	</script>
	<script src="scripts.js"></script>
    
	<title><?php echo $title; ?></title>
</head>
<body>
<div id="headerBar">
	<a href="index.php">Home</a>
    <a href="index.php#scenes">Scenes</a>
	<a target="_blank" href="https://github.com/bren1818/TCPLightingWebInterface">GitHub Link</a>
    <a target="_blank" href="https://github.com/bren1818/TCPLightingWebInterface/wiki">Wiki</a>
    <a href="queryBuilder.php">IFTTT Query Builder</a>
</div>
	<?php
}

function pageFooter(){
?>
	<div id="toolBar"><a href="scheduler.php">Lighting Scheduler</a> | <a href="createDevice.php">Create Virtual Device</a> | <a href="setDateTime.php">Set Bridge Date Time</a></div>
</body>
</html>	
	<?php
}




/*do some checks based on parameters above*/
if( ALLOW_EXTERNAL_API_ACCESS == 1 ){
	//allow external
}else{
	//if not allowing external access, check you're a local IP
	if( ! isLocalIPAddress($REMOTE_IP) ){
		//if not local you can only hit the API
		if( basename($_SERVER["SCRIPT_FILENAME"], '.php') != "api" ){
			echo "This application is restricted to the internal network.";
			exit;
		}
	}
}

?>
