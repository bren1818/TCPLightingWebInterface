<!DOCTYPE html>
<html>
<head>
<title>TCP Control Script</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>
$(function(){
	$('.room-slider').slider({
      range: "min",
      min: 0,
      max: 100,
      value: 50,
      slide: function( event, ui ) {
        //$( "#amount" ).val( ui.value );
		console.log("Room: " + $(this).attr('data-room-id') );
		console.log("Set Room Brightness to " + ui.value)
		}
	});
	
	$('.device-slider').slider({
      range: "min",
      min: 0,
      max: 100,
      value: 50,
      slide: function( event, ui ) {
        //$( "#amount" ).val( ui.value );
		console.log("Device: " + $(this).attr('data-device-id') );
		console.log("Set Device Brightness to " + ui.value)
		}
	});
	
	
	$('button.onOffToggleButton').click(function(event){
		var roomID = $(this).attr('data-room-id');
		if( $(this).hasClass('buttonOn') ){
			window.alert("Turning Room " + roomID + " On!");	
		}else{
			window.alert("Turning Room " + roomID + " Off!");	
		}
	});
	
	$('button.onOffDeviceToggleButton').click(function(event){
		var DID = $(this).attr('data-device-id');
		if( $(this).hasClass('buttonOn') ){
			window.alert("Turning Device " + DID + " On!");	
		}else{
			window.alert("Turning Device " + DID + " Off!");	
		}
	});
	
});
</script>
<style>
	html *{
	  box-sizing: border-box;
	}

	.roomContainer{ max-width: 1024px; margin: 10px auto; padding: 20px; }
	.room-controls{ padding: 10px; }

	.room-slider{ margin: 20px 0;}

	.roomContainer, .room-devices, .room-controls{
		width: 100%;
		border: 1px solid #000;
	}

	.room-devices{
		width: 100%;
		position: relative;
		clear: both;
		overflow: hidden;
		margin: 10px 0;
	}

	.device{
		width: 200px;
		height: 200px;
		margin: 10px;
		padding: 10px;
		float: left;
		border: 1px solid #000;
		float: left;
	}

	.clear{ clear: both; width: 100%; }
</style>
</head>
<body>
<?php
/*
 *
 * TCP Ligthing Web UI Test Script - By Brendon Irwin
 * 
 */

include "include.php";


if( TOKEN != "" ){
	
	$URL = "https://".LIGTHING_URL.":".LIGHTING_PORT.API_PATH;
	
	//Get State of System Data
	$CMD = "cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>RoomGetCarousel</gcmd><gdata><gip><version>1</version><token>".TOKEN."</token><fields>name,image,imageurl,control,power,product,class,realtype,status</fields></gip></gdata></gwrcmd></gwrcmds>&fmt=xml";
	
	$result = getCurlReturn($CMD);
	
	$array = xmlToArray($result);
	
	$DATA = $array["gwrcmd"]["gdata"]["gip"]["room"];
	
	echo '<h1>Device control</h1>';
	
	foreach($DATA as $room){
		echo '<div class="roomContainer" data-room-id="'. $room["rid"].'">';
			echo '<h3>'.$room["name"].'</h3>';

			$DEVICES = array();
				
			if( ! is_array($room["device"]) ){
				$DEVICES[] = $room["device"]; //singular device in a room
			}else{
				$device = (array)$room["device"];
				for( $x = 0; $x < sizeof($device); $x++ ){
					if( isset($device[$x]) && is_array($device[$x]) && ! empty($device[$x]) ){
						$DEVICES[] = $device[$x];
					}
				}
			}
			
			if( sizeof($DEVICES) > 0 ){
				echo '<div class="devices">';
					echo '<p>Room Devices:</p>';
					
					echo '<div class="room-devices">';
					foreach($DEVICES as $device){
						echo '<div class="device" data-device-id="'.$device['did'].'">';
							echo '<p>'.$device['name'].'</p>';
							echo '<button data-device-id="'.$device['did'].'" class="onOffDeviceToggleButton buttonOn">On</button> | <button data-device-id="'.$device['did'].'" class="onOffDeviceToggleButton buttonOff">Off</button>';
							echo '<div class="clear"></div>';
							echo '<p>Brightness:</p>';
							echo '<div class="device-slider" data-device-id="'. $device["did"].'"></div>';
						echo '</div>';
					}
					echo '</div>';
				echo '</div>';
			}
		
			echo '<div class="room-controls">';
				echo 'Room Brightness: <div class="room-slider" data-room-id="'. $room["rid"].'"></div>';
				echo 'Room <button data-room-id="'. $room["rid"].'" class="onOffToggleButton buttonOn">On</button> | <button data-room-id="'. $room["rid"].'" class="onOffToggleButton buttonOff">Off</button>';
			echo '</div>';
		echo '</div>';
	}
	
}else{
	echo "<h1>If you are seeing this, you haven't put your token in the include.php file.</h1>";
	echo "<p>Press the sync button on the modem and re-run this script to generate one</p>";

	$CMD = "cmd=GWRLogin&data=<gip><version>1</version><email>".USER_EMAIL."</email><password>".USER_PASSWORD."</password></gip>&fmt=xml";
		
	$result = getCurlReturn($CMD);
	
	echo "<p>If you do not see a long string within <b><token></token></b> you need to ensure you have hit the sync button before running this</p>";
	echo "Result Token: | ".htmlentities($result)." | - note this has been turned to html entities for legibility.";
	
} 
?>
<p><a href="APITEST.php">API Test Zone</a></p>
</body>
</html>