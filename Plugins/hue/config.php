<?php

	$cwd = dirname( dirname(__FILE__) );
	require_once $cwd.DIRECTORY_SEPARATOR."base_bridge.php";
	
	$bridge = new base_bridge();
	$bridge->setName("Philips Hue");
	$bridge->setEnabled( false );
	$bridge->setIP("192.168.1.107");
	

?>