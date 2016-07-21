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
				stop: function(event, ui) {
					var room = $(this).parent().parent();
					$.get("/api.php?fx=dim&type=room&uid=" + $(this).attr('data-room-id') + "&val=" + ui.value, function( data ) {
						console.log( data );
						$(room).find('.device-slider').each(function(){
							$(this).slider('value', ui.value );
						});
					});
				}
			});
			
			$('.device-slider').slider({
				range: "min",
				min: 0,
				max: 100,
				//value: $(this).attr('data-level'),
				create: function( event, ui ){
					$(this).slider("option", "value", $(this).attr('data-value') );
					if( $(this).parent().hasClass('light-on') ){
						$(this).parent().css('background-color', 'rgba(255,255,0,' + ( $(this).attr('data-value') / 100 ) + ')');
					}
				},
				stop: function(event, ui) {
					$.get("/api.php?fx=dim&type=device&uid=" + $(this).attr('data-device-id') + "&val=" + ui.value, function( data ) {
					  console.log( data );
					  
					  
					});
				},
				slide: function( event, ui ) {
					if( $(this).parent().hasClass('light-on') ){
						$(this).parent().css('background-color', 'rgba(255,255,0,' + ( ui.value / 100 ) + ')');
					}
				}
			});
			
			$('.house-slider').slider({
				range: "min",
				min: 0,
				max: 100,
				value: 50,
				stop: function(event, ui) {
					$.get("/api.php?fx=dim&type=all&uid=" + $(this).attr('data-device-id') + "&val=" + ui.value, function( data ) {
					  console.log( data );
					  //set every slider to the new value
					  
					});
				}
			});
			
			$('button.onOffToggleButton').click(function(event){
				var roomID = $(this).attr('data-room-id');
				var room = $(this).parent().parent();
				var val = 0;
				if( $(this).hasClass('buttonOn') ){
					val = 1;	
				}
				
				$.get( "/api.php?fx=toggle&type=room&uid=" + roomID + "&val=" + val, function( data ) {
					  console.log( data );
					  
					  $(room).find('.room-devices .device').each(function(){
						if( val == 1){
							$(this).addClass('light-on');
							var v = $(this).find('.device-slider').slider( "value" );
							$(this).css('background-color', 'rgba(255,255,0,' + ( v / 100 ) + ')');
						}else{
							$(this).removeClass('light-on');
							$(this).css('background-color','transparent');
						}
					});
					  
				});
				
				
			});
			
			$('button.onOffDeviceToggleButton').click(function(event){
				var DID = $(this).attr('data-device-id');
				var val = 0;
				var light = $(this);
				if( $(this).hasClass('buttonOn') ){
					val = 1;
				}
				
				$.get( "/api.php?fx=toggle&type=device&uid=" + DID + "&val=" + val, function( data ) {
					 console.log( data );
					 if( val == 1){
						$(light).parent().addClass('light-on');
						var v = $(light).parent().find('.device-slider').slider( "value" );
						$(light).parent().css('background-color', 'rgba(255,255,0,' + ( v / 100 ) + ')');
					}else{
						$(light).parent().removeClass('light-on');
						$(light).parent().css('background-color','transparent');
					}
				});
			});
			
			$('button.onOffHouseToggleButton').click(function(event){
				var DID = $(this).attr('data-device-id');
				var val = 0;
				if( $(this).hasClass('buttonOn') ){
					val = 1;
				}
				
				$.get( "/api.php?fx=toggle&type=all&uid=" + DID + "&val=" + val, function( data ) {
					  console.log( data );
					$('.room-devices .device').each(function(){
						if( val == 1){
							$(this).addClass('light-on');
							var v = $(this).find('.device-slider').slider( "value" );
							$(this).css('background-color', 'rgba(255,255,0,' + ( v / 100 ) + ')');
						}else{
							$(this).removeClass('light-on');
							$(this).css('background-color','transparent');
						}
					});
					  
				});
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
		
		.light-on{background-color: rgba(255,255,0,1); }
		

		.clear{ clear: both; width: 100%; }
		
		.unplugged{ 
			background-image: url(data:image/png;base64,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);
			background-repeat: no-repeat; 
			}
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
	
	//Get State of System Data
	$CMD = "cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>RoomGetCarousel</gcmd><gdata><gip><version>1</version><token>".TOKEN."</token><fields>name,image,imageurl,control,power,product,class,realtype,status</fields></gip></gdata></gwrcmd></gwrcmds>&fmt=xml";
	
	$result = getCurlReturn($CMD);
	
	$array = xmlToArray($result);
	
	$DATA = $array["gwrcmd"]["gdata"]["gip"]["room"];
	
	echo '<h1>Device control</h1>';
	$deviceCount = 0;
	
	foreach($DATA as $room){
		echo '<div class="roomContainer" data-room-id="'. $room["rid"].'">';
			echo '<h3>'.$room["name"].'</h3>';

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
						echo '<div class="'.( (isset($device['offline']) && $device['offline'] == 1) ? 'unplugged' : 'plugged' ).' device '.($device['state'] == 1 ? 'light-on' : 'light-off' ).'" data-device-id="'.$device['did'].'">'; //power > 0 then enabled 
							//level = brightness
							//state = on or off
							echo '<p>'.$device['name'].'</p>';
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
		echo '<h1>Home</h1>';
		echo '<div class="house">';
			echo '<button data-device-id="all" class="onOffHouseToggleButton buttonOn">On</button> | <button data-device-id="all" class="onOffHouseToggleButton buttonOff">Off</button>';
			echo '<div class="clear"></div>';
			echo '<p>Brightness:</p>';
			echo '<div class="house-slider" data-device-id="all"></div>';
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