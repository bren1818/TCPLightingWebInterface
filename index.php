<?php

/*
 *
 * TCP Ligthing Web UI Test Script - By Brendon Irwin
 * 
 */

include "config.php";
pageHeader("TCP Lighting Controller");

global $home;

echo '<div class="container">';
	echo '<h1>'.$home->getName()."</h1>";

	foreach( $home->getDevices() as $bridge ){
		
		if( $bridge->getEnabled() ){
			echo '<div class="bridge">';
				echo '<h2>'.$bridge->getName().'</h2>';
				if( $bridge->getID() == ""){
					echo "<p>".$bridge->getName()." is missing a unique ID. Please set!</p>";
				}
				$bridge->renderDevices();
			echo '</div>';
		}
	}

	//render home control...
echo '</div>';

echo '<div class="container">';
	echo '<h1>Home</h1>';
	echo '<div class="house">';
		echo '<button data-device-id="all" class="onOffHouseToggleButton buttonOn">On</button> | <button data-device-id="all" class="onOffHouseToggleButton buttonOff">Off</button>';
		echo '<div class="clear"></div>';
		echo '<p>Brightness:</p>';
		echo '<div class="house-slider" data-device-id="all"></div>';
	echo '</div>';
echo '</div>';

echo '<button ID="refresh">Refresh</button>';

pageFooter();
?>