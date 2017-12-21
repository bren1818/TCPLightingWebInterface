<?php
/*
 *
 * TCP Ligthing Web UI Info Script - By Brendon Irwin
 * 
 */

include "include.php";

if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
	error_reporting(E_ALL);
	$sel = $_POST['selected'];
	$name = $_POST['name'];
	$colourID = $_POST['color'];
	$remoteControl = $_POST['remote'];
	
	$devices = "";
	if( is_array($sel) ){
		for($d=0; $d < sizeof($sel); $d++){
			$devices.= '<device><did>'.$sel[$d].'</did></device>';
		}
		
		$CMD = "cmd=DeviceVirtualCreate&data=<gip><version>1</version><token>".TOKEN."</token><color>".$colourID."</color><name>".( $name != "" ? $name : "NEW Virtual Device" )."</name>".$devices. ( $remoteControl != ""  ? "<other><rcgroup>".$remoteControl."</rcgroup></other>" : "" )."</gip>";
		$result = getCurlReturn($CMD);
		$array = xmlToArray($result);
		
		$newDevice = $array['did'];
		
		if( $newDevice != ""){
			header("Location: info.php?did=".$newDevice);
		}
	}
	
	/*
        if (this.image != null && this.image.length() > 0) {
            dataString.append(String.format("<image>%s</image>", new Object[]{xmlEscape(this.image)}));
        }
        if (this.producttype != null) {
            dataString.append(String.format("<producttype>%s</producttype>", new Object[]{xmlEscape(this.producttype)}));
        }
	*/
	
	
	exit;
}


pageHeader("TCP Lighting Controller - Create Device");
?>

<?php
echo '<div class="container" style="padding: 20px; background-color: #fff;">';
?>
<script type="text/javascript">
	function SelectMoveRows(SS1,SS2){
		//function shamelessly borrowed from http://johnwbartlett.com/cf_tipsntricks/index.cfm?TopicID=86
		var SelID='';
		var SelText='';
		// Move rows from SS1 to SS2 from bottom to top
		for (i=SS1.options.length - 1; i>=0; i--){
			if (SS1.options[i].selected == true){
				SelID=SS1.options[i].value;
				SelText=SS1.options[i].text;
				var newRow = new Option(SelText,SelID);
				SS2.options[SS2.length]=newRow;
				SS1.options[i]=null;
			}
		}
	}
	$(function(){
		$('.btnAdd, .btnRemove').click(function(){		
			var available =  $(this).closest('.deviceList').find('select.available')[0];
			var selected = $(this).closest('.deviceList').find('select.selected')[0];

			if( $(this).hasClass('btnAdd') ){
				SelectMoveRows(available,  selected );
			}else{
				SelectMoveRows( selected, available );
			}
		});
		
		$('select[name="remote"]').change(function(){
			if( $(this).find('option:selected').val() != "" ){
				$('#remote').show();
			}else{
				$('#remote').hdie();
			}
			
		});
		
		$('#post').click(function(event){
			
			var count = $('#selected option').length;
			console.log( count );
			if( count > 1){
				console.log("Submit");
				//select all the selected 
				$('#selected option').prop('selected', true);
				$('#createDevice').submit();
			}else{
				event.preventDefault();
				window.alert("You need to select 2 or more devices");
			}
		});
		
	});		
</script>
<h1>Create Virtual Device</h1>

	<p>Add two or more devices to the selected list</p>
	<div class="deviceList">
		<form id="createDevice" method="post" action="createDevice.php">
		
		<?php
			$CMD = "cmd=RoomGetList&data=<gip><version>1</version><token>".TOKEN."</token></gip>";
			$result = getCurlReturn($CMD);
			$array = xmlToArray($result);
			$ROOM_COLOURS = array();
			//pa($array);
			foreach( $array['room'] as $room ){
				$ROOM_COLOURS[ $room["colorid"] ]["name"] =  $room["name"];
				$ROOM_COLOURS[ $room["colorid"] ]["hex"] =  $room["color"];
				$ROOM_COLOURS[ $room["colorid"] ]["colorid"] =  $room["colorid"];
				$ROOM_COLOURS[ $room["colorid"] ]["image"] =  $room["img"];
				$ROOM_COLOURS[ $room["colorid"] ]["room"] =  $room["rid"];
			}
		?>
			<label for="name">New Device Name:<br /> <input name="name" id="name" value="" placeholder="NEW_VIRTUAL_DEVICE" /></label><br />
	
			<label for="color">Color:<br /> 
			<select name="color">
				<option value="">Not Specified</option>
				<option value="0">Black <?php echo isset( $ROOM_COLOURS[0] ) ?  ' ('.$ROOM_COLOURS[0]['name'].')' : ''; ?></option>
				<option value="1">Green <?php echo isset( $ROOM_COLOURS[1] ) ?  ' ('.$ROOM_COLOURS[1]['name'].')' : ''; ?></option>
				<option value="2">Dark Blue <?php echo isset( $ROOM_COLOURS[2] ) ?  ' ('.$ROOM_COLOURS[2]['name'].')' : ''; ?></option>
				<option value="3">Red <?php echo isset( $ROOM_COLOURS[3] ) ?  ' ('.$ROOM_COLOURS[3]['name'].')' : ''; ?></option>
				<option value="4">Yellow <?php echo isset( $ROOM_COLOURS[4] ) ?  ' ('.$ROOM_COLOURS[4]['name'].')' : ''; ?></option>
				<option value="5">Purple <?php echo isset( $ROOM_COLOURS[5] ) ?  ' ('.$ROOM_COLOURS[5]['name'].')' : ''; ?></option>
				<option value="6">Orange <?php echo isset( $ROOM_COLOURS[6] ) ?  ' ('.$ROOM_COLOURS[6]['name'].')' : ''; ?></option>
				<option value="7">Light Blue <?php echo isset( $ROOM_COLOURS[7] ) ?  ' ('.$ROOM_COLOURS[7]['name'].')' : ''; ?></option>
				<option value="8">Pink <?php echo isset( $ROOM_COLOURS[8] ) ?  ' ('.$ROOM_COLOURS[8]['name'].')' : ''; ?></option>
			</select> 
			</label><br />
		
			<label for="remote">Assign Remote Control Button: <br />
				<select name="remote">
					<option value="">Not Specified / No remote</option>
					<option value="1">Button 1</option>
					<option value="2">Button 2</option>
					<option value="3">Button 3</option>
					<option value="4">Button 4</option>
				</select><br />
				<img id="remote" style="display: none;" src="css/images/remote.png" />
			</label><br />
			
		<table>
			<tr>
				<td>Available</td><td>&nbsp;</td><td>Selected</td>
			</tr>
			<tr>
				<td>
					<?php
						$devices = getDevices();
						//pa($devices);
						
						
						
					?>
					<select size="9" class="available" name="DEVICE_AVAILABLE" multiple>
						<?php
							foreach($devices as $device){
								echo '<option value="'.$device['did'].'">'.$device['prodtype'].' - '.$device['name'].' ('.$ROOM_COLOURS[ $device["colorid"] ]["name"].')</option>';
							}
						?>
					</select>
				</td>
				<td>
					<input type="Button" value="Add >>" class="btnAdd"><br />
					<br />
					<input type="Button" value="<< Remove" class="btnRemove">
				</td>
				<td>
					
						<select id="selected" size="9" class="selected" name="selected[]" multiple>
						</select>
						
				</td>
			</tr>
		</table>
		
	</div>

		<input name="submit" id="post" type="submit" value="Create Device" />
	</form>
</form>


<?php
echo '</div>';
pageFooter();
?>
