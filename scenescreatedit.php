<?php
	/*
	 *
	 * TCP Ligthing Web UI Scenes Create/Edit Script - By Brendon Irwin
	 * 
	 */

	include "include.php";
	
	if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
		/*For Saving Scene*/
		$action = "";
		$sceneID = "";
		
		if( isset( $_POST ) && isset( $_POST['action'] ) && $_POST['action'] != "" ){
			$action = $_POST['action'];
		}
		
		if( isset( $_POST ) && isset( $_POST['sceneID'] ) && $_POST['sceneID'] != "" ){
			$sceneID = $_POST['sceneID'];
		}
		
		if( $sceneID == "" || $action == "" ){
			echo json_encode( array("error" => "Invalid SceneID or Action") );
			exit;
		}
		
		
		//echo "SceneID: ".$sceneID." Action: ". $action;
		
		if( $action == "save"){
			echo '<pre>'.print_r($_POST,true).'</pre>';
			
			if( $sceneID == -1 ){
				//New Scene
				$sceneID = "<sid>0</sid>"; // will populate with number
			}else{
				//Updating a scene
				$sceneID = '<sid>'.$sceneID.'</sid>';
			}
			
			$icon = "images/scene/". $_POST['icon'];
			$type = "manualcustom";
			$active = isset($_POST["active"]) ? $_POST["active"] : 1 ;
			$name = isset($_POST['name']) ? $_POST['name'] : "New Scene";
			$schedule = -1;
			$startTime = -1;
			$stopTime = -1;
			$every = "0,1,2,3,4,5,6";
			
			$icon  = htmlspecialchars($icon, ENT_XML1 | ENT_QUOTES, 'UTF-8');
			$name  = htmlspecialchars($name, ENT_XML1 | ENT_QUOTES, 'UTF-8');
			
			$deviceStr = "";
			
			if( isset( $_POST['rooms'] ) && is_array($_POST['rooms'] ) ){
				foreach( $_POST['rooms'] as $room ){
					$deviceStr .= "<device><id>".$room["rid"]."</id><type>R</type><cmd><type>power</type><value>".$room["toggled"]."</value></cmd><cmd><type>level</type><value>".$room["value"]."</value></cmd></device>";	
				}
			}
			
			if( isset( $_POST['devices'] ) && is_array($_POST['devices'] ) ){
				foreach( $_POST['devices'] as $dev ){
					//if( $dev["toggled"] == 0 )
					$deviceStr .= "<device><id>".$dev["did"]."</id><type>D</type><cmd><type>power</type><value>".$dev["toggled"]."</value></cmd><cmd><type>level</type><value>".$dev["value"]."</value></cmd></device>";	
				}
			}
			
			if( isset( $_POST['schedule'] ) && is_array($_POST['schedule'] ) ){
				$startTime =  $_POST['schedule']['starttime'];
				$stopTime =  $_POST['schedule']['stoptime'];
				$every =  $_POST['schedule']['every'];
				$schedule = 1;
				$type = "schedulecustom";
			}
						
					//	pa( $_POST );
				//	echo "Start: ".$startTime.", stop: ".$stopTime.", every: ".$every;
					//exit;

			$cmd = "cmd=SceneCreateEdit&data=<gip><version>1</version><token>".TOKEN."</token>".$sceneID."<active>".$active."</active>";
			
			$cmd .= "<name>".$name."</name>";
			$cmd .= "<type>".$type."</type>";
			$cmd .= "<islocal>1</islocal>"; //unknown...
			if( $schedule == 1){
				$cmd .= "<every>".$every."</every>";
				if( $startTime !=  '-1' ){
					$cmd .= "<starttime>".$startTime."</starttime>";
				}
				
				if( $stopTime != '-1' ){
					$cmd .= "<stoptime>".$stopTime."</stoptime>";
				}
			}
			$cmd .= "<icon>".$icon."</icon>";
			$cmd .= $deviceStr;
			$cmd.= "</gip>";
			
		
			
			$result = getCurlReturn($cmd);
			$array = xmlToArray($result);
			
			ob_clean();
			echo json_encode( array("success" => 1, "cmd" => $cmd, "scene" => $array["sid"], "fx" => $action, "resp" => $array) );
			
		
			
		}
		
		if( $action == "delete"){
			//<gip><version>1</version><token>%s</token><sid>%s</sid></gip>
			$CMD = "cmd=SceneDelete&data=<gip><version>1</version><token>".TOKEN."</token><sid>".$sceneID."</sid></gip>"; 
			$result = getCurlReturn($CMD);
			$array = xmlToArray($result);
			ob_clean();
			echo json_encode( array("success" => 1, "scene" => $sceneID, "fx" => $action, "resp" => $array) );
		}
		
		
		exit;
	}
	pageHeader("TCP Lighting - Scene Controller");
	
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

	#scheduleContainer .scheduleEnabled-1{ display: block; }
	#scheduleContainer .scheduleEnabled-0{ display: none; }
	
	.scene-disabled #runScene, .scene-disabled #sceneOff, .scene-disabled #sceneOn{
		display: none;
	}
</style>
<script>
	$(function(){
		//run scene, scene on, scene off function in main js file.
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
		
		$('.roomToggle').change(function(){
			console.log( $(this).attr('value') + ( $(this).prop('checked') ? ' checked' : '' ) );
			if( $(this).prop('checked') == true){
				//toggling on the room
				$(this).closest('.roomSceneContainer').find('.room-controls').removeClass('controls-toggled-0').addClass('controls-toggled-1');
				$(this).closest('.roomSceneContainer').find('.room-devices').removeClass('room-devices-toggled-0').addClass('room-devices-toggled-1');
			}else{
				//toggling off the room
				$(this).closest('.roomSceneContainer').find('.room-controls').removeClass('controls-toggled-1').addClass('controls-toggled-0');
				$(this).closest('.roomSceneContainer').find('.room-devices').removeClass('room-devices-toggled-1').addClass('room-devices-toggled-0');
				$(this).closest('.roomSceneContainer').find('.deviceToggle').prop('checked', false);
			}	
		});
		
		$('.deviceToggle').change(function(){
			console.log( $(this).attr('value') + ( $(this).prop('checked') ? ' checked' : '' ) );
			if( $(this).prop('checked') == true){
				$(this).closest('.roomDeviceContainer').find('.device-controls').removeClass('controls-toggled-0').addClass('controls-toggled-1');
			}else{
				$(this).closest('.roomDeviceContainer').find('.device-controls').removeClass('controls-toggled-1').addClass('controls-toggled-0');
			}
		});
		
		$('.control-switch').click(function(event){
			event.preventDefault();
			if( $(this).hasClass('switch-val-1') ){
				console.log("Toggle switch from on to off");
				$(this).removeClass('switch-val-1').addClass('switch-val-0');
				$(this).find('input[type="checkbox"]').prop('checked', false);
				$(this).find('input[type="checkbox"]').attr('value', 0 );
			}else{
				console.log("Toggle switch from off to on");
				$(this).removeClass('switch-val-0').addClass('switch-val-1');
				$(this).find('input[type="checkbox"]').prop('checked', true);
				$(this).find('input[type="checkbox"]').attr('value',1);
			}
		});
		
		
		$('#saveScene').click(function(event){
			event.preventDefault();
			
			console.log("here");
			
			$('.switch-val-0 .switch input.device-toggle').each(function(){
				$(this).attr('value', 0); //fix for non zeroed out switches
			});
			
			/*To Do - In Progress*/
			
			var rooms = [];
			var devices = [];
			
			
			//get rooms, then devices
			$('.roomSceneContainer').each(function(){
				
				//console.log( $(this).attr('data-room-name') + " Room ID: " + $(this).attr('data-room-id') + " - toggled: " + $(this).find(' > .EnabledOrNot > input.roomToggle').prop('checked') );
				var rid = $(this).attr('data-room-id');
				var rname = $(this).attr('data-room-name');
				var ron = "";
				var rval = "";
				
				
				//if checked, get the on or off setting, 
				if( $(this).find(' > .EnabledOrNot > input.roomToggle').prop('checked') == true ){
					//if on, get slider value
				
					if( $(this).find('.control-switch input.device-toggle').prop('checked') == true ){
						ron = 1;
						console.log("Room " + $(this).attr('data-room-name') + " is set to ON.");
						console.log("Room is set to: " + $(this).find('.control-slider input.value-slider').attr('value') );
						rval = $(this).find('.control-slider input.value-slider').attr('value');
						
					}else{
						console.log("Room " + $(this).attr('data-room-name') + " is set to OFF.");
						ron = 0;
						rval = 0;
					}
					
					rooms.push({ rid: rid, name: rname, toggled : ron, value: rval, renabled: 1  });
	
				}else{
					//if off, check rooms
					$(this).find('.roomDeviceContainer').each(function(){	
						var did = $(this).attr('data-device-id');
						var don = "";
						var dval = "";
						
						
						var dname = $(this).find(' > p').html();
						if( $(this).find('.EnabledOrNot > input.deviceToggle').prop('checked') == true ){
							console.log(dname + " is part of scene.");
							//check if device is set to ON or OFF.
							if( $(this).find('.control-switch input.device-toggle').attr('value') == 1 ){
								don = 1;
								console.log( dname + " is set to ON");
								
								//get value.
								console.log("Light is set to: " + $(this).find('.control-slider input.value-slider').attr('value') );
								dval = $(this).find('.control-slider input.value-slider').attr('value');
							}else{
								console.log( dname + " is set to OFF");
								don = 0;
								dval = 0;
							}
							
							devices.push( { did: did, name: dname, toggled: don, value: dval, enabled: 1 } );
						}else{
							//ignore device
							console.log(dname + " not part of scene.");
						}
					});
				}
				
			});
			
			var sID = $('#sceneID').val();
			var sName = $('#SceneName').val();
			var icon = $('#sceneIcon option:selected').attr('value');
			var enabled = $('input[name="sceneActive"]:checked').attr('value');
			var schedule = 0;
			
			//schedule
			if( $('.sched-switch input').prop("checked") ){
				var startTime = ( $('#startHour').val() == "sunrise" || $('#startHour').val() == "sunset" ) ? $('#startHour').val() : $('#startHour').val() + ':' + $('#startMin').val();
				var stopTime = ( $('#stopHour').val() == "sunrise" || $('#stopHour').val() == "sunset" ) ? $('#stopHour').val() : $('#stopHour').val() + ':' + $('#stopMin').val();
				
				if( startTime == stopTime && startTime != -1 ){
					window.alert("Scheduled start time cannot be the same as stop time.");
					return;
				}
				
				var every = "";
				
				$('input[name="every"]').each(function(){
					if( $(this).prop("checked") ){
						every = every + $(this).val() + ",";
					}
				});
				
				if( every.length > 1){
					every = every.slice(0, -1);
				}
				
				if( every == "" ){
					window.alert("If you wish to use the scene schedule, you must specify atleast one day.");
					return;
				}
				
				schedule = { 
					'starttime' : startTime,
					'stoptime' : stopTime,
					'every' : every
				};
				
				
			}
			
			var scene = {action : "save", sceneID: sID, name: sName, icon: icon,  rooms: rooms, devices: devices, active: enabled, schedule : schedule };
			
			$.post( "scenescreatedit.php",  scene ).done( function( data ){
				console.log("Response: " + data);
				var json =  jQuery.parseJSON( data );
				if( json.resp.rc == 200 ){
					window.alert("Scene Saved");
					$('#scene-id-' + sID ).removeClass("scene-disabled scene-enabled");
					if( enabled == 1 ){
						$('#scene-id-' + sID ).addClass("scene-enabled");
					}else{
						$('#scene-id-' + sID ).addClass("scene-disabled");
					}
				}
				
				if( sID != json.resp.sid && sID == -1){
					window.location = "scenescreatedit.php?SID=" + json.resp.sid;
				
				console.log( json );
				}
				
			});
			
			//post the scene data and save or update
			
		});
		
		$('#save2').click(function(event){
			event.preventDefault();
			$('#saveScene').click();
		});
		
		
		$('#deleteScene').click(function(event){
			event.preventDefault();
			var SID = $('#sceneID').attr('value');
			var SName = $('#SceneName').attr('value');
			
			var c = confirm("Are you sure you wish to delete the scene: '" + SName + "' Scene ID: " + SID + "?" );
			
			if ( c == true ){
				$.post( "scenescreatedit.php",  { action: 'delete', sceneID: SID } ).done( function( data ){
					var resp =  jQuery.parseJSON( data );
					if( resp.resp.rc == 200 ){
						window.alert("Scene Deleted OK!");
						window.location = "index.php#scenes";
					}else{
						window.alert("There may have been an error in deleting the scene.");
					}	
				});
			}
		});
		
		$('#sceneDisabled').click(function(event){
			event.preventDefault();
			var SID = $('#sceneID').attr('value');
			$.post( "scenescreatedit.php",  { action: 'disable', sceneID: SID } ).done( function( data ){
				var resp =  jQuery.parseJSON( data );
				if( resp.resp.rc == 200 ){
					window.alert("Scene Disabled!");
				}else{
					window.alert("There may have been an error in disabling the scene.");
				}	
			});
		});
		
		$('#sceneIcon').change(function(event){
			event.preventDefault();
			var icon = $('#sceneIcon option:selected').attr('value');
			$('#icon').attr('src', 'css/images/scene/' + icon );
		});
		
		$('#startHour, #stopHour').change(function(){
			if( $(this).val() == "sunrise" || $(this).val() == "sunset" || $(this).val() == "-1" ){
				$(this).parent().find("select").not($(this)).attr("disabled","disabled");
				//$('#schedule .timeContainer').not( $(this).parent() ).find("select option[value='" + $(this).val() + "']");
			}else{
				$(this).parent().find("select").not($(this)).removeAttr("disabled","disabled");
			}
		});
		
		
		$('.sched-switch input').change(function(){
			var toggled = $(this).prop("checked");
			if( toggled ){
				$('#schedule').removeClass('scheduleEnabled-0').addClass('scheduleEnabled-1');
			}else{
				$('#schedule').removeClass('scheduleEnabled-1').addClass('scheduleEnabled-0');
			}
		});	
	});
</script>	
<?php
	echo '<div class="roomContainer" style="padding: 20px;">';
	echo '<h2>Scenes / Smart Control</h2>';
	
	$CMD = "cmd=SceneGetListDetails&data=<gip><version>1</version><token>".TOKEN."</token><bigicon>1</bigicon></gip>";
	$result = getCurlReturn($CMD);
	$array = xmlToArray($result);
	
	$scenes = $array["scene"];
	if( is_array($scenes) && isset($_REQUEST['SID']) && $_REQUEST['SID'] != ""){
		$scene =  $_REQUEST['SID'];
		//thought -> if -1 New Scene?
		$foundScene = 0;
		for($x = 0; $x < sizeof($scenes); $x++){
			if($scenes[$x]["sid"] == $scene ){
				$foundScene = 1;
			?>
			<div class="scene-container <?php echo $scenes[$x]["active"] == 1 ? "scene-enabled" : "scene-disabled"; ?>" id="scene-id-<?php echo $scenes[$x]["sid"]; ?>">
                	
					<!--<p><b><?php echo $scenes[$x]["name"]; ?></b> (<?php echo is_array($scenes[$x]["device"]) ? sizeof($scenes[$x]["device"]) : ""; ?>)</p>-->
					<p><img id="icon" src="css/<?php echo $scenes[$x]["icon"]; ?>" /></p>
					<p>
						<?php $icons = array("away.png", "bolt.png", "clock.png", "coffee.png", "dim.png", "eat.png", "e_car.png", "fan_cool.png", "fan_heat.png", "heart.png", "home.png", "lamp.png", "light.png", "music.png", "night.png", "off_to_work.png", "rainy.png", "sensor.png", "star.png", "target.png", "thermostat.png", "tree.png", "tv.png", "vacation.png", "washing_machine.png", "zroom_00.png", "zroom_01.png", "zroom_02.png", "zroom_03.png", "zroom_04.png", "zroom_05.png", "zroom_06.png", "zroom_07.png", "zroom_08.png", "zroom_09.png"); ?>
						<select name="sceneIcon" id="sceneIcon">
							<?php foreach($icons as $icon ){
								echo '<option value="'.$icon.'"'.($scenes[$x]["icon"] == "images/scene/".$icon ? " selected" : "").'>'. ucfirst( pathinfo($icon)['filename'] ).'</option>';
							}
							?>
						</select>
					</p>
					<p>
                        <button id="runScene" data-scene-mode="run" data-scene-id="<?php echo $scenes[$x]["sid"]; ?>" class="runScene">Run Scene</button> 
                        <button id="sceneOff" data-scene-mode="off" data-scene-id="<?php echo $scenes[$x]["sid"]; ?>" class="runScene">Scene Devices Off</button> 
                        <button id="sceneOn" data-scene-mode="on" data-scene-id="<?php echo $scenes[$x]["sid"]; ?>" class="runScene">Scene Devices On</button> 
                    </p>
			</div>
			<p>Scene ID: <?php echo $scenes[$x]["sid"]; ?></p>
			<p>Scene Name: <input id="SceneName" type="text" value="<?php echo $scenes[$x]["name"]; ?>" /><p>
			<p>Scene Enabled: <input type="radio" value="1" name="sceneActive" <?php echo ($scenes[$x]["active"] == 1 ? "checked" : "" ); ?>/> Scene Disabled: <input type="radio" value="0" name="sceneActive" <?php echo ($scenes[$x]["active"] == 0 ? "checked" : "" ); ?>/></p>
			
			<p>Save Scene: <button id="saveScene" data-scene-id="<?php echo $scenes[$x]["sid"]; ?>">Save Scene</button></p>
			<p>Delete Scene: <button id="deleteScene" data-scene-id="<?php echo $scenes[$x]["sid"]; ?>">Delete Scene</button></p>
			

			<input type="hidden" value="<?php echo $scenes[$x]["sid"]; ?>" name="sceneID" id="sceneID" />
			<!--<input type="hidden" value="<?php echo $scenes[$x]["sid"]; ?>" name="sceneIcon" id="sceneIcon" />-->
			
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
		
		if( !isset( $scene )  || $foundScene == 0 && $scene != -1){
			echo "<p>Invalid Scene</p>
			<p>You may have an invalid ID, or the Scene may have been deleted.<p>";
			echo '</div>';
			pageFooter();
			exit;
		}
		
		if( $scene == -1){
			?>
			<div class="scene-container" id="scene-id-<?php echo $scenes[$x]["sid"]; ?>">
					<p><img id="icon" src="css/images/scene/bolt.png" /></p>
					<p>
						<?php $icons = array("away.png", "bolt.png", "clock.png", "coffee.png", "dim.png", "eat.png", "e_car.png", "fan_cool.png", "fan_heat.png", "heart.png", "home.png", "lamp.png", "light.png", "music.png", "night.png", "off_to_work.png", "rainy.png", "sensor.png", "star.png", "target.png", "thermostat.png", "tree.png", "tv.png", "vacation.png", "washing_machine.png", "zroom_00.png", "zroom_01.png", "zroom_02.png", "zroom_03.png", "zroom_04.png", "zroom_05.png", "zroom_06.png", "zroom_07.png", "zroom_08.png", "zroom_09.png"); ?>
						<select name="sceneIcon" id="sceneIcon">
							<?php foreach($icons as $icon ){
								echo '<option value="'.$icon.'"'.( "images/scene/bolt.png" == "images/scene/".$icon ? " selected" : "").'>'. ucfirst( pathinfo($icon)['filename'] ).'</option>';
							}
							?>
						</select>
					</p>
					
			</div>
			<p>Scene ID: <?php echo "To be generated on save."; ?></p>
			<p>Scene Name: <input id="SceneName" type="text" value="New Scene" placeholder="Type a Scene Name" /><p>
			<p>Save Scene: <button id="saveScene" data-scene-id="-1">Save Scene</button></p>
			
			<input type="hidden" value="-1" name="sceneID" id="sceneID" />
            <div class="clear"></div>
			
			<?php
		}
		
		?>

		<div id="sceneSettings" style="padding: 20px;">    				
		<?php
			function renderSwitch($roomID, $deviceID, $ON_OFF){
				?>
				<div class="control control-switch switch-val-<?php echo $ON_OFF; ?>">
					<label class="switch">
					  <input class="device-toggle" data-device-id="<?php echo $deviceID; ?>" data-room-id="<?php echo $roomID; ?>" name="SWITCH_SCHED" value="1" type="checkbox" <?php echo ($ON_OFF == 1) ? "checked" : ""; ?>>
					  <div class="slider round"></div>
					</label>
				</div>
				<?php
			}
			
			function renderSlider( $roomID, $deviceID, $VALUE ){
				?>
				<div class="control control-slider">
					<div class="scene-slider" data-device-id="all"></div>
					<input class="value-slider" data-device-id="<?php echo $deviceID; ?>" data-room-id="<?php echo $roomID; ?>" name="DIM_SCENE" type="hidden" value="<?php echo $VALUE ?>" />
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
							
							if( isset( $scene['device']["0"] ) ){
								$SWITCH_ON = $scene['device']["0"]["value"];
								//echo "<p>Level: ". $scene['device']["cmd"]["1"]["value"]."</p>";
								$LEVEL = $scene['device']["cmd"]["1"]["value"];
							}else{
								//echo "Here!";
								$SWITCH_ON = $scene['device']['cmd']['0']["value"];
								$LEVEL = $scene['device']["cmd"]['1']["value"];
								//pa( $scene );
							}
							
						}else{
							//echo "Not in Scene";
						}
					}

					if( in_array( $d['did'], $devices ) ){
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
			
			?>
			
			<h1>Scene Schedule</h1>
			
			<?php
			$SceneSchedule = 0;
			$days = "0,1,2,3,4,5,6";
			$startTime = -1;
			$stopTime = -1;
		
			if( $scene == -1 ){ //scene id
				//new scene
			}else{
				if( isset($scene["every"]) && $scene["every"] != "" ){
					$days = $scene["every"];
					$startTime = ( isset($scene["starttime"]) && $scene["starttime"] != "" ) ? $scene["starttime"] : '-1'; //could be sunrise / sunset
					$stopTime = ( isset($scene["stoptime"]) && $scene["stoptime"] != "" ) ? $scene["stoptime"] : '-1';
					$SceneSchedule = 1;
				}else{
					//no Data
				}
			}
			//pa( $scene );
			?>
			<div id="scheduleContainer">
				Use Schedule:
				<div class="sched-switch sched-val-<?php echo $SceneSchedule; ?>">
					<label class="switch">
					  <input class="device-toggle" name="USE_SCHED" value="1" type="checkbox" <?php echo ($SceneSchedule == 1) ? "checked" : ""; ?>>
					  <div class="slider round"></div>
					</label>
				</div>
				
				<div id="schedule" class="scheduleEnabled scheduleEnabled-<?php echo $SceneSchedule; ?>">
					<p><b>Days:</b></p>
					<p>
						<label for="sunday">Sunday 		<input type="checkbox" id="sunday" value="0" name="every" <?php echo strpos($days, "0") !== false ? " checked" : ""  ?>/></label><br />
						<label for="monday">Monday 		<input type="checkbox" id="monday" value="1" name="every" <?php echo strpos($days, "1") !== false ? " checked" : ""  ?>/></label><br /> 
						<label for="tuesday">Tuesday 	<input type="checkbox" id="tuesday" value="2" name="every" <?php echo strpos($days, "2") !== false ? " checked" : ""  ?>/></label><br /> 
						<label for="wednesday">Wednesday<input type="checkbox" id="wednesday" value="3" name="every" <?php echo strpos($days, "3") !== false ? " checked" : ""  ?>/></label><br /> 
						<label for="thursday">Thursday 	<input type="checkbox" id="thursday" value="4" name="every" <?php echo strpos($days, "4") !== false ? " checked" : ""  ?>/></label><br /> 
						<label for="friday">Friday 		<input type="checkbox" id="friday" value="5" name="every" <?php echo strpos($days, "5") !== false ? " checked" : ""  ?>/></label> <br />
						<label for="saturday">Saturday 	<input type="checkbox" id="saturday" value="6" name="every" <?php echo strpos($days, "6") !== false ? " checked" : ""  ?>/></label> 
						
					</p>
					<p><b>Start Time:</b></p>
					<div class="timeContainer">
					<select id="startHour" name="startHour">
						<option value="-1" <?php if($startTime == '-1' ){ echo ' selected'; }?>>No Start Time</option>
						<?php for($x = 0; $x < 24; $x ++){
							if( $startTime != '-1' && $startTime != 'sunrise' && $startTime != 'sunset'  ){
								echo '<option value="'.$x.'" '.( date('G', strtotime( $startTime ) ) == $x ? " selected" : "").'>'.date("g a",strtotime($x.":00")).'</option>';
							}else{
								echo '<option value="'.$x.'">'.date("g a",strtotime($x.":00")).'</option>';
							}
						}
						?>
						<option value="sunrise" <?php if($startTime == "sunrise"){ echo ' selected'; }?>>Sun Rise</option>
						<option value="sunset" <?php if($startTime == "sunset"){ echo ' selected'; }?>>Sun Set</option>
					</select>
					<select id="startMin" name="startMin" <?php if($startTime == "sunrise" || $startTime == "sunset" || $startTime == -1 ){ echo 'disabled="disabled"'; } ?>>
						<?php 
						for( $x = 0; $x < 60; $x++){
							if( $startTime != '-1' && $startTime != 'sunrise' && $startTime != 'sunset'  ){
								echo '<option value="'.str_pad($x,2,"0",STR_PAD_LEFT).'" '.( date('i', strtotime( $startTime ) ) == $x ? " selected" : "").'>'.str_pad($x,2,"0",STR_PAD_LEFT).'</option>';
							}else{
								echo '<option value="'.str_pad($x,2,"0",STR_PAD_LEFT).'">'.str_pad($x,2,"0",STR_PAD_LEFT).'</option>';
							}
						}
						?>
					</select>
					</div>
					<p><b>Stop Time:</b></p>

					<div class="timeContainer">
						
						<select id="stopHour" name="stoptHour">
						<option value="-1" <?php if($stopTime == '-1' ){ echo ' selected'; }?>>No Stop Time</option>
							<?php for($x = 0; $x < 24; $x ++){
								if( $stopTime != '-1' && $stopTime != 'sunrise' && $stopTime != 'sunset'  ){
									echo '<option value="'.$x.'" '.( date('G', strtotime( $stopTime ) ) == $x ? " selected" : "").'>'.date("g a",strtotime($x.":00")).'</option>';
								}else{
									echo '<option value="'.$x.'">'.date("g a",strtotime($x.":00")).'</option>';
								}
							}
							?>
							<option value="sunrise" <?php if($stopTime == "sunrise"){ echo ' selected'; }?>>Sun Rise</option>
							<option value="sunset" <?php if($stopTime == "sunset"){ echo ' selected'; }?>>Sun Set</option>
						</select>
						
						<select id="stopMin" name="stopMin" <?php if($stopTime == "sunrise" || $stopTime == "sunset" || $stopTime == -1 ){ echo 'disabled="disabled"'; } ?>>
							<?php 
							for( $x = 0; $x < 60; $x++){
								if( $stopTime != '-1' && $stopTime != 'sunrise' && $stopTime != 'sunset'  ){
									echo '<option value="'.str_pad($x,2,"0",STR_PAD_LEFT).'" '.( date('i' , strtotime( $stopTime )) == $x ? " selected" : "").'>'.str_pad($x,2,"0",STR_PAD_LEFT).'</option>';
								}else{
									echo '<option value="'.str_pad($x,2,"0",STR_PAD_LEFT).'">'.str_pad($x,2,"0",STR_PAD_LEFT).'</option>';
								}
							}
							?>
						</select>
					</div>
				
				</div>
			
			</div>
			<?php
		
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
					$toggled = 0;

					//check if roomID is in scene...
					if( in_array( $room['rid'], $devices ) ){
						$toggled = 1;
						renderRoom($room, 1);
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
		<button id="save2">Save</button>
		</div>				
		
		<?php
	}
	echo '</div>';
pageFooter();
?>
