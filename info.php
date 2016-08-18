<?php
/*
 *
 * TCP Ligthing Web UI Info Script - By Brendon Irwin
 * 
 */

include "include.php";

if( isset($_REQUEST['did']) && $_REQUEST['did'] != "" ){
	$did = $_REQUEST['did'];
	echo '<h2>Device Info</h2>';
	echo '<p><b>Device ID:'.$did.'</b></p>';
	$CMD = "cmd=DeviceGetInfo&data=<gip><version>1</version><token>".TOKEN."</token><did>".$did."</did><fields>name,power,product,class,image,control,realtype,other,status</fields></gip>";
	
	$result = getCurlReturn($CMD);
	$array = xmlToArray($result);
	if( isset( $array["image"] ) && ! is_array( $array["image"]) ){
		echo '<p><img src="data:image/png;base64,'.$array["image"].'" /></p>';
	}
	
	pa( $array );
	
}
	
if( isset($_REQUEST['rid']) && $_REQUEST['rid'] != "" ){	
	$rid = $_REQUEST['rid'];
	echo '<h2>Room Information</h2>';
	echo '<p><b>Room ID:'.$rid.'</b></p>';
	
	$CMD = "cmd=RoomGetInfoAll&data=<gip><version>1</version><token>".TOKEN."</token><rid>".$rid."</rid><fields>name,power,product,class,image,imageurl,control,other</fields></gip>";
	
	$result = getCurlReturn($CMD);
	$array = xmlToArray($result);
	
	pa( $array );
}
?>