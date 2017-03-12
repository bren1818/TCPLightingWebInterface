<?php
	/*
	 *
	 * PHP API CALLS
	 *
	 */	
	error_reporting(0);
	include "config.php";
	global $home;
	
	if( sizeof( $home->getDevices() ) == 0 ){
		//No bridges - report error
		exit;
	}
	
	$function = isset($_REQUEST['fx']) ? $_REQUEST['fx'] : ""; 			//Toggle or Brightness
	$bid =   	isset($_REQUEST['bid']) ? $_REQUEST['bid'] : "";		//DeviceID or Room ID
	$type = 	isset($_REQUEST['type']) ? $_REQUEST['type'] : "";		//Device or collection 
	$UID = 		isset($_REQUEST['uid']) ? $_REQUEST['uid'] : "";		//DeviceID or Room ID
	$val = 		isset($_REQUEST['val']) ? $_REQUEST['val'] : "";		//DeviceID or Room ID
	
	$val = $val < 0 ? 0 : $val;
	$val = $val > 100 ? 100 : $val;
		
	$bridge = "NOT_FOUND";
	foreach($home->getDevices() as $bridges){
		if( $bridges->getID() == $bid ){
			$bridge = $bridges;
			break;
		}
	}
	

	
	if( $bridge == "NOT_FOUND" && $function != "refreshState"){
		echo json_encode( array("error" => "Bridge not found") );
		exit;
	}
	
	if( $function == "refreshState" ){
		//
		
		$devices = array();
		foreach($home->getDevices() as $bridges){

			if( $bridges->getCacheDeviceState() ){ //if caching is disabled, clear the cache
				//$bridge->setCacheDeviceState(false);

				if( $bridges->getDeviceCachePath() != ""){
					error_log("Deleting Cache");
					if( unlink( $bridges->getDeviceCachePath() ) ){
						error_log("Deleted Cache");
					}
					$bridges->init();
				}
			}else{
				$bridges->init();
			}

			//append device states to response

			foreach( $bridges->getDevices() as $device ){
				$devices[] = array("bid" => $bridges->getID(), "did" => $device->getID(), "state" => $device->getState(), "brightness" => $device->getBrightness(), "online" => $device->getOnline() );
			}
		}

		$response = array("action" =>"refresh", "deviceStates"=> $devices);
		ob_clean();
		echo json_encode( $response );
		exit;

	}
	
	if( $function != "" && $type != "" && $UID != "" && $val != ""){ // && is_object($bridge) 
		if( $type == "device"){
			switch ($function){
				case "toggle": 
					$val = ($val > 0) ? 1 : 0;
					
					if( $val == 1 ){
						$ret =	$bridge->turnDeviceOn( $UID );
					}else{
						$ret = $bridge->turnDeviceOff( $UID );
					}
						
					echo json_encode( array("toggle" => $val, "device" => $UID, "return" => $ret) );
					
				break;
				case "dim": 
					$ret = $bridge->dimDevice( $UID , $val );
					echo json_encode( array("dim" => $val, "device" => $UID, "return" => $ret ) );
				break;
				case "color":
					//tbd
					
				
				break;
				default:
				echo json_encode( array("error" => "unknown function, required: toggle | dim") );
			}
			
		}elseif($type == "room"){ //collection
			
			if( $function == "toggle" ){
				$tval = ($val > 0) ? 1 : 0;	
				if( $val == 1 ){
					$ret = $bridge->turnCollectionOn( $UID );
				}else{
					$ret = $bridge->turnCollectionOff( $UID );
				}
			}elseif( $function == "dim" ){
				$ret = $bridge->dimCollection( $UID , $val );
			
			}elseif ( $function == "color"){
				//tbd
				
				
			}
			
			echo json_encode( array("room" => $UID, "fx" => $function, "val" => $val,  "return" => $ret) );
				
		}elseif($type == "all"){
			//iterate over each bridge...
			$bc = 0;
			foreach( $home->getDevices() as $bridge ){
				
				$DEVICES = $bridge->getDevices();

				if( sizeof($DEVICES) > 0 ){
					$bc++;
					if( $function == "toggle" ){
						//only toggle if it needs to be toggled							
						$tval = ($val > 0) ? 1 : 0;
						
						if( $tval == 1 ){
							$bridge->turnAllOn();
						}else{
							$bridge->turnAllOff();
						}
					}elseif( $function == "dim"){
						$bridge->dimAll( $val );
					}elseif( $function == "color" ){
						//tbd
						
						
						
					}
					
				}
			}
			
			echo json_encode( array("message" => $bc." Bridges messaged.") );
			
		}else{
			echo json_encode( array("error" => "unknown type, required: device | room") );
		}
	}else{
		echo json_encode( array("error" => "argument empty or invalid. Required: fx, type, UID, val", "recieved" => $_REQUEST) );
	}
?>	