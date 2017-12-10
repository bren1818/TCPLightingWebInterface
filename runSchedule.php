<?php
error_reporting(E_ALL);
/*
 *
 * TCP Ligthing Scheduler Script - By Brendon Irwin
 * 
 * Sunrise/Sunset mod originally added by Andrew Tsui
 *
 */
 
include "include.php";

/*Get Empty "default" schedule array*/
function getCleanSchedTask(){
	return array(
		"TITLE_NOTE" => "",
		"DAY_MON" => "off",
		"DAY_TUE" => "off",
		"DAY_WED" => "off",
		"DAY_THU" => "off",
		"DAY_FRI" => "off",
		"DAY_SAT" => "off",
		"DAY_SUN" => "off",
		"DAY_ALL" => "off",
		"TIME_TYPE" => "FIXED",
		"FX" => "SWITCH",
		"HOUR" => "0",
		"MIN" => "0",
		"OFFSET_HOUR" => "0",
		"OFFSET_MIN" => "0",
		"OFFSET_DIR" => "after",
		"DIM_SCHED" => "0",
		"SWITCH_SCHED" => "0",
		"devices" => array()
	);
}

$tasks = array();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
echo date("Y-m-d h:i:s");
if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
	
	$sched = $_POST["schedule"];
	
	$sched = json_decode($sched);
	
	$tasks = array();
	foreach( $sched as &$scheduledItem){
		$newTask = getCleanSchedTask();
		foreach( $scheduledItem as $item){
			if( is_object($item) ){
				if( isset($item->name) && $item->name == "DEVICE_SELECTED"){
					array_push($newTask["devices"], $item->value);
				}else{
					$newTask[$item->name] = $item->value;
				}
			}
		}
		$tasks[] = $newTask;
	}
	
	if( SAVE_SCHEDULE ){
		$serialized = serialize($tasks);
		$curDir = dirname(__FILE__);		
		file_put_contents($curDir . DIRECTORY_SEPARATOR . "schedule.sched", $serialized);
	}
	
}else{
	echo "looking for tasks";
	$curDir = dirname(__FILE__);

	if( file_exists( $curDir . DIRECTORY_SEPARATOR .  "schedule.sched") ){
		$array = file_get_contents( $curDir . DIRECTORY_SEPARATOR .  "schedule.sched");
		$tasks = unserialize ($array);	
	}else{
		echo "No Schedule file!";
	}
}


	if( sizeof($tasks) > 0 ){

		$HOUR_NOW = date('H');
		$MIN_NOW = date('i');
		$DAY_NOW = date('D');
		$sun_info = date_sun_info(time(), LATITUDE, LONGITUDE);
		
		foreach($tasks as $task){
			if( $task["DAY_ALL"] == "on" || $task["DAY_".strtoupper($DAY_NOW)] == "on" ){

				if (isset($task["OFFSET_DIR"]) && $task["OFFSET_DIR"] == "before") {
					$offset_dir = "-";
				}else{
					$offset_dir = "+";
				}

				if( $task["TIME_TYPE"] == "SUNRISE") {
					$task_hour = date("H", strtotime($offset_dir . $task['OFFSET_HOUR'] . " hours " . $offset_dir . $task['OFFSET_MIN'] . " minutes ", $sun_info['sunrise']));
					$task_min = date("i", strtotime($offset_dir . $task['OFFSET_HOUR'] . " hours " . $offset_dir . $task['OFFSET_MIN'] . " minutes ", $sun_info['sunrise']));
				}elseif ($task["TIME_TYPE"] == "SUNSET"){
					$task_hour = date("H", strtotime($offset_dir . $task['OFFSET_HOUR'] . " hours " . $offset_dir . $task['OFFSET_MIN'] . " minutes ", $sun_info['sunset']));
					$task_min = date("i", strtotime($offset_dir . $task['OFFSET_HOUR'] . " hours " . $offset_dir . $task['OFFSET_MIN'] . " minutes ", $sun_info['sunset']));
				}else{
					$task_hour = $task["HOUR"];
					$task_min = $task["MIN"];
				}

				if( $task_hour == $HOUR_NOW){
					if( $task_min == $MIN_NOW){
						$fx = "";
						$val = 0;
						if( $task["FX"] == "SWITCH" ){
							//toggle switch
							$fx = "toggle";
							//look at switch_sched
							$val = $task["SWITCH_SCHED"];
						}else{
							//toggle dim
							$fx = "dim";
							//look at dim_sched
							$val = $task["DIM_SCHED"];
						}
						//do it for each device
						foreach($task["devices"] as $U=>$ID ){
							
							$req = LOCAL_URL . "/api.php?fx=".$fx."&type=device&uid=".$ID."&val=".$val;
														
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $req);
							curl_setopt($ch, CURLOPT_POST, false);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							$result = curl_exec($ch);
							curl_close($ch);
							
							if(LOG_ACTIONS){
								SCHEDLog( 'Running Task - '.$req. " - Result: " . print_r($result, true) );
							}
							echo $result;
						}
					}else{
						//next task
					}
				}else{
					//next task
				}
			}else{
				//next task
			}
		}
	}else{
		//Done
	}

?>
