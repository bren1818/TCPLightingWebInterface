# TCPLightingWebInterface-MQTT - Can be used with MQTT & IFTTT
This is a fork of the fabulous TCPLightingWebInterface (https://github.com/bren1818/TCPLightingWebInterface) created by bren1818.

This project aims to remove cloud dependance by leveraging MQTT. While leaving the IFTTT focused API available. 

This is acheived through an additional python script (generated as need by a PHP script) and  a small API change 

Initial setup is as described in the original projects wiki - https://github.com/bren1818/TCPLightingWebInterface/wiki

MQTT Configuration is an additional section in config.inc.php as below
Currently the code requires an authenticated connection to the MQTT server
```
/*
	MQTT Services - Broker connections settings for subscribing and publishing 
*/
$ENABLE_MQTT = 1;			// Enable MQTT State Publishing (1 = true, 0 = false)
$MQTTserver = "172.16.33.8"; 		// Change as necessary
$MQTTport = 1883;			// Change as necessary
$MQTTusername = "admin";		// Set your username
$MQTTpassword = "password";             // set your password
$MQTTsub_id = "tcp-subscriber"; 	// make sure this is unique for connecting to sever - you could use uniqid()
$MQTTpub_id = "tcp-publisher"; 		// make sure this is unique for connecting to sever - you could use uniqid()
$MQTT_prefix = "light";         	// Topic prefix for lights
$ENABLE_HA_DISCO = 1;		        // Enable MQTT Publishing of Home Assistant Discovery Topics
$HASSTopic_id = "homeassistant";	// Topice prefix for Home Assistant Discovery Topics
```

Once your lights are setup in TCP Connected bridge. You would run http://<webInterfaceIP>/MQTTGenerator.php  This will generate the python subscriber file mqtt_sub.py  - To run it you need the python modules - paho.mqtt.client and requests.  

You can run it however you like. I generally create a service for scripts like this so they always run on restart. https://tecadmin.net/setup-autorun-python-script-using-systemd/ My service definition looks like this.

```
[Unit]
Description=MQTT Sub
After=multi-user.target
Conflicts=getty@tty1.service

[Service]
Type=simple
ExecStart=/usr/bin/python3 /home/pi/mqtt_sub.py
StandardInput=tty-force

[Install]
WantedBy=multi-user.target
```

For the publishing of state changes. As long as you have configured the MQTT section in config.inc.php any states changed by MQTT should be published. Additionally the state topic is set to retained so that if the web interface is restarted the state of the bulbs should be retained as well.

Additionally there is a php page called mqttstate.php that will double check the current state of a bulb and update MQTT accordingly. I use this because I still sometimes control the bulbs with the Android app and it doesn't automatically publish changes to MQTT. I run it as a cron job every 5 minutes

`*/5 * * * * lynx -dump https://lighting.taylortrash.com/mqttstate.php`

There are 4 topics for each bulb On/Off, State, Brightness, Brightness State. They follow the format of. Note the light prefix can be changed in config.inc.php
```
On/Off State: light/<room-name>/<light-name>/<UniqueBulbID>/status
On/Off Command: light/<room-name>/<light-name>/<UniqueBulbID>/switch
Brightness State: light/<room-name>/<light-name>/<UniqueBulbID>/brightness
Brightness Command: light/<room-name>/<light-name>/<UniqueBulbID>/brightness/set
```

## Home Assistant Integration

These lights can easily be integrated into Home Assistant with YAML. For example.
```
  - platform: mqtt
    name: "Back Entry"
    state_topic: "light/RearEntrance/BackDoor/216773570733040739/status"
    command_topic: "light/RearEntrance/BackDoor/216773570733040739/switch"
    brightness_state_topic: "light/RearEntrance/BackDoor/216773570733040739/brightness"
    brightness_command_topic: "light/RearEntrance/BackDoor/216773570733040739/brightness/set"
    brightness_scale: 100
    qos: 0
    payload_on: "1"
    payload_off: "0"
    optimistic: false
```

Additionally there is developmental support for HA MQTT Discovery. This is done through the php page mqttdiscovery.php As HA doesn't retain discovered devices on restart I have chosen to also run this page as a cron job every 5 minutes ie.
`*/5 * * * * lynx -dump https://lighting.taylortrash.com/mqttdiscovery.php`
