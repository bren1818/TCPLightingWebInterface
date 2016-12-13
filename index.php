<?php

/*
 *
 * TCP Ligthing Web UI Test Script - By Brendon Irwin
 * 
 */

include "config.php";
pageHeader("TCP Lighting Controller");

global $home;

echo '<div class="container">';
echo '<h1>'.$home->getName()."</h1>";

foreach( $home->getDevices() as $bridge ){
	
	if( $bridge->getEnabled() ){
		echo '<div class="bridge">';
			echo '<h2>'.$bridge->getName().'</h2>';
			if( $bridge->getID() == ""){
				echo "<p>".$bridge->getName()." is missing a unique ID. Please set!</p>";
			}
			$bridge->renderDevices();
		echo '</div>';
	}
	
	//pa($bridge);
}
echo '</div>';

pageFooter();
?>


<?php 
/*
if( TOKEN != "" ){
	
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
	
	?>
	<div id="toolBar"><a href="scheduler.php">Lighting Scheduler</a> | <a href="apitest.php">API Test Zone</a> | <a href="scenes.php">Scenes/Smart Control</a> | <a href="createDevice.php">Create Virtual Device</a> </div>
	<?php
	
	$deviceCount = 0;
	
	if( sizeof($DATA) > 0 ){
	
		echo '<div class="container">';
		echo '<h1>Device control</h1>';
		echo '</div>';	
			
		if ( isset( $DATA["rid"] ) ){ $DATA = array( $DATA ); }
			
		foreach($DATA as $room){
			
			if( isset($room['rid'] ) ){
				

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
						$roomBrightness = 0;
						$roomDevices = 0;
						
						foreach($DEVICES as $device){
							
							echo '<div class="'.( (isset($device['offline']) && $device['offline'] == 1) ? 'unplugged' : 'plugged' ).' device '.($device['state'] == 1 ? 'light-on' : 'light-off' ).' '.($device['prodtype'] == 'Light Fixture' ? 'light-fixture' : '' ).'" data-device-id="'.$device['did'].'">'; //power > 0 then enabled 
								//level = brightness
								//state = on or off
								echo '<p>'.$device['name'].' <a href="info.php?did='.$device['did'].'"><img src="/images/info.png"/></a></p>';
								echo '<button data-device-id="'.$device['did'].'" class="onOffDeviceToggleButton buttonOn">On</button> | <button data-device-id="'.$device['did'].'" class="onOffDeviceToggleButton buttonOff">Off</button>';
								echo '<div class="clear"></div>';
								echo '<p>Brightness:</p>';
								echo '<div class="device-slider" data-value="'.(isset($device['level']) ? $device['level'] : 100).'" data-device-id="'. $device["did"].'"></div>';
							echo '</div>';
							$roomBrightness += (isset($device['level']) ? $device['level'] : 100);
							$roomDevices++;
							
							
						}
						echo '</div>';
						
					echo '</div>';
					
				}else{
					echo 'No devices?';
					pa( $room );
				}
			
				echo '<div class="room-controls">';
					echo 'Room Brightness: <div class="room-slider" data-value="'.($roomBrightness/$roomDevices).'" data-room-id="'. $room["rid"].'"></div>';
					echo 'Room <button data-room-id="'. $room["rid"].'" class="onOffToggleButton buttonOn">On</button> | <button data-room-id="'. $room["rid"].'" class="onOffToggleButton buttonOff">Off</button>';
				echo '</div>';
			echo '</div>';
			}
		}
	
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
	echo "<h2>If you are seeing this, you haven't generated your token yet.</h2>";
	
	$CMD = "cmd=GWRLogin&data=<gip><version>1</version><email>".USER_EMAIL."</email><password>".USER_PASSWORD."</password></gip>&fmt=xml";
		
	$result = getCurlReturn($CMD);
	$tokenArray = xmlToArray($result);
	
	
	if( !isset($tokenArray["token"]) ){
		echo '<p>Could not fetch token. Ensure you have the correct IP for your bridge and that you have hit the <b>sync</b> button before running this.</p>';
		if(USE_TOKEN_FILE){
			echo '<p>Since you ae not using the token file option, ensure you paste your token in the include.php.</p>';
		}
		echo '<p><img src="images/syncgateway.png" /></p>';
	}else{ 
		if(USE_TOKEN_FILE){
			ob_clean();
			file_put_contents("tcp.token", $tokenArray["token"]);
			header("Location: index.php");
		}else{
			echo "<p>Result Token: <b>".$tokenArray["token"]."</b> save this token in the TOKEN definition in the include.php file.</p><p>Full response: | ".htmlentities($result)." | - note this has been turned to html entities for legibility.<p>";
			echo '<p><img src="images/syncgateway.png" /></p>';
		}
	}
} 
?>
</body>
</html>
*/
?>
