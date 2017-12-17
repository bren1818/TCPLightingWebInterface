<?php
include "include.php";
pageHeader("IFTTT Query Builder");
?>
<style>
p{margin: 10px 0; }
</style>
<div class="container" style="padding: 20px; background-color: #fff; border: 1px solid #000;">
<p>Use this to assist with creation of the IFTTT web-hooks quickly</p>
<p>I suggest setting up a Google Assistant Hook which then calls a webhook with the API string. Follow the <a target="_blank" href="https://github.com/bren1818/TCPLightingWebInterface/wiki/IFTTT-Integration">excellent guide</a> written by <a target="_blank" href="https://github.com/sktaylortrash">@sktaylortrash</a> in the wiki and feel free to ask a question in the forum</p>

<p>It is recommended that use SSL if you're able. This will work on non-standard ports, (ports other than 80/443) but you will need to setup port-forwarding and setup your webserver to listen on those ports. <a target="_blank" href="https://github.com/sktaylortrash">@sktaylortrash</a> has <a target="_blank" href="https://github.com/bren1818/TCPLightingWebInterface/wiki/Installation">documented</a> how to set everything up on both a <a target="_blank" href="https://github.com/bren1818/TCPLightingWebInterface/wiki/Installation-for-Apache-on-Raspbian">Raspberry Pi / Linux Environment</a>, or using <a target="_blanks" href="https://github.com/bren1818/TCPLightingWebInterface/wiki/Installation-for-Apache-on-XAMPP-Windows">XAMPP on Windows</a>.</p> 

<?php 
if( ALLOW_EXTERNAL_API_ACCESS == 1 ){
?>

<script>
var allowExternalAPI = "<?php echo ALLOW_EXTERNAL_API_ACCESS; ?>";
var externalAddr = "<?php echo EXTERNAL_DDNS_URL; ?>";
var externalPort = "<?php echo EXTERNAL_PORT; ?>";
var externalPass = "<?php echo EXTERNAL_API_PASSWORD; ?>";
var requireExtPass = "<?php echo REQUIRE_EXTERNAL_API_PASSWORD; ?>";

$(function(){
	$('#sceneLights').hide();
	$('#roomLights').hide();
	$('#builder').change(function(){
		console.log( $(this).val() );
		var id = $(this).find('option:selected').attr('data-device-id');
		var type = $(this).find('option:selected').attr('data-device-type');
		var roomName = "";
		$('#appletImage').html('');
		
		if( type == "room" || type == "light"){
			$('#sceneLights').hide();
			$('#roomLights').show();
			
			roomName = $(this).find('option:selected').attr('data-room-name');
			console.log("Type: " + type + ", id: " + id + ", RN: " + roomName );
			$('#appletImage').html('<img src="css/images/IFTTTAppletPhrase.png"/>');
			
			if( type == "room"  ){
				$('#command_on_1').html("TCP " +  roomName + " lights ON");
				$('#command_on_2').html(roomName + " TCP lights ON");
				$('#command_on_3').html(roomName + " lights on");
				$('#command_on_response').html("Turning " + roomName + " lights ON");
				$('#command_on_url').html( externalAddr + ( (externalPort != 80 || externalPort != 443) ?  ':' + externalPort : '' ) + '/api.php?fx=toggle&type=room&uid=' + id + '&val=1' + ( requireExtPass == 1 ? '&password=' + externalPass : '')  );
				
				$('#command_off_1').html("TCP " +  roomName + " lights off");
				$('#command_off_2').html(roomName + " TCP Lights off");
				$('#command_off_3').html(roomName + " lights off");
				$('#command_off_response').html("Turning off " + roomName + " lights");
				$('#command_off_url').html( externalAddr + ( (externalPort != 80 || externalPort != 443) ?  ':' + externalPort : '' ) + '/api.php?fx=toggle&type=room&uid=' + id + '&val=0' + ( requireExtPass == 1 ? '&password=' + externalPass : '') );
				
				$('#command_dim_1').html("TCP " +  roomName + " lights to # % brightness");
				$('#command_dim_2').html(roomName + " TCP light brightness # %");
				$('#command_dim_3').html(roomName + " brightness # %");
				$('#command_dim_response').html("Adjusting " + roomName + " lights");
				$('#command_dim_url').html( externalAddr + ( (externalPort != 80 || externalPort != 443) ?  ':' + externalPort : '' ) + '/api.php?fx=dimby&type=room&uid=' + id + '&val={{NumberField}}' + ( requireExtPass == 1 ? '&password=' + externalPass : '') );
				
				$('#command_brighten_1').html("Brighten TCP " +  roomName + " lights by # %");
				$('#command_brighten_2').html(roomName + "lights increase brightness by # %");
				$('#command_brighten_3').html("Increase " + roomName + " brightness # %");
				$('#command_brighten_response').html("Brightening " + roomName + " light");
				$('#command_brighten_url').html( externalAddr + ( (externalPort != 80 || externalPort != 443) ?  ':' + externalPort : '' ) + '/api.php?fx=brightenby&type=room&uid=' + id + '&val={{NumberField}}' + ( requireExtPass == 1 ? '&password=' + externalPass : '') );
				
				$('#command_dimby_1').html("Dim TCP " +  roomName + " light by # %");
				$('#command_dimby_2').html(roomName + " lights decrease brightness by # %");
				$('#command_dimby_3').html("Decrease " + roomName + " brightness by # %");
				$('#command_dimby_response').html("Dimming " + roomName + " lights");
				$('#command_dimby_url').html( externalAddr + ( (externalPort != 80 || externalPort != 443) ?  ':' + externalPort : '' ) + '/api.php?fx=dimby&type=room&uid=' + id + '&val={{NumberField}}' + ( requireExtPass == 1 ? '&password=' + externalPass : '') );
			}
			
			if( type == "light" ){
				$('#command_on_1').html("TCP " +  roomName + " light ON");
				$('#command_on_2').html(roomName + " TCP light ON");
				$('#command_on_3').html(roomName + " light on");
				$('#command_on_response').html("Turning " + roomName + " light ON");
				$('#command_on_url').html( externalAddr + ( (externalPort != 80 || externalPort != 443) ?  ':' + externalPort : '' ) + '/api.php?fx=toggle&type=room&uid=' + id + '&val=1' + ( requireExtPass == 1 ? '&password=' + externalPass : '')  );
				
				$('#command_off_1').html("TCP " +  roomName + " light off");
				$('#command_off_2').html(roomName + " TCP Light off");
				$('#command_off_3').html(roomName + " light off");
				$('#command_off_response').html("Turning off " + roomName + " light");
				$('#command_off_url').html( externalAddr + ( (externalPort != 80 || externalPort != 443) ?  ':' + externalPort : '' ) + '/api.php?fx=toggle&type=room&uid=' + id + '&val=0' + ( requireExtPass == 1 ? '&password=' + externalPass : '') );
				
				$('#command_dim_1').html("TCP " +  roomName + " light to # % brightness");
				$('#command_dim_2').html(roomName + " TCP light brightness # %");
				$('#command_dim_3').html(roomName + " light brightness # %");
				$('#command_dim_response').html("Adjusting " + roomName + " brightness");
				$('#command_dim_url').html( externalAddr + ( (externalPort != 80 || externalPort != 443) ?  ':' + externalPort : '' ) + '/api.php?fx=dimby&type=device&uid=' + id + '&val={{NumberField}}' + ( requireExtPass == 1 ? '&password=' + externalPass : '') );
				
				
				$('#command_brighten_1').html("Brighten TCP" +  roomName + " light by # %");
				$('#command_brighten_2').html(roomName + " light brightness increase by # %");
				$('#command_brighten_3').html("increase " + roomName + " light brightness # %");
				$('#command_brighten_response').html("Brightening " + roomName + " light");
				$('#command_brighten_url').html( externalAddr + ( (externalPort != 80 || externalPort != 443) ?  ':' + externalPort : '' ) + '/api.php?fx=brightenby&type=device&uid=' + id + '&val={{NumberField}}' + ( requireExtPass == 1 ? '&password=' + externalPass : '') );
				
				$('#command_dimby_1').html("Dim TCP " +  roomName + " light by # %");
				$('#command_dimby_2').html("Darken " + roomName + " light by # %");
				$('#command_dimby_3').html("Decrease " + roomName + " brightness by # %");
				$('#command_dimby_response').html("Dimming " + roomName + " lights");
				$('#command_dimby_url').html( externalAddr + ( (externalPort != 80 || externalPort != 443) ?  ':' + externalPort : '' ) + '/api.php?fx=dimby&type=device&uid=' + id + '&val={{NumberField}}' + ( requireExtPass == 1 ? '&password=' + externalPass : '') );
			}
			
			
			
		}
		
		if( type == "scene" ){
			$('#sceneLights').show();
			$('#roomLights').hide();
			var sceneName = $(this).find('option:selected').attr('data-device-name');
			console.log("Type: " + type + ", id: " + id + ", scene: " + sceneName );
			$('#appletImage').html('<img src="css/images/IFTTTAppletPhrase.png"/>');
			
			$('#scene_command_on_1').html("Activate " + sceneName );
			$('#scene_command_on_2').html("Turn " + sceneName + " scene ON");
			$('#scene_command_on_3').html(sceneName + " scene on");
			$('#scene_command_on_response').html("Turning " + sceneName + " devices ON");
			
			$('#scene_command_on_url').html( externalAddr + ( externalPort != 80 ?  ':' + externalPort : '' ) + '/api.php?fx=scene&type=on&uid=' + id + '&val=1' + ( requireExtPass == 1 ? '&password=' + externalPass : '')  );
			
			$('#scene_command_off_1').html("Deactivate " +  sceneName);
			$('#scene_command_off_2').html("Turn " + sceneName + " scene off");
			$('#scene_command_off_3').html(sceneName + " off");
			$('#scene_command_off_response').html("Turning " + sceneName + " devices off");
			
			$('#scene_command_off_url').html( externalAddr + ( externalPort != 80 ?  ':' + externalPort : '' ) + '/api.php?fx=scene&type=off&uid=' + id + '&val=0' + ( requireExtPass == 1 ? '&password=' + externalPass : '') );
			
			$('#command_run_1').html("Run " +  sceneName + " scene");
			$('#command_run_2').html("Enable " + sceneName + " scene");
			$('#command_run_3').html(sceneName + "scene on");
			$('#command_run_response').html("Running " + sceneName + " scene");
			
			$('#command_run_url').html( externalAddr + ( externalPort != 80 ?  ':' + externalPort : '' ) + '/api.php?fx=scene&type=run&uid=' + id + '&val={{NumberField}}' + ( requireExtPass == 1 ? '&password=' + externalPass : '') );
			
		}

	});
});
</script>

<p>
<select id="builder">
	<option>Select a Device | Room | Scene</option>
<?php
//Get State of System Data
	$CMD = "cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>RoomGetCarousel</gcmd><gdata><gip><version>1</version><token>".TOKEN."</token><fields>name,image,imageurl,control,power,product,class,realtype,status</fields></gip></gdata></gwrcmd></gwrcmds>&fmt=xml";
	$result = getCurlReturn($CMD);
	$array = xmlToArray($result);
	if( isset( $array["gwrcmd"]["gdata"]["gip"]["room"] ) ){
		$DATA = $array["gwrcmd"]["gdata"]["gip"]["room"];
	}
	
	if( sizeof($DATA) > 0 ){
		if ( isset( $DATA["rid"] ) ){ $DATA = array( $DATA ); }
		echo '<optgroup label="Rooms">';
		foreach($DATA as $room){
			echo '<option data-device-type="room" data-device-id="'.$room["rid"].'" data-room-name="'.$room["name"].'">'    .  $room["name"] .    '</option>';
			if( ! is_array($room["device"]) ){
				
			}else{
				$device = (array)$room["device"];
				if( isset($device["did"]) ){
					$DEVICES[] = "<option data-device-type='light' data-device-id='".$room["device"]["did"]."' data-room-name='".$room["name"]."'>".  $room["device"]["name"] ."</option>";
				}else{	
					for( $x = 0; $x < sizeof($device); $x++ ){
						if( isset($device[$x]) && is_array($device[$x]) && ! empty($device[$x]) ){
							$DEVICES[] = '<option data-device-type="light" data-device-id="'.$device[$x]["did"].'" data-room-name="'.$room["name"].'">'. $device[$x]["name"] ."</option>";
						}
					}
				}
			}
		}
		echo '</optgroup>';
	}
	
	echo '<optgroup label="Devices">';
	foreach($DEVICES as $device){
		echo $device;
	}
	echo '</optgroup>';
	
	$CMD = "cmd=SceneGetListDetails&data=<gip><version>1</version><token>".TOKEN."</token><bigicon>1</bigicon></gip>";
	$result = getCurlReturn($CMD);
	$array = xmlToArray($result);
	$scenes = $array["scene"];
	if( is_array($scenes) ){
		echo '<optgroup label="Scenes">';
		for($x = 0; $x < sizeof($scenes); $x++){
			echo '<option  data-device-type="scene" data-device-id="'.$scenes[$x]["sid"] .'" data-device-name="'.$scenes[$x]["name"] .'">'.$scenes[$x]["name"] . " - " . $scenes[$x]["sid"] . "</option>";
		}
		echo '</optgroup>';
	}
	
?>


</select>
</p>
<br />
<style>
#instructions p{ margin: 10px 0; }
#select{ margin-bottom: 10px; }
.response{ font-weight: bold; font-style: italic; font-size: 16px; }

</style>
<div id="instructions">
	<div id="appletImage" style="float: left; margin-right: 20px; overflow: hidden;">
	
    </div>
    <div class="fields">
    	<div id="roomLights">
            <h1>On Commands</h1>
            <p>What do you want to say: <span class="response" id="command_on_1"></span></p>
            <p>Whats another way to say it? (Optional): <span class="response" id="command_on_2"></span></p>
            <p>And another way?(optional): <span class="response" id="command_on_3"></span></p>
            <p>What do you want the Assistant to say in Response: <span class="response" id="command_on_response"></span></p>
            <p>URL: <span class="response" id="command_on_url"></span></p>
            <p>Method: Get</p>
            
            <h1>Off Commands</h1>
            <p>What do you want to say: <span class="response" id="command_off_1"></span></p>
            <p>Whats another way to say it? (Optional): <span class="response" id="command_off_2"></span></p>
            <p>And another way?(optional): <span class="response" id="command_off_3"></span></p>
            <p>What do you want the Assistant to say in Response: <span class="response" id="command_off_response"></span></p>
            <p>URL: <span class="response" id="command_off_url"></span></p>
            <p>Method: Get</p>
            
            <h1>Dim To Command (Say a phrase with a number)</h1>
            <p>What do you want to say: <span class="response" id="command_dim_1"></span></p>
            <p>Whats another way to say it? (Optional): <span class="response" id="command_dim_2"></span></p>
            <p>And another way?(optional): <span class="response" id="command_dim_3"></span></p>
            <p>What do you want the Assistant to say in Response: <span class="response" id="command_dim_response"></span></p>
            <p>URL: <span class="response" id="command_dim_url"></span></p>
            <p>Method: Get</p>
			
			<h1>Brighten By (Say a phrase with a number)</h1>
            <p>What do you want to say: <span class="response" id="command_brighten_1"></span></p>
            <p>Whats another way to say it? (Optional): <span class="response" id="command_brighten_2"></span></p>
            <p>And another way?(optional): <span class="response" id="command_brighten_3"></span></p>
            <p>What do you want the Assistant to say in Response: <span class="response" id="command_brighten_response"></span></p>
            <p>URL: <span class="response" id="command_brighten_url"></span></p>
            <p>Method: Get</p>
			
			<h1>Dim By (Darken By) (Say a phrase with a number)</h1>
            <p>What do you want to say: <span class="response" id="command_dimby_1"></span></p>
            <p>Whats another way to say it? (Optional): <span class="response" id="command_dimby_2"></span></p>
            <p>And another way?(optional): <span class="response" id="command_dimby_3"></span></p>
            <p>What do you want the Assistant to say in Response: <span class="response" id="command_dimby_response"></span></p>
            <p>URL: <span class="response" id="command_dimby_url"></span></p>
            <p>Method: Get</p>
			
			
			<!-- Brighten By, Dim By -->
			
    	</div>
        
        <div id="sceneLights">
            <h1>Run Command</h1>
            <p>What do you want to say: <span class="response" id="command_run_1"></span></p>
            <p>Whats another way to say it? (Optional): <span class="response" id="command_run_2"></span></p>
            <p>And another way?(optional): <span class="response" id="command_run_3"></span></p>
            <p>What do you want the Assistant to say in Response: <span class="response" id="command_run_response"></span></p>
            <p>URL: <span class="response" id="command_run_url"></span></p>
            <p>Method: Get</p>
            
            <h1>Off Command</h1>
            <p>What do you want to say: <span class="response" id="scene_command_off_1"></span></p>
            <p>Whats another way to say it? (Optional): <span class="response" id="scene_command_off_2"></span></p>
            <p>And another way?(optional): <span class="response" id="scene_command_off_3"></span></p>
            <p>What do you want the Assistant to say in Response: <span class="response" id="scene_command_off_response"></span></p>
            <p>URL: <span class="response" id="scene_command_off_url"></span></p>
            <p>Method: Get</p>
            
            <h1>All On Command</h1>
            <p>What do you want to say: <span class="response" id="scene_command_on_1"></span></p>
            <p>Whats another way to say it? (Optional): <span class="response" id="scene_command_on_2"></span></p>
            <p>And another way?(optional): <span class="response" id="scene_command_on_3"></span></p>
            <p>What do you want the Assistant to say in Response: <span class="response" id="scene_command_on_response"></span></p>
            <p>URL: <span class="response" id="scene_command_on_url"></span></p>
            <p>Method: Get</p>
        </div>
    </div>
	<div class="clear"></div>
</div>



<?php
}else{
	echo '<p>To use IFTTT you must enable the external API access</p>';
}
?>


</div>



<?php
pageFooter();
?>