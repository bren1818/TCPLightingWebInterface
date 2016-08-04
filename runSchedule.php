<?php
/*
 *
 * TCP Ligthing Scheduler Script - By Brendon Irwin
 * 
 */
 
include "include.php";

/*Get Empty "default" schedule array*/
function getCleanSchedTask(){
	return array(
		"DAY_MON" => "off",
		"DAY_TUE" => "off",
		"DAY_WED" => "off",
		"DAY_THU" => "off",
		"DAY_FRI" => "off",
		"DAY_SAT" => "off",
		"DAY_SUN" => "off",
		"DAY_ALL" => "off",
		"FX" => "SWITCH",
		"HOUR" => "0",
		"MIN" => "0",
		"DIM_SCHED" => "0",
		"SWITCH_SCHED" => "0",
		"devices" => array()
	);
}

$tasks = array();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

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
		file_put_contents("schedule.sched", $serialized);
	}
	
}else{
	if( file_exists("schedule.sched") ){
		$array = file_get_contents("schedule.sched");
		$tasks = unserialize ($array);	
	}
}


	if( sizeof($tasks) > 0 ){
		$HOUR_NOW = date('H');
		$MIN_NOW = date('i');
		$DAY_NOW = date('D');
		
		foreach($tasks as $task){
			if( $task["DAY_ALL"] == "on" || $task["DAY_".strtoupper($DAY_NOW)] == "on" ){
				if( $task["HOUR"] == $HOUR_NOW){
					if( $task["MIN"] == $MIN_NOW){
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
							$req = LOCAL_URL."/API.php?fx=".$fx."&type=device&uid=".$ID."&val=".$val;
							$data = file_get_contents($req);
							if(LOG_ACTIONS){
								file_put_contents("schedule.actioned", $req." - Response: ".$data."\n", FILE_APPEND | LOCK_EX);
							}
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