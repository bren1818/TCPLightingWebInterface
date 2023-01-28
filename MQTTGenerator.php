<?php
include "include.php";
require("phpMQTT/phpMQTT.php");



 if( TOKEN != "" ){
	if($ENABLE_MQTT == 0){
		echo "MQTT is not Enabled";
	} else {

/* Begin Python File Fixed Header Creation */
$file_handle = fopen('mqtt_sub.py', 'w') or die('Error opening mqtt_sub.py.');
$data1 = "#!/usr/bin/env python3\n\nimport paho.mqtt.client as mqtt\nimport requests\nimport subprocess\n\n";
$data2 = "# This is the Subscriber\n\n";
$data3 = "def on_connect(client, userdata, flags, rc):\n";
$data4 = "    print(\"Connected with result code \"+str(rc)) \n";
$data5 = "    client.subscribe(\"".$MQTT_prefix."/#\")\n";
$data6 = "    client.subscribe(\"control/".$MQTT_control."/#\")\n";
$data7 = "    client.subscribe(\"".$HASSOnline."\")\n";
$data8 = "\n### topic message\ndef on_message(mosq, obj, msg):\n    print(msg.topic+\" \"+str(msg.qos)+\" \"+str(msg.payload))";
$data9 ="\n\ndef on_message_control(client, userdata, msg):\n    if (msg.payload.decode() == 'QUIT'):\n        print ('Exiting')\n        client.disconnect()\n";
$data10 ="    elif (msg.payload.decode() == 'REBOOT'):\n        print ('Rebooting')\n        client.disconnect()\n        command = 'sudo reboot'\n        subprocess.check_call(command.split())\n";
$data11 ="\ndef on_message_hassonline(client, userdata, msg):\n    if (msg.payload.decode() == 'online'):\n        print ('Refreshing')\n        command = 'curl ".LOCAL_URL."/mqttdiscovery.php'\n        subprocess.check_call(command.split())\n\n";

fwrite($file_handle, $data1);
fwrite($file_handle, $data2);
fwrite($file_handle, $data3);
fwrite($file_handle, $data4);
fwrite($file_handle, $data5);
fwrite($file_handle, $data6);
fwrite($file_handle, $data7);
fwrite($file_handle, $data8);
fwrite($file_handle, $data9);
fwrite($file_handle, $data10);
fwrite($file_handle, $data11);
fclose($file_handle);
/* End Python File Fixed Header Creation */

	$mqtt = new phpMQTT($MQTTserver, $MQTTport, uniqid());
	
	//Get State of System Data
	$CMD = "cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>RoomGetCarousel</gcmd><gdata><gip><version>1</version><token>".TOKEN."</token><fields>name,image,imageurl,control,power,product,class,realtype,status</fields></gip></gdata></gwrcmd></gwrcmds>&fmt=xml";
	$result = getCurlReturn($CMD);
	
	$array = xmlToArray($result);
	
	//check if token is expired 
	if( isset( $array["gwrcmd"]["gdata"]["gip"]["room"] ) ){
		$DATA = $array["gwrcmd"]["gdata"]["gip"]["room"];
	}else{
			echo "No Room Data";
			pa( $array );
			$DATA =  array();
			pageFooter();
			exit;
	}
	
	$deviceCount = 0;
	
	if( sizeof($DATA) > 0 ){
		if ( isset( $DATA["rid"] ) ){ $DATA = array( $DATA ); }
		
		foreach($DATA as $room){
			$RoomName = str_replace(' ', '', $room['name']);
			if( isset($room['rid'] ) ){
				$DEVICES = array();

				if ($mqtt->connect(true, NULL, $MQTTusername, $MQTTpassword)) {
					$file_handle = fopen('mqtt_sub.py', 'a') or die('Error opening file.');
					$data1 = "\n### ".$RoomName." \ndef on_message_".$RoomName."(client, userdata, msg):\n";
					$data2 = "    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):\n        print (\"".$RoomName."\" + msg.payload.decode())\n";
					$data3 = "        r = requests.get('".LOCAL_URL."/api.php?fx=toggle&type=room&uid=".$room['rid']."&val=' + msg.payload.decode())\n";
					$data4 = "        r.json()\n";
					$data5 = "        client.publish(\"".$MQTT_prefix."/".$RoomName."/".$room['rid']."/status\", msg.payload.decode()";
					$data6 = ", 0, True)\n";
					$data7 = "\ndef on_message_".$RoomName."_Bright(client, userdata, msg):\n";
					$data8 = "    print (\"".$RoomName." Brightness \" + msg.payload.decode())\n";
					$data9 = "    r = requests.get('".LOCAL_URL."/api.php?fx=dim&type=room&uid=".$room['rid']."&val=' + msg.payload.decode())\n";
					$data10 =  "    r.json()\n\n";
					$data11 = "    client.publish(\"".$MQTT_prefix."/".$RoomName."/".$room['rid']."/brightness\", msg.payload.decode()";
					$data12 = ", 0, True)\n";
					
					fwrite($file_handle, $data1);
					fwrite($file_handle, $data2);
					fwrite($file_handle, $data3);
					fwrite($file_handle, $data4);
					fwrite($file_handle, $data5);
					fwrite($file_handle, $data6);
					fwrite($file_handle, $data7);
					fwrite($file_handle, $data8);
					fwrite($file_handle, $data9);
					fwrite($file_handle, $data10);
					fwrite($file_handle, $data11);
					fwrite($file_handle, $data12);
					fclose($file_handle);

				$mqtt->close();
			}			
				
				
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
						$unplugged = 0;
						$roomBrightness = 0;
						$roomDevices = 0;
						if ($mqtt->connect(true, NULL, $MQTTusername, $MQTTpassword)) {
							foreach($DEVICES as $device){
								$DeviceName = str_replace(' ', '', $device['name']);
								$file_handle = fopen('mqtt_sub.py', 'a') or die('Error opening file.');
								$data1 = "\n### ".$RoomName."-".$DeviceName." \ndef on_message_".$RoomName."_".$DeviceName."(client, userdata, msg):\n";
								$data2 = "    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):\n        print (\"".$RoomName." ".$DeviceName."\" + msg.payload.decode())\n";
								$data3 = "        r = requests.get('".LOCAL_URL."/api.php?fx=toggle&type=device&uid=".$device['did']."&val=' + msg.payload.decode())\n";
								$data4 = "        r.json()\n";
								$data5 = "        client.publish(\"".$MQTT_prefix."/".$RoomName."/".$DeviceName."/".$device['did']."/status\", msg.payload.decode()";
								$data6 = ", 0, True)\n";
								$data7 = "\ndef on_message_".$RoomName."_".$DeviceName."_Bright(client, userdata, msg):\n";
								$data8 = "    print (\"".$RoomName." ".$DeviceName." Brightness \" + msg.payload.decode())\n";
								$data9 = "    r = requests.get('".LOCAL_URL."/api.php?fx=dim&type=device&uid=".$device['did']."&val=' + msg.payload.decode())\n";
								$data10 =  "    r.json()\n\n";
								$data11 = "    client.publish(\"".$MQTT_prefix."/".$RoomName."/".$DeviceName."/".$device['did']."/brightness\", msg.payload.decode()";
								$data12 = ", 0, True)\n";
								
								fwrite($file_handle, $data1);
								fwrite($file_handle, $data2);
								fwrite($file_handle, $data3);
								fwrite($file_handle, $data4);
								fwrite($file_handle, $data5);
								fwrite($file_handle, $data6);
								fwrite($file_handle, $data7);
								fwrite($file_handle, $data8);
								fwrite($file_handle, $data9);
								fwrite($file_handle, $data10);
								fwrite($file_handle, $data11);
								fwrite($file_handle, $data12);
								fclose($file_handle);
							}

							$mqtt->close();
						} else {
							echo "Time out!\n";
						}

				}else{
					echo 'No devices?';
					pa( $room );
				}
			}
		}
	}
	
	$CMD = "cmd=SceneGetListDetails&data=<gip><version>1</version><token>".TOKEN."</token><bigicon>1</bigicon></gip>";
	$sresult = getCurlReturn($CMD);
	$sarray = xmlToArray($sresult);
	$scenes = $sarray["scene"];
	if( is_array($scenes) ){
		foreach($scenes as $scene){
			$SceneName = str_replace(' ', '', $scene['name']);
//		for($x = 0; $x < sizeof($scenes); $x++){
			if ($mqtt->connect(true, NULL, $MQTTusername, $MQTTpassword)) {
					$file_handle = fopen('mqtt_sub.py', 'a') or die('Error opening file.');
					$data1 = "\n### ".$SceneName." \ndef on_message_".$SceneName."(client, userdata, msg):\n";
					$data2 = "    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):\n        print (\"".$SceneName."\" + msg.payload.decode())\n";
					$data3 = "        r = requests.get('".LOCAL_URL."/api.php?fx=scene&uid=".$scene['sid']."&type=' + msg.payload.decode())\n";
					$data4 = "        r.json()\n";
					$data5 = "        client.publish(\"".$MQTT_prefix."/".$SceneName."/".$scene['sid']."/status\", msg.payload.decode()";
					$data6 = ", 0, True)\n";
							
					fwrite($file_handle, $data1);
					fwrite($file_handle, $data2);
					fwrite($file_handle, $data3);
					fwrite($file_handle, $data4);
					fwrite($file_handle, $data5);
					fwrite($file_handle, $data6);
					fclose($file_handle);
			}

		}

	}	
	/* Begin Python File Fixed Connect Creation */
	$file_handle = fopen('mqtt_sub.py', 'a') or die('Error opening file.');
	$data1 = "\n\nclient = mqtt.Client('".$MQTTsub_id."')               #create new instance\n";
	$data2 = "client.username_pw_set('".$MQTTusername."', password='".$MQTTpassword."')    #set username and password\n\n";
	$data3 = "#Callbacks that trigger on a specific subscription match\nclient.message_callback_add('control/".$MQTT_control."', on_message_control)\n";
	$data4 = "client.message_callback_add('".$HASSOnline."', on_message_hassonline)\n";
	fwrite($file_handle, $data1);
	fwrite($file_handle, $data2);
	fwrite($file_handle, $data3);
	fwrite($file_handle, $data4);
	fclose($file_handle);	
	/* End Python File Fixed Connect Creation */

	$deviceCount = 0;
	
	if( sizeof($DATA) > 0 ){
		if ( isset( $DATA["rid"] ) ){ $DATA = array( $DATA ); }
		
		foreach($DATA as $room){
			$RoomName = str_replace(' ', '', $room['name']);
			if( isset($room['rid'] ) ){
				$DEVICES = array();
				if ($mqtt->connect(true, NULL, $MQTTusername, $MQTTpassword)) {
						$file_handle = fopen('mqtt_sub.py', 'a') or die('Error opening file.');
						$data1 = "\n### ".$RoomName." Begin\n";
						$data2 = "client.message_callback_add('".$MQTT_prefix."/".$RoomName."/".$room['rid']."/switch', on_message_".$RoomName.")\n";
						$data3 = "client.message_callback_add('".$MQTT_prefix."/".$RoomName."/".$room['rid']."/brightness/set', on_message_".$RoomName."_Bright)\n";
						fwrite($file_handle, $data1);
						fwrite($file_handle, $data2);
						fwrite($file_handle, $data3);
						fclose($file_handle);
	
					$mqtt->close();
				}
			
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
						$unplugged = 0;
						$roomBrightness = 0;
						$roomDevices = 0;
						if ($mqtt->connect(true, NULL, $MQTTusername, $MQTTpassword)) {
							foreach($DEVICES as $device){
								$DeviceName = str_replace(' ', '', $device['name']);
								$file_handle = fopen('mqtt_sub.py', 'a') or die('Error opening file.');
								$data1 = "\n### ".$RoomName."-".$device['name']." Begin\n";
								$data2 = "client.message_callback_add('".$MQTT_prefix."/".$RoomName."/".$DeviceName."/".$device['did']."/switch', on_message_".$RoomName."_".$DeviceName.")\n";
								$data3 = "client.message_callback_add('".$MQTT_prefix."/".$RoomName."/".$DeviceName."/".$device['did']."/brightness/set', on_message_".$RoomName."_".$DeviceName."_Bright)\n";
								fwrite($file_handle, $data1);
								fwrite($file_handle, $data2);
								fwrite($file_handle, $data3);
								fclose($file_handle);
														
							}

							$mqtt->close();
						} else {
							echo "Time out!\n";
						}

				}else{
					echo 'No devices?';
					pa( $room );
				}
			}
		}
	}

} 

$CMD = "cmd=SceneGetListDetails&data=<gip><version>1</version><token>".TOKEN."</token><bigicon>1</bigicon></gip>";
$sresult = getCurlReturn($CMD);
$sarray = xmlToArray($sresult);
$scenes = $sarray["scene"];
if( is_array($scenes) ){
	foreach($scenes as $scene){
		$SceneName = str_replace(' ', '', $scene['name']);
//		for($x = 0; $x < sizeof($scenes); $x++){
		if ($mqtt->connect(true, NULL, $MQTTusername, $MQTTpassword)) {
				$file_handle = fopen('mqtt_sub.py', 'a') or die('Error opening file.');
				$data1 = "\n### ".$scene['name']." Begin\n";
				$data2 = "client.message_callback_add('".$MQTT_prefix."/".$SceneName."/".$scene['sid']."/switch', on_message_".$SceneName.")\n";
				fwrite($file_handle, $data1);
				fwrite($file_handle, $data2);
				fclose($file_handle);
		}

	}

}
 }
/* Begin Python File Fixed Footer Creation */
$file_handle = fopen('mqtt_sub.py', 'a') or die('Error opening file.');
$data3 = "client.connect('".$MQTTserver."', ".$MQTTport.",60)\n\n";
$data4 = "client.on_connect = on_connect\nclient.on_message = on_message\n\nclient.loop_forever()\n";
fwrite($file_handle, $data3);
fwrite($file_handle, $data4);
fclose($file_handle);	
/* End Python File Fixed Footer Creation */

Echo "mqtt_sub.py created"

?>

