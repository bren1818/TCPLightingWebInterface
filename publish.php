<?php
	include "include.php";

	require("phpMQTT/phpMQTT.php");

echo hello;
$mqtt = new phpMQTT($MQTTserver, $MQTTport, $MQTTpub_id);

if ($mqtt->connect(true, NULL, $MQTTusername, $MQTTpassword)) {
	$mqtt->publish("light/MasterBedroom/Lamp/status", "on");
	$mqtt->publish("light/MasterBedroom/Lamp/switch", "on");
	$mqtt->close();

} else {
    echo "Time out!\n";
}
?>