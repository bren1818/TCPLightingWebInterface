<?php
	/*
	 *
	 * TCP Ligthing Web UI Scenes Create/Edit Script - By Brendon Irwin
	 * 
	 */

	include "include.php";
	pageHeader("TCP Lighting - Scene Controller");
	if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
		
		exit;
	}
?>
<script>
	$(function(){
		$('.activateScene').click(function(event){
			event.preventDefault();
			var sceneID = $(this).data("scene");
			$.post( "scenes.php", { scene: sceneID, action: 'on' })
			  .done(function( data ) {
				console.log( "Response " + data );
			});
		});
		
		$('.deactivateScene').click(function(event){
			event.preventDefault();
			var sceneID = $(this).data("scene");
			$.post( "scenes.php", { scene: sceneID, action: 'off' })
			  .done(function( data ) {
				console.log( "Response " + data );
			});
		});
		
		$('.deleteScene').click(function(event){
			event.preventDefault();
			var sceneID = $(this).data("scene");
			if (window.confirm("Are you sure?")) {
				$.post( "scenes.php", { scene: sceneID, action: 'delete' })
				  .done(function( data ) {
					console.log( "Response " + data );
					window.location = "scenes.php";
				});
			}
		});
		
		
		$('.scene-slider').slider({
				range: "min",
				min: 0,
				max: 100,
				//value: $(this).attr('data-level'),
				create: function( event, ui ){
					$(this).slider("option", "value", $(this).parent().find("input[name='DIM_SCENE']").val() );
				},
				stop: function(event, ui) {
					$(this).parent().find('input[name="DIM_SCENE"]').val( ui.value );
				},
				slide: function( event, ui ) {
					$(this).parent().find('input[name="DIM_SCENE"]').val( ui.value );
				}
			});
		
		
	});
</script>	
<?php
	echo '<div class="roomContainer">';
	echo '<h2>Scenes / Smart Control</h2>';
	
	$CMD = "cmd=SceneGetListDetails&data=<gip><version>1</version><token>".TOKEN."</token><bigicon>1</bigicon></gip>";
	$result = getCurlReturn($CMD);
	$array = xmlToArray($result);
	
	$scenes = $array["scene"];
	if( is_array($scenes) && isset($_REQUEST['SID']) && $_REQUEST['SID'] != ""){
		$scene =  $_REQUEST['SID'];
		for($x = 0; $x < sizeof($scenes); $x++){
			if($scenes[$x]["sid"] == $scene ){
			?>
			<div class="scene-container" id="scene-id-<?php echo $scenes[$x]["sid"]; ?>">
                	<!--<div class="scene-info"><a href="scenescreatedit.php?SID=<?php echo $scenes[$x]["sid"]; ?>"><img src="images/info.png"/></a></div>-->
					<p><b><?php echo $scenes[$x]["name"]; ?></b> (<?php echo is_array($scenes[$x]["device"]) ? sizeof($scenes[$x]["device"]) : ""; ?>)</p>
					<p><img src="css/<?php echo $scenes[$x]["icon"]; ?>" /></p>
					<p>
                        <button data-scene-mode="run" data-scene-id="<?php echo $scenes[$x]["sid"]; ?>" class="runScene">Run Scene</button> 
                        <button data-scene-mode="off" data-scene-id="<?php echo $scenes[$x]["sid"]; ?>" class="runScene">Scene Devices Off</button> 
                        <button data-scene-mode="on" data-scene-id="<?php echo $scenes[$x]["sid"]; ?>" class="runScene">Scene Devices On</button>
                    </p>
			</div>
            <div class="clear"></div>
           
			<?php
				$devices = array();
				$scene = $scenes[$x];
				if(  isset( $scenes[$x]['device'][0]["id"] ) ){
					foreach(  $scenes[$x]['device'] as $d ){
						$devices[] = $d["id"];
					}
				}else{
					$devices[] = $scenes[$x]['device']["id"];
				}
					
			
			}
		}
		
		//pa( $devices );
		
		if( !isset( $scene ) ){
			echo "Invalid Scene";
			exit;
		}
		?>
		<style>
			.roomSceneContainer,
			.roomDeviceContainer{
				position: relative;
			}
		
			.roomSceneContainer{
				margin-top: 10px;
				padding-left: 20px;
			}
			
			.roomDeviceContainer{
				border: 1px solid #000;
				padding: 10px;
				margin: 10px;
			}
			
			
			.EnabledOrNot{
				top: 10px;
				right: 10px;
				position: absolute;
				background-color: #fff;
				padding: 3px;
			}
			
			h2.room-name{
				padding: 10px;
				border-radius: 10px;
				display: block;
				width: 100%;
				margin-top: 0px;
			}
			
			.device-controls,
			.room-controls{
				padding: 20px;
			}
			
			.control{
				padding: 10px 0;
			}
			
			.room-devices.room-devices-toggled-1,
			.device-controls.controls-toggled-0,
			.room-controls.controls-toggled-0{
				display: none;
			}
			
			.room-controls.controls-toggled-1{
				border: 1px solid #000;
			}
			
			.switch-val-0 + .control.control-slider{
				display: none;
			}
		
		</style>
	

		<div style="padding: 20px;">    				
		<?php
			function renderSwitch($roomID, $deviceID, $ON_OFF){
				?>
				<div class="control control-switch switch-val-<?php echo $ON_OFF; ?>">
					<label class="switch">
					  <input name="SWITCH_SCHED" value="1" type="checkbox" <?php echo ($ON_OFF == 1) ? "checked" : ""; ?>>
					  <div class="slider round"></div>
					</label>
				</div>
				<?php
			}
			
			function renderSlider( $roomID, $deviceID, $VALUE ){
				?>
				<div class="control control-slider">
					<div class="scene-slider" data-device-id="all"></div>
					<input name="DIM_SCENE" type="hidden" value="<?php echo $VALUE ?>" />
				</div>
				<?php
			}
		
		
			function renderDevice($d, $rtoggled, $rid){
				global $devices;
				global $scene;
				echo '<div class="roomDeviceContainer" data-room-id="'.$rid.'" data-device-id="'.$d['did'].'" data-device-name="'.$d['name'].'">';
					echo '<p>'.$d['name'].'</p>';
					
					$SWITCH_ON = 0;
					$LEVEL = 0;
					$toggled = 0;
					
					if(  isset( $scene['device'][0]["id"] ) ){
						
						foreach(  $scene['device'] as $sd ){
							if( $d["did"] == $sd["id"] ){
								if( isset( $sd["cmd"]["0"] ) ){
									$SWITCH_ON = $sd["cmd"]["0"]["value"];
									if( ! is_array( $sd["cmd"]["1"]["value"] ) ){
										//echo "<p>Level: ". $sd["cmd"]["1"]["value"]."</p>";
										$LEVEL = $sd["cmd"]["1"]["value"];
									}else{
										//echo "<p>Level: Unknown</p>"; // bug with bridge sometimes doesn't set for unplugged devices
									}
								}else{
									//echo "Device set to Off!";
								}
							}else{
								//echo "Not in Scene"; 
							}
						}
					}else{
						//check
						if($d["did"] == $scene['device']["id"] ){
						
							$SWITCH_ON = $scene['device']["0"]["value"];
							//echo "<p>Level: ". $scene['device']["cmd"]["1"]["value"]."</p>";
							$LEVEL = $scene['device']["cmd"]["1"]["value"];
						}else{
							//echo "Not in Scene";
						}
					}

					if( $rtoggled== 1 || in_array( $d['did'], $devices ) ){
						$toggled = 1;
						echo '<div class="EnabledOrNot">Enabled: <input class="deviceToggle" type="checkbox" name="device[]" value="'.$d["did"].'" checked /></div>';
					}else{
						echo '<div class="EnabledOrNot">Enabled: <input class="deviceToggle" type="checkbox" name="device[]" value="'.$d["did"].'"/></div>';
					}

					echo '<div class="device-controls controls-toggled-'.$toggled.'">';
						renderSwitch($rid, $d["did"], $SWITCH_ON );
						renderSlider( $rid, $d["did"], $LEVEL );
					echo '</div>';

					
				echo '</div>';
			}
			
			function renderRoom($r, $toggled){
				global $scene;
				echo '<div class="roomSceneContainer" data-room-name="'.$r["name"].'" data-room-id="'.$r["rid"].'">';
				echo '<h2 class="room-name room-color-'.$r['colorid'].'">'.$r["name"].'</h2>';
				$SWITCH_ON = 0;
				$LEVEL = 0;
				
				if(  isset( $scene['device'][0]["id"] ) ){
					foreach(  $scene['device'] as $sr ){
						if( $r["rid"] == $sr["id"] ){
							$SWITCH_ON = $sr["cmd"]["0"]["value"];
							$LEVEL =  $sr["cmd"]["1"]["value"];
						}
					}
				}else{
					if($r["rid"] == $scene['device']["id"] ){
						$SWITCH_ON = $scene['device']["cmd"]["0"]["value"];
						$LEVEL = $scene['device']["cmd"]["1"]["value"];
					}
				}
				
				if( $toggled == 1 ){
					echo '<div class="EnabledOrNot">Enabled: <input class="roomToggle" type="checkbox" name="room[]" value="'.$r["rid"].'" checked /></div>';
				}else{
					echo '<div class="EnabledOrNot">Enabled: <input class="roomToggle" type="checkbox" name="room[]" value="'.$r["rid"].'"/></div>';
				}
				
				echo '<div class="room-controls controls-toggled-'.$toggled.'">';
				
				renderSwitch($r["rid"], $r["rid"], $SWITCH_ON );
				renderSlider( $r["rid"], $r["rid"], $LEVEL );
				
				echo '</div>';
			}
			
		
			$CMD = "cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>RoomGetCarousel</gcmd><gdata><gip><version>1</version><token>".TOKEN."</token><fields>name,image,imageurl,control,power,product,class,realtype,status</fields></gip></gdata></gwrcmd></gwrcmds>&fmt=xml";
			$result = getCurlReturn($CMD);
			$array = xmlToArray($result);
			if( isset( $array["gwrcmd"]["gdata"]["gip"]["room"] ) ){
				$DATA = $array["gwrcmd"]["gdata"]["gip"]["room"];
			}
			
			if( sizeof($DATA) > 0 ){
				echo '<hr />';
			
				echo '<h1>Scene Settings</h1>';
				if ( isset( $DATA["rid"] ) ){ $DATA = array( $DATA ); }
			

			foreach($DATA as $room){
					//echo '<optgroup label="Room - '.$room["name"].'">';
					$toggled = 0;

					//check if roomID is in scene...
					if( in_array( $room['rid'], $devices ) ){
						$toggled = 1;
						renderRoom($room, 1);
						//pa( $scene );
						
					}else{
						renderRoom($room, 0);
						
					}
					
					echo '<div class="room-devices room-devices-toggled-'.$toggled.'">';
						//check if Device(s) in scene
						if(  is_array($room["device"]) ){
							$device = (array)$room["device"];
							if( isset($device["did"]) ){
								renderDevice( $device, $toggled, $room['rid'] );
							}else{	
								for( $x = 0; $x < sizeof($device); $x++ ){
									if( isset($device[$x]) && is_array($device[$x]) && ! empty($device[$x]) ){
										renderDevice( $device[$x], $toggled,  $room['rid']);
									}
								}
							}
						}
					
					echo '</div>';
					echo '</div>'; //end of render room div
				}
			}
		?>
		</div>				
		
		<?php
	}else{
		?>
        <div style="padding: 20px;"> 
			<h2>Create Scene</h2>
			<p>Feature not built yet... sorry!</p>
        </div>
		<?php
	}
	echo '</div>';
pageFooter();
?>