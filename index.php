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
</head>
<body>
<div id="toolBar"><a href="scheduler.php">Lighting Scheduler</a> | <a href="apitest.php">API Test Zone</a> | <a href="scenes.php">Scenes/Smart Control</a></div>
<?php
/*
 *
 * TCP Ligthing Web UI Test Script - By Brendon Irwin
 * 
 */

include "include.php";


if( TOKEN != "" ){
	
	//Get State of System Data
	$CMD = "cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>RoomGetCarousel</gcmd><gdata><gip><version>1</version><token>".TOKEN."</token><fields>name,image,imageurl,control,power,product,class,realtype,status</fields></gip></gdata></gwrcmd></gwrcmds>&fmt=xml";
	$result = getCurlReturn($CMD);
	
	$array = xmlToArray($result);
	
	//check if token is expired 
	if( !isset($array["gwrcmd"]) ){
		echo '<p>GWR Command not returned, this likely indicates your token is expired, or invalid.<p>';
		echo '<p>Remove token and try regenerating a new one.</p>';
		echo '</body></html>';
		exit;
	}
	
	$DATA = $array["gwrcmd"]["gdata"]["gip"]["room"];
	
	
	echo '<div class="container">';
	echo '<h1>Device control</h1>';
	echo '</div>';
	$deviceCount = 0;
	
	foreach($DATA as $room){
		echo '<div class="roomContainer" data-room-id="'. $room["rid"].'">';
			echo '<h3>'.$room["name"].' <a href="info.php?rid='.$room["rid"].'"><img src="/images/info.png"/></a></h3>';

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
				echo '<div class="devices">';
					echo '<p>Room Devices:</p>';
					echo '<div class="room-devices">';
					foreach($DEVICES as $device){
						echo '<div class="'.( (isset($device['offline']) && $device['offline'] == 1) ? 'unplugged' : 'plugged' ).' device '.($device['state'] == 1 ? 'light-on' : 'light-off' ).' '.($device['prodtype'] == 'Light Fixture' ? 'light-fixture' : '' ).'" data-device-id="'.$device['did'].'">'; //power > 0 then enabled 
							//level = brightness
							//state = on or off
							echo '<p>'.$device['name'].' <a href="info.php?did='.$device['did'].'"><img src="/images/info.png"/></a></p>';
							echo '<button data-device-id="'.$device['did'].'" class="onOffDeviceToggleButton buttonOn">On</button> | <button data-device-id="'.$device['did'].'" class="onOffDeviceToggleButton buttonOff">Off</button>';
							echo '<div class="clear"></div>';
							echo '<p>Brightness:</p>';
							echo '<div class="device-slider" data-value="'.(isset($device['level']) ? $device['level'] : 50).'" data-device-id="'. $device["did"].'"></div>';
						echo '</div>';
					}
					echo '</div>';
					
				echo '</div>';
				
			}else{
				echo 'No devices?';
				pa( $room );
			}
		
			echo '<div class="room-controls">';
				echo 'Room Brightness: <div class="room-slider" data-room-id="'. $room["rid"].'"></div>';
				echo 'Room <button data-room-id="'. $room["rid"].'" class="onOffToggleButton buttonOn">On</button> | <button data-room-id="'. $room["rid"].'" class="onOffToggleButton buttonOff">Off</button>';
			echo '</div>';
		echo '</div>';
	}
	
	if( $deviceCount > 0 ){
		echo '<div class="container">';
			echo '<h1>Home</h1>';
			echo '<div class="house">';
				echo '<button data-device-id="all" class="onOffHouseToggleButton buttonOn">On</button> | <button data-device-id="all" class="onOffHouseToggleButton buttonOff">Off</button>';
				echo '<div class="clear"></div>';
				echo '<p>Brightness:</p>';
				echo '<div class="house-slider" data-device-id="all"></div>';
			echo '</div>';
		echo '</div>';
	}
	
}else{
	echo "<h1>If you are seeing this, you haven't put your token in the include.php file.</h1>";
	
	$CMD = "cmd=GWRLogin&data=<gip><version>1</version><email>".USER_EMAIL."</email><password>".USER_PASSWORD."</password></gip>&fmt=xml";
		
	$result = getCurlReturn($CMD);
	$tokenArray = xmlToArray($result);
	
	
	if( !isset($tokenArray["token"]) ){
		echo '<p>Could not fetch token. Ensure you have the correct IP for your bridge and that you have hit the <b>sync</b> button before running this.</p>';
		echo '<p><img src="images/syncgateway.png" /></p>';
	}else{ 
		echo "<p>Result Token: <b>".$tokenArray["token"]."</b> save this token in the TOKEN definition in the include.php file.</p><p>Full response: | ".htmlentities($result)." | - note this has been turned to html entities for legibility.<p>";
		echo '<p><img src="images/syncgateway.png" /></p>';
	}
} 
?>
</body>
</html>
