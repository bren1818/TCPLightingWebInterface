<?php
include "include.php";
require("phpMQTT/phpMQTT.php");


/*
 *
 * TCP Ligthing Web UI 
 * 
 */

/* Begin Python File Fixed Header Creation */
$file_handle = fopen('mqtt_sub.py', 'w') or die('Error opening file.');
$data1 = "#!/usr/bin/env python3\n\nimport paho.mqtt.client as mqtt\nimport requests\n\n";
$data2 = "# This is the Subscriber\n\n";
$data5 = "def on_connect(client, userdata, flags, rc):\n";
$data6 = "    print(\"Connected with result code \"+str(rc)) \n";
$data7 = "    client.subscribe(\"light/#\")\n";
$data8 = "    client.subscribe(\"control\")\n";
$data3 = "\n### topic message\ndef on_message(mosq, obj, msg):\n    print(msg.topic+\" \"+str(msg.qos)+\" \"+str(msg.payload))";
$data4 ="\n\ndef on_message_control(client, userdata, msg):\n    if (msg.payload.decode() == 'QUIT'):\n        print ('Exiting')\n    client.disconnect()\n\n";
fwrite($file_handle, $data1);
fwrite($file_handle, $data2);
fwrite($file_handle, $data5);
fwrite($file_handle, $data6);
fwrite($file_handle, $data7);
fwrite($file_handle, $data8);
fwrite($file_handle, $data3);
fwrite($file_handle, $data4);
fclose($file_handle);
/* End Python File Fixed Header Creation */

 if( TOKEN != "" ){
	$mqtt = new phpMQTT($MQTTserver, $MQTTport, uniqid());
	
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
	
	$deviceCount = 0;
	
	if( sizeof($DATA) > 0 ){
		if ( isset( $DATA["rid"] ) ){ $DATA = array( $DATA ); }
		
		foreach($DATA as $room){
			
			if( isset($room['rid'] ) ){
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
						$unplugged = 0;
						$roomBrightness = 0;
						$roomDevices = 0;
						if ($mqtt->connect(true, NULL, $MQTTusername, $MQTTpassword)) {
							foreach($DEVICES as $device){
								$file_handle = fopen('mqtt_sub.py', 'a') or die('Error opening file.');
								$data1 = "\n### ".$room['name']."-".$device['name']." \ndef on_message_".$room['name']."_".$device['name']."(client, userdata, msg):\n";
								$data2 = "    if (msg.payload.decode() == '1'):\n        print (\"".$room['name']." ".$device['name']."\" + msg.payload.decode())\n";
								$data3 = "        r = requests.get('".LOCAL_URL."/api.php?fx=toggle&type=device&uid=".$device['did']."&val=1')\n";
								$data4 = "        r.json()\n";
								$data5 = "    elif (msg.payload.decode() == '0'):\n        print (\"".$room['name']." ".$device['name']."\" + msg.payload.decode())\n";
								$data6 = "        r = requests.get('".LOCAL_URL."/api.php?fx=toggle&type=device&uid=".$device['did']."&val=0')\n";
								$data7 =  "        r.json()\n";
								$data8 = "\ndef on_message_".$room['name']."_".$device['name']."_Bright(client, userdata, msg):\n";
								$data9 = "    print (\"".$room['name']." ".$device['name']." Brightness \" + msg.payload.decode())\n";
								$data10 = "    r = requests.get('".LOCAL_URL."/api.php?fx=dim&type=device&uid=".$device['did']."&val=' + msg.payload.decode())\n";
								$data11 =  "    r.json()\n\n";
								
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
	

	/* Begin Python File Fixed Connect Creation */
	$file_handle = fopen('mqtt_sub.py', 'a') or die('Error opening file.');
	$data1 = "\n\nclient = mqtt.Client('".$MQTTsub_id."')               #create new instance\n";
	$data2 = "client.username_pw_set('".$MQTTusername."', password='".$MQTTpassword."')    #set username and password\n\n";
	$data3 = "#Callbacks that trigger on a specific subscription match\nclient.message_callback_add('control', on_message_control)\n";
	fwrite($file_handle, $data1);
	fwrite($file_handle, $data2);
	fwrite($file_handle, $data3);
	fclose($file_handle);	
	/* End Python File Fixed Connect Creation */

	$deviceCount = 0;
	
	if( sizeof($DATA) > 0 ){
		if ( isset( $DATA["rid"] ) ){ $DATA = array( $DATA ); }
		
		foreach($DATA as $room){
			
			if( isset($room['rid'] ) ){
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
						$unplugged = 0;
						$roomBrightness = 0;
						$roomDevices = 0;
						if ($mqtt->connect(true, NULL, $MQTTusername, $MQTTpassword)) {
							foreach($DEVICES as $device){
								$file_handle = fopen('mqtt_sub.py', 'a') or die('Error opening file.');
								$data1 = "\n### ".$room['name']."-".$device['name']." Begin\n";
								$data2 = "client.message_callback_add('light/".$room['name']."/".$device['name']."/".$device['did']."/switch', on_message_".$room['name']."_".$device['name'].")\n";
								$data3 = "client.message_callback_add('light/".$room['name']."/".$device['name']."/".$device['did']."/brightness/set', on_message_".$room['name']."_".$device['name']."_Bright)\n";
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

