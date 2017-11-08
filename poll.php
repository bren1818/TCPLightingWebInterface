<?php
header('Content-Type: application/json');
error_reporting(0);
include "config.php";
$data = array();
$data["bridges"] = sizeof( $home->getDevices() );
$sensors = array();
$devices = array();

//if bridge is using caching, need to reset cache..

foreach( $home->getDevices() as $bridge ){
	foreach( $bridge->getDevices() as $device){
		$d = array();
		$d["id"] = $device->id;
		$d["online"] = $device->online;
		$d["brightness"] = $device->brightness;
		$d["state"] = $device->state;
		
		
		$devices[] = $d;
	}
	
	if( sizeof( $bridge->sensors ) > 0 ){
		foreach( $bridge->sensors as $sensor){
			$s = array();
			if( $sensor["type"] == "ZLLTemperature" ){
				$s["type"] = "thermometer";
				$s["id"] = $sensor["uniqueid"];
				$s["temp"] = ( $sensor["state"]["temperature"] / 100 );
				$s["lastActivity"] = date("Y-m-d h:i:s a", strtotime(  $sensor["state"]["lastupdated"]." -5 hours" )   ) ;
				$s["activityts"] = strtotime(  $sensor["state"]["lastupdated"]." -5 hours" )    ;
				$s["battery"] = $sensor["config"]["battery"];
				
				
			}else if( $sensor["type"] == "ZLLSwitch" ){	
				$s["type"] = "switch";
				$s["id"] = $sensor["uniqueid"];
				$s["activityts"] = strtotime(  $sensor["state"]["lastupdated"]." -5 hours" )    ;				
				$s["lastActivity"] = date("Y-m-d h:i:s a", strtotime(  $sensor["state"]["lastupdated"]." -5 hours" )   ) ;
				$s["battery"] = $sensor["config"]["battery"];
			
			}else if( $sensor["type"] == "ZLLPresence" ){
				$s["type"] = "motionSensor";
				$s["id"] = $sensor["uniqueid"];
				$s["lastActivity"] = date("Y-m-d h:i:s a", strtotime(  $sensor["state"]["lastupdated"]." -5 hours" )   ) ;
				$s["activityts"] = strtotime(  $sensor["state"]["lastupdated"]." -5 hours" )    ;
				$s["battery"] = $sensor["config"]["battery"];
				
			}else if( $sensor["type"] == "ZLLLightLevel" ){
				$s["type"] = "lightSensor";
				$s["id"] = $sensor["uniqueid"];
				$s["lastActivity"] = date("Y-m-d h:i:s a", strtotime(  $sensor["state"]["lastupdated"]." -5 hours" )   ) ;
				$s["activityts"] = strtotime(  $sensor["state"]["lastupdated"]." -5 hours" )    ;				$s["lightLevel"] = $sensor["state"]["lightlevel"];
				$s["dark"] = $sensor["state"]["dark"];
				$s["daylight"] = $sensor["state"]["daylight"];
				$s["battery"] = $sensor["config"]["battery"];
				$s["tholddark"] = $sensor["config"]["tholddark"];
				$s["tholdoffset"] = $sensor["config"]["tholdoffset"];
			}			
			
			$sensors[] = $s;
		}
	}
}

$data["devices"] = $devices;
$data["sensors"] = $sensors;
$data["ts"] = time();

echo json_encode($data);
exit;
?>