<?php
	include "include.php";
	include "Plugins/plugins.php";
	
	date_default_timezone_set("America/New_York"); 				//Ensure this matches your timezone so if you use scheduler the hours match
	
	
	$tcp1 = new tcp_bridge();
	$tcp1->setID( "TCP 1" );		//UNIQUE ID
	$tcp1->setEnabled( true );		//SET TRUE
	$tcp1->setIP("192.168.1.108"); 	//SET BRIDGE IP
	$tcp1->setName("TCP Bridge 1");
	$tcp1->setHueEmulation( true );
	$tcp1->setTokenPath( dirname(__FILE__).DIRECTORY_SEPARATOR.$tcp1->getName().'.token' );
	$tcp1->init();
	
	$tcp2 = new tcp_bridge();
	$tcp2->setID( "TCP 2" );		//UNIQUE ID
	$tcp2->setEnabled( true );		//SET TRUE
	$tcp2->setIP("192.168.1.109"); 	//SET BRIDGE IP
	$tcp2->setName("TCP Bridge 2");
	$tcp2->setHueEmulation( true );
	$tcp2->setTokenPath( dirname(__FILE__).DIRECTORY_SEPARATOR.$tcp2->getName().'.token' );
	$tcp2->init();
	
	
	$hue = new hue_bridge();
	$tcp2->setID( "Hue 1" );		//UNIQUE ID
	$hue->setName("Philips Hue");
	$hue->setEnabled( false );
	$hue->setIP("192.168.1.107");
	
	/***Home Config***/
	$home = new home();
	$home->setName("Bren's Home");
	$home->setIP( '127.0.0.1' );
	
	/***Add Devices***/
	$home->addBridge( $tcp1 );
	$home->addBridge( $tcp2 );
	
	
	
	
?>