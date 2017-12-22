<?php
/*
 *
 * TCP Ligthing Web UI Info Script - By Brendon Irwin
 * 
 */

include "include.php";

$CMD = "cmd=RoomGetList&data=<gip><version>1</version><token>".TOKEN."</token></gip>";
$result = getCurlReturn($CMD);
$array = xmlToArray($result);
$ROOM_COLOURS = array();

/*
pa( $array );
Array
(
    [version] => 1
    [rc] => 200
    [room] => Array
        (
            [rid] => 0
            [name] => Unknown Room
            [desc] => Array
                (
                )

            [known] => 1
            [type] => 0
            [color] => 000000
            [colorid] => 0
            [img] => css/images/black.png
            [power] => 0
            [poweravg] => 0
            [energy] => 0
        )

)*/


if ( isset( $array["room"]["rid"] ) ){ $array["room"] = array(   $array["room"]); }


//if( sizeof( $array['room'] ) > 1 ){

foreach( $array['room'] as $room ){
	$ROOM_COLOURS[ $room["colorid"] ]["name"] =  $room["name"];
	$ROOM_COLOURS[ $room["colorid"] ]["hex"] =  $room["color"];
	$ROOM_COLOURS[ $room["colorid"] ]["colorid"] =  $room["colorid"];
	$ROOM_COLOURS[ $room["colorid"] ]["image"] =  $room["img"];
	$ROOM_COLOURS[ $room["colorid"] ]["room"] =  $room["rid"];
}

//}

if (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET'){
		if(isset($_GET['delete']) && $_GET['delete'] != "" ){
		/*
		 if (this.full != null && this.full.length() > 0 && this.full.equalsIgnoreCase("1")) {
            fullDelete = "<full>1</full>";
        }
        dataString = String.format("<gip><version>1</version><token>%s</token><did>%s</did>%s</gip>", new Object[]{xmlEscape(this.token), xmlEscape(this.did), fullDelete});
        this.postData.add(new BasicNameValuePair("cmd", "DeviceDelete"));
        this.postData.add(new BasicNameValuePair("data", dataString));
		*/
		
		$did = $_REQUEST['delete'];
		$fullDelete =  $_REQUEST['fullDelete'];
		
		$CMD = "cmd=DeviceDelete&data=<gip><version>1</version><token>".TOKEN."</token><did>".$did."</did>".($fullDelete == 1 ? '<full>1</full>' : '')."</gip>";
		$result = getCurlReturn($CMD);
		$array = xmlToArray($result);
		
		
		
		echo json_encode( array("delete" => $did, "fullDelete" => $fullDelete, "Result" => $array) );
		
	}
}
	
	
if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
	
	if(isset($_POST['did']) && $_POST['did'] != "" ){
		$did = $_POST['did'];
		$name = $_POST['name'];
		$color = $_POST['color'];
		$remote = $_POST['remote'];
		$imdata = "";
		
		if( isset($_FILES["image"]) && $_FILES["image"]["tmp_name"] != "" ){ 	
			$imageFileType = pathinfo( basename($_FILES["image"]["name"]) ,PATHINFO_EXTENSION);
			$check = getimagesize($_FILES["image"]["tmp_name"]);
			if($check !== false) {
				if ($_FILES["image"]["size"] > 500000) {
					echo "Sorry, your file is too large.";
				}else{
					if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
						echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
					}else{
						//needs to be 100 x 100?
						$imdata = base64_encode( file_get_contents($_FILES["image"]["tmp_name"] ) );      
						//echo $imdata;
						echo '<p><img src="data:image/png;base64,'.$imdata.'" /></p>';
					//	$imdata = "data:image/png;base64,".$imdata;
					//	$imdata = htmlentities($imdata);
					}
				}
			}
		}		
		
		
		
		$CMD = "cmd=DeviceSetInfo&data=<gip><version>1</version><token>".TOKEN."</token><did>".$did."</did><name>".$name."</name>".($color != "" ? "<color>".$color."</color>" : "").($imdata != "" ? "<image>".$imdata."</image>" : "").( $remote != ""  ? "<other><rcgroup>".$remote."</rcgroup></other>" : "" )."</gip>";
		
		//echo htmlentities($CMD);
		$result = getCurlReturn($CMD);
		//pa( $result );
		$array = xmlToArray($result);
		//pa( $array );
		if( $array['rc'] == 200){
			//echo "Updated Successfully";
		}
		
		header("Location: info.php?did=".$did);
	}
	
	if(isset($_POST['rid']) && $_POST['rid'] != "" ){
		$rid = $_POST['rid'];
		$name =  $_POST['name'];
		$color = $_POST['color'];
		
		
		$CMD = "cmd=RoomSetInfo&data=<gip><version>1</version><token>".TOKEN."</token><rid>".$rid."</rid><name>".$name."</name>".($color != "" ? "<colorid>".$color."</colorid>" : "")."</gip>";
		$result = getCurlReturn($CMD);
		$array = xmlToArray($result);
	/*
        if (this.type != null) {
            dataString.append(String.format("<type>%s</type>", new Object[]{xmlEscape(this.type)}));
        }
	*/
		
	
		header("Location: info.php?rid=".$rid);
	
		
	}
	

	
	
}


if( isset($_REQUEST['did']) && $_REQUEST['did'] != "" ){
	$did = $_REQUEST['did'];
	pageHeader("TCP Lighting Controller - Device Controller - Device: ".$did);
	?>

	<?php
	echo '<div class="roomContainer" style="background-color: #fff; padding: 20px; border: 1px solid #000;">';
	//echo '<h2>Device Info</h2>';
	//echo '<p><b>Device ID:'.$did.'</b></p>';
	
	$CMD = "cmd=DeviceGetInfo&data=<gip><version>1</version><token>".TOKEN."</token><did>".$did."</did><fields>name,power,product,class,image,control,realtype,other,status</fields></gip>";
	
	$result = getCurlReturn($CMD);
	$array = xmlToArray($result);

	?>
	<script>
		$(function(){
			$('#deleteDevice').click(function(){
				var did = $(this).attr('data-deviceID');
				var c = confirm("Are you sure you wish to delete the device?");
				if(c){
					//var fd = confirm("Do you want to 'Full' Delete? - No puts device into detection mode and will add it to first room");
					//if(fd) { fd = 1; }else{ fd = 0; }
					var fd = 0;
					
					$.get( "info.php?delete=" + did + "&fullDelete=" + fd, function( data ) {
						console.log( data );
						window.location = "index.php";
					});
				}
			});
		});
	</script>
	<form method="post" action="info.php" enctype="multipart/form-data">
		<fieldset>
			<legend>Update Device</legend>
		<?php
		if( isset( $array["image"] ) && ! is_array( $array["image"]) ){
			echo '<p><img src="data:image/png;base64,'.$array["image"].'" /></p>';
		}
		?>
		<label for="name">Name: <input name="name" id="name" value="<?php echo $array["name"]; ?>" /></label><br />
		<label for="did">Device ID: <?php echo $did; ?></label><br />
		<label for="image">Image: <input type="file" name="image" id="image"></label><br />
		<label for="color">Color: 
			<select name="color">
				<option value="">Pick Colour</option>
				<option value="0" <?php echo ($array["colorid"] == 0 ? "selected" : ""); ?>>Black <?php echo isset( $ROOM_COLOURS[0] ) ?  ' ('.$ROOM_COLOURS[0]['name'].')' : ''; ?></option>
				<option value="1" <?php echo ($array["colorid"] == 1 ? "selected" : ""); ?>>Green <?php echo isset( $ROOM_COLOURS[1] ) ?  ' ('.$ROOM_COLOURS[1]['name'].')' : ''; ?></option>
				<option value="2" <?php echo ($array["colorid"] == 2 ? "selected" : ""); ?>>Dark Blue <?php echo isset( $ROOM_COLOURS[2] ) ?  ' ('.$ROOM_COLOURS[2]['name'].')' : ''; ?></option>
				<option value="3" <?php echo ($array["colorid"] == 3 ? "selected" : ""); ?>>Red <?php echo isset( $ROOM_COLOURS[3] ) ?  ' ('.$ROOM_COLOURS[3]['name'].')' : ''; ?></option>
				<option value="4" <?php echo ($array["colorid"] == 4 ? "selected" : ""); ?>>Yellow <?php echo isset( $ROOM_COLOURS[4] ) ?  ' ('.$ROOM_COLOURS[4]['name'].')' : ''; ?></option>
				<option value="5" <?php echo ($array["colorid"] == 5 ? "selected" : ""); ?>>Purple <?php echo isset( $ROOM_COLOURS[5] ) ?  ' ('.$ROOM_COLOURS[5]['name'].')' : ''; ?></option>
				<option value="6" <?php echo ($array["colorid"] == 6 ? "selected" : ""); ?>>Orange <?php echo isset( $ROOM_COLOURS[6] ) ?  ' ('.$ROOM_COLOURS[6]['name'].')' : ''; ?></option>
				<option value="7" <?php echo ($array["colorid"] == 7 ? "selected" : ""); ?>>Light Blue <?php echo isset( $ROOM_COLOURS[7] ) ?  ' ('.$ROOM_COLOURS[7]['name'].')' : ''; ?></option>
				<option value="8" <?php echo ($array["colorid"] == 8 ? "selected" : ""); ?>>Pink <?php echo isset( $ROOM_COLOURS[8] ) ?  ' ('.$ROOM_COLOURS[8]['name'].')' : ''; ?></option>
			</select> 
		</label><br />
		<?php
		$color = $array["colorid"];
		if( isset( $ROOM_COLOURS[$color] ) ){
		?>
			<label for="room">Room: 
			<?php echo '<a href="info.php?rid='.$ROOM_COLOURS[$color]['room'].'">'.$ROOM_COLOURS[$color]['name'].'</a>'; ?>
			</label>
		<?php
		}
		?>
		<br />
		<label for="remote">Assigned Remote Control Button: <br />
		<?php
			if( isset($array['other']['rcgroup']) &&  $array['other']['rcgroup'] != "" ){
				$rcID = $array['other']['rcgroup'];
			}else{
				$rcID = "";
			}
		?>
			<select name="remote">
				<option value="">Not Specified / No remote</option>
				<option value="1" <?php echo ($rcID == 1 ? " Selected" : ""  ); ?>>Button 1</option>
				<option value="2" <?php echo ($rcID == 2 ? " Selected" : ""  ); ?>>Button 2</option>
				<option value="3" <?php echo ($rcID == 3 ? " Selected" : ""  ); ?>>Button 3</option>
				<option value="4" <?php echo ($rcID == 4 ? " Selected" : ""  ); ?>>Button 4</option>
			</select><br />
			<img id="remote" style="display: none;" src="<?php echo LOCAL_URL; ?>/css/images/remote.png" />
		</label><br />
		
		<input type="hidden" name="did" value="<?php echo $did; ?>" /><br />
		<input type="submit" value="SAVE" />
		</fieldset>
	</form>
	<?php
	echo '<div class="devices">';
		echo '<div class="room-devices">';
			$device = $array;
			$device['did'] = $did;
			echo '<div class="'.( (isset($device['offline']) && $device['offline'] == 1) ? 'unplugged' : 'plugged' ).' device '.($device['state'] == 1 ? 'light-on' : 'light-off' ).' '.($device['prodtype'] == 'Light Fixture' ? 'light-fixture' : '' ).'" data-device-id="'.$device['did'].'">'; //power > 0 then enabled 
				//level = brightness
				//state = on or off
				echo '<p style="display: block !important">'.$device['name'].'</p>';
				echo '<button data-device-id="'.$device['did'].'" class="onOffDeviceToggleButton buttonOn">On</button> | <button data-device-id="'.$device['did'].'" class="onOffDeviceToggleButton buttonOff">Off</button>';
				echo '<div class="clear"></div>';
				echo '<p>Brightness:</p>';
				echo '<div class="device-slider" data-value="'.(isset($device['level']) ? $device['level'] : 100).'" data-device-id="'. $device["did"].'"></div>';
			echo '</div>';
		echo '</div>';
		
		?>
			<button id="deleteDevice" data-deviceID="<?php echo $device['did'];  ?>">Delete Device</button>
		<?php
	echo '</div>';
	?>
	</div>
	<div id="arrayDump" class="roomContainer" style="padding: 20px;">
	<?php
	pa( $array );
	?>
	</div>
	<?php
	pageFooter();
}
	
if( isset($_REQUEST['rid']) && $_REQUEST['rid'] != "" ){	
	$rid = $_REQUEST['rid'];
	pageHeader("TCP Lighting Controller - Room Controller - Room: ".$rid);
	
	$CMD = "cmd=RoomGetInfoAll&data=<gip><version>1</version><token>".TOKEN."</token><rid>".$rid."</rid><fields>name,power,product,class,image,imageurl,control,other</fields></gip>";
	
	$result = getCurlReturn($CMD);
	$room = xmlToArray($result);
	
	
	echo '<div class="roomContainer" style="background-color: #fff; padding: 20px; border: 1px solid #000; margin-top: 20px;">';
	?>
	<form method="post" action="info.php" enctype="multipart/form-data">
		<fieldset>
			<legend>Update Room</legend>
			<?php	

			echo '<h3>&lsquo;'.$room["name"].'&rsquo; - Room ID: '.$rid.'</h3>';
			?>
			<input type="hidden" name="rid" value="<?php echo $rid; ?>" />
			
			<p><label for="name">Name: <input name="name" id="name" value="<?php echo $room["name"]; ?>" /></label></p>
			
			<p><label for="color">Color: 
			<select name="color">
				<option value="0" <?php echo ($room["colorid"] == 0 ? "selected" : ""); ?>>Black <?php echo isset( $ROOM_COLOURS[0] ) ?  ' ('.$ROOM_COLOURS[0]['name'].')' : ''; ?></option>
				<option value="1" <?php echo ($room["colorid"] == 1 ? "selected" : ""); ?>>Green <?php echo isset( $ROOM_COLOURS[1] ) ?  ' ('.$ROOM_COLOURS[1]['name'].')' : ''; ?></option>
				<option value="2" <?php echo ($room["colorid"] == 2 ? "selected" : ""); ?>>Dark Blue <?php echo isset( $ROOM_COLOURS[2] ) ?  ' ('.$ROOM_COLOURS[2]['name'].')' : ''; ?></option>
				<option value="3" <?php echo ($room["colorid"] == 3 ? "selected" : ""); ?>>Red <?php echo isset( $ROOM_COLOURS[3] ) ?  ' ('.$ROOM_COLOURS[3]['name'].')' : ''; ?></option>
				<option value="4" <?php echo ($room["colorid"] == 4 ? "selected" : ""); ?>>Yellow <?php echo isset( $ROOM_COLOURS[4] ) ?  ' ('.$ROOM_COLOURS[4]['name'].')' : ''; ?></option>
				<option value="5" <?php echo ($room["colorid"] == 5 ? "selected" : ""); ?>>Purple <?php echo isset( $ROOM_COLOURS[5] ) ?  ' ('.$ROOM_COLOURS[5]['name'].')' : ''; ?></option>
				<option value="6" <?php echo ($room["colorid"] == 6 ? "selected" : ""); ?>>Orange <?php echo isset( $ROOM_COLOURS[6] ) ?  ' ('.$ROOM_COLOURS[6]['name'].')' : ''; ?></option>
				<option value="7" <?php echo ($room["colorid"] == 7 ? "selected" : ""); ?>>Light Blue <?php echo isset( $ROOM_COLOURS[7] ) ?  ' ('.$ROOM_COLOURS[7]['name'].')' : ''; ?></option>
				<option value="8" <?php echo ($room["colorid"] == 8 ? "selected" : ""); ?>>Pink <?php echo isset( $ROOM_COLOURS[8] ) ?  ' ('.$ROOM_COLOURS[8]['name'].')' : ''; ?></option>
			</select> 
			<?php
				if( isset($ROOM_COLOURS[ $room["colorid"] ]) ){
					echo '<div style="display: inline-block; background-color: #'.$ROOM_COLOURS[ $room["colorid"] ]["hex"].'; padding: 5px;">'.$ROOM_COLOURS[ $room["colorid"] ]["hex"].'</div>'; 
				}
			?>
		</label></p>
		
	
		<input type="submit" value="UPDATE" />
		</fieldset>
		</form>
			<?php
			echo '<div class="roomContainer" data-room-id="'. $rid.'" style="margin-top:20px;">';
			$DEVICES = array();
			$deviceCount = 0;	
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

							echo '<p style="display: block !important;">'.$device['name'].' <a href="info.php?did='.$device['did'].'"><img src="css/images/info.png"/></a></p>';

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
				//pa( $room );
			}
		
			echo '<div class="room-controls">';
				echo 'Room Brightness: <div class="room-slider" data-value="'.($roomBrightness/$roomDevices).'" data-room-id="'. $room["rid"].'"></div>';
				echo 'Room <button data-room-id="'. $room["rid"].'" class="onOffToggleButton buttonOn">On</button> | <button data-room-id="'. $room["rid"].'" class="onOffToggleButton buttonOff">Off</button>';
			echo '</div>';
		echo '</div>';
	echo '</div>';

	
	?>
	<div id="arrayDump" class="roomContainer" style="padding: 20px;">
	<?php
	pa( $room );
	?>
	</div>
	<?php
	
	pageFooter();
}
?>