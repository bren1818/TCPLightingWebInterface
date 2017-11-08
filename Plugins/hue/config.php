<?php

	$cwd = dirname( dirname(__FILE__) );
	require_once $cwd.DIRECTORY_SEPARATOR."base_bridge.php";
	
	class hue_bridge extends base_bridge{
		public $showSensors;
		public $sensors = [];
		public $groups;
		
		public function __construct(){
			$this->requiresToken = true;
			$this->supportsColor = true;
			$this->requiresHTTPS = false;
			$this->showSensors = true;
			$this->setID( md5("HUE") );
		}
		
		function init(){
			if( ! $this->getEnabled() ){
				return;
			}				
			
			if( $this->getRequiresToken() && file_exists( $this->getTokenPath() ) && file_get_contents( $this->getTokenPath() ) != "" ){
				$this->setToken( file_get_contents( $this->getTokenPath() ) );
				
			}else{
				//try to generate token
				$getToken = $this->runCommand('/api', '	{"devicetype":"my_hue_app#web bren1818"}', "POST" );
				if( isset($getToken[0]['error']) && $getToken[0]['error']['description'] == "link button not pressed" ){
					echo '<p>Error: '.$getToken[0]['error']['description'].'</p>';
					echo '<p>Generate Token for '.$this->getName().'</p>';
					
				}else{
					if( isset( $getToken[0]['success']['username'] ) ){
						$token = $getToken[0]['success']['username'];
						//UWHFzVJZjjeboh4WmFa6lduBwXadMdtXzSaCKKLz
						
						if( file_put_contents( $this->getTokenPath(), $token ) ){
							$this->setToken( $token );
						}else{
							echo "<p>Could not write token file ".$this->getTokenPath()."</p>";
						}
					
					}else{
						echo "<p>Unknown Error...</p>";
						echo '<pre>'.print_r(  $getToken  , true).'</pre>';
					}
				
				}
				
			}
			
			if( $this->getToken() != "" ){
				//echo "I has Token: ".$this->getToken();
				$system = $this->runCommand('/api/'.$this->getToken().'/lights', '', "GET" );
				if( is_array( $system ) ){
					$x = 1;
					foreach( $system as $device){
						$hueDevice = new hue_device();
						$hueDevice->setID( $x );
						$hueDevice->setHueID( $device['uniqueid'] );
						$hueDevice->setName( $device['name'] );
						$hueDevice->setState( $device['state']['on'] );
						$hueDevice->setBrightness( intval(( $device['state']['bri'] / 255 ) * 100) );
						if( $device['type'] == "Extended color light" ){
							$hueDevice->setColorSupport( true );
						}
						$hueDevice->setOnline( $device['state']['reachable'] );
						
						$rgb = $this->toRGB( $device['state']['xy'][0],$device['state']['xy'][1] , $device['state']['bri'] );
						
						
						$hueDevice->setColorRed( $rgb[0] );
						$hueDevice->setColorBlue( $rgb[2] );
						$hueDevice->setColorGreen( $rgb[1] );
						
						//$hueDevice->setHue( $device['state']['hue'] );
						//$hueDevice->setSat( $device['state']['sat'] );
						
						
						$hueDevice->setDeviceType( $device['type'] );
						$hueDevice->setDeviceOwner( $this->getID() );
						$this->addDevice( $hueDevice );
			
						$x++;
						
						
						/*
							
							$this->setState( 1 );
							$this->setBrightness( 100 );
							$this->setDeviceType( "" );
							$this->setOnline(  1 );
						*/
						/*
						  [state] => Array
							(
								[on] => 1
								[bri] => 254
								[hue] => 50100
								[sat] => 254
								[effect] => none
								[xy] => Array
									(
										[0] => 0.2496
										[1] => 0.0858
									)

								[ct] => 153
								[alert] => none
								[colormode] => hs
								[reachable] => 1
							)

						[type] => Extended color light
						[name] => Couch
						[modelid] => LCT007
						[manufacturername] => Philips
						[uniqueid] => 00:17:88:01:10:31:b1:47-0b
						[swversion] => 5.50.1.19085
						*/
					
					
					
					}					
					//echo '<pre>'.print_r(  $this  , true).'</pre>';
				}
				
				//show sensors
				if( $this->showSensors ){
					$sensors = $this->runCommand('/api/'.$this->getToken().'/sensors', '', "GET" );
					$sensorList = [];
					if( is_array( $sensors ) ){
						foreach( $sensors as $sensor){
							if( in_array( $sensor["type"] , array("ZLLTemperature", "ZLLSwitch", "ZLLPresence", "ZLLLightLevel") ) ){
								$sensorList[] = $sensor;
							}
						}
						
						$this->sensors = $sensorList;
						//echo '<pre>'.print_r($sensorList, true).'</pre>';
						
					}
				}
				
				$this->groups = $this->runCommand('/api/'.$this->getToken().'/groups', '', "GET" );
				
			}
			
		}
		
		function runCommand($PATH, $CMD, $METHOD){
			$SCHEME = "http";
			$URL = $SCHEME."://".$this->getIP();	
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $URL.$PATH);
			if( $METHOD == "POST"){
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $CMD);
			}
			if( $METHOD == "PUT" ){
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $CMD);
			}
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			$data = json_decode($result, true);
			
			curl_close($ch);
			return $data;
		}
		
		
		
		
		
		
		function turnDeviceOn( $ID ){ /*Override*/ 
			$this->runCommand('/api/'.$this->getToken().'/lights/'.$ID.'/state', '{"on":true}', "PUT");
		}
		
		function turnDeviceOff( $ID ){ /*Override*/ 
			$this->runCommand('/api/'.$this->getToken().'/lights/'.$ID.'/state', '{"on":false}', "PUT");
		}
		
		function dimDevice( $ID, $opacity){ /*Override*/ 
			 $this->runCommand('/api/'.$this->getToken().'/lights/'.$ID.'/state', '{"on":true,  "bri":'.intval( ($opacity / 100) * 255 ).'}', "PUT"); //"sat":254,,"hue":10000
		}
		
		function turnCollectionOn( $ID ){ /*Override*/}
		
		function turnCollectionOff( $ID ){ /*Override*/ 
		
		}
		
		function dimCollection( $ID, $opacity){ /*Override*/

		}
		
		function turnAllOn(){ /*Override*/
			//http://<bridge ip address>/api/<username>/groups/0/action
		
			for($x=1; $x<= sizeof($this->getDevices()); $x++){
				$this->runCommand('/api/'.$this->getToken().'/lights/'.$x.'/state', '{"on":true}', "PUT");
			}
		}
		
		function turnAllOff(){ /*Override*/ 
			//http://<bridge ip address>/api/<username>/groups/0/action
		
			for($x=1; $x<= sizeof($this->getDevices()); $x++){
				$this->runCommand('/api/'.$this->getToken().'/lights/'.$x.'/state', '{"on":false}', "PUT");
			}
		}
		
		
		function dimAll($opacity){ /*Override*/ }
		
		function toXY($red,$green,$blue){
			//Gamma correctie
			$red = ($red > 0.04045) ? pow( ($red + 0.055) / (1.0 + 0.055), 2.4) : ($red / 12.92);
			$green = ($green > 0.04045) ? pow( ($green + 0.055) / (1.0 + 0.055), 2.4) : ($green / 12.92);
			$blue = ($blue > 0.04045) ? pow( ($blue + 0.055) / (1.0 + 0.055), 2.4) : ($blue / 12.92);
			
			//Apply wide gamut conversion D65
			$X = $red * 0.664511 + $green * 0.154324 + $blue * 0.162028;
			$Y = $red * 0.283881 + $green * 0.668433 + $blue * 0.047685;
			$Z = $red * 0.000088 + $green * 0.072310 + $blue * 0.986039;
			
			$fx = $X / ($X + $Y + $Z);
			$fy = $Y / ($X + $Y + $Z);
			
			if ( is_nan($fx) ){
				$fx = 0.0;
			}
			if ( is_nan($fy) ) {
				$fy = 0.0;
			}
			
			return array( round($fx, 4), round($fy, 4) );
		}
		
		function toRGB( $x, $y, $brightness){
			$z = 1.0 - $x - $y;
            $uY = $brightness / 255; // / 255.0; // Brightness of lamp
            $uX = ($uY / $y) * $x;
            $Z = ($uY / $y) * $z;
            $r = $uX * 1.612 - $uY * 0.203 - $Z * 0.302;
            $g = (-1 * $uX) * 0.509 + $uY * 1.412 + $Z * 0.066;
            $b = $uX * 0.026 - $uY * 0.072 + $Z * 0.962;
            $r = $r <= 0.0031308 ? 12.92 * $r : (1.0 + 0.055) * pow($r, (1.0 / 2.4)) - 0.055;
            $g = $g <= 0.0031308 ? 12.92 * $g : (1.0 + 0.055) * pow($g, (1.0 / 2.4)) - 0.055;
            $b = $b <= 0.0031308 ? 12.92 * $b : (1.0 + 0.055) * pow($b, (1.0 / 2.4)) - 0.055;
            $maxValue = max($r,$g,$b);
            $r /= $maxValue;
            $g /= $maxValue;
            $b /= $maxValue;
            $r = $r * 255;   if ($r < 0) { $r = 255 ; }
            $g = $g * 255;   if ($g < 0) { $g = 255 ; }
            $b = $b * 255;   if ($b < 0) { $b = 255 ; }
            return array( round($r,0),round($g,0) ,round($b,0) );
		
		}
		
		function setDeviceColor( $deviceID, $r, $g, $b ){		
			$xy = $this->toXY($r,$g,$b);
			$this->runCommand('/api/'.$this->getToken().'/lights/'.$deviceID.'/state', '{"on" : true}', "PUT");
			$this->runCommand('/api/'.$this->getToken().'/lights/'.$deviceID.'/state', '{"xy":['.$xy[0].','.$xy[1].']}', "PUT");
			//"bri":'.intval( ($opacity / 100) * 255 )
			
			
		}
		
		function renderDevices(){
			
			if( sizeof($this->getDevices() ) > 0 ){
			
					if( sizeof( $this->getCollection() ) > 0 ){
						//go over collections and devices,	
						//echo '<div class="room-devices">';
						
						foreach( $this->getCollection() as $collection ){
							$collection->renderCollection();
						}
						
						
						
					}else{
						//spit out devices
						echo '<div class="roomContainer bridgeContainer">';
						echo '<div class="devices">';
						echo '<div class="room-devices">';
						
						
						foreach( $this->groups as $group ){
							if( $group["type"] == "Room" ){
								echo '<div class="roomContainer">';
									echo '<h3>'.$group["name"].'</h3>';
									echo '<div class="room-devices">';
									foreach( $this->getDevices() as $device ){
										//$device->renderDevice();
										
										if( in_array( $device->getID(), $group["lights"] ) ){
											$device->renderDevice();
										}
										
										
									}
									echo '</div>';
								
									//room controls
								
								echo '</div>';
							
							}
							
						}
							
						echo '</div>';
						if( sizeof( $this->sensors ) > 0 ){
							echo '<div class="room-sensors">';		
								
							foreach( $this->sensors as $sensor ){
									
								
								
								if( $sensor["type"] == "ZLLTemperature" ){
									echo '<div device-id="'.$sensor["uniqueid"].'">';
										echo "<b>Temperature</b>: ";
										echo ( $sensor["state"]["temperature"] / 100 ) . "&deg; C";	
										
										//echo '<pre>'.print_r($sensor, true).'</pre>';
										
									echo '</div>';
								}else if( $sensor["type"] == "ZLLSwitch" ){
									echo '<div device-id="'.$sensor["uniqueid"].'">';
										echo "<b>Switch</b>: ".$sensor["name"]."<br />";
										
										
										$datetime =  date("Y-m-d h:i:s a", strtotime(  $sensor["state"]["lastupdated"]." -5 hours" )   ) ;
										
										//echo '<pre>'.print_r($sensor, true).'</pre>';
										
										echo "Last Activity: ".$datetime."<br />";
										echo "Battery: ".$sensor["config"]["battery"];
										
										
									echo '</div>';
								}else if( $sensor["type"] == "ZLLPresence" ){
									echo '<div device-id="'.$sensor["uniqueid"].'">';
										echo "<b>Motion Sensor</b>: ".$sensor["state"]["presence"]."<br />";
										
										
										
										$datetime =  date("Y-m-d h:i:s a", strtotime(  $sensor["state"]["lastupdated"]." -5 hours" )   ) ;
										
										
										
										echo "Last Activity: ".$datetime."<br />";
										echo "Battery: ".$sensor["config"]["battery"];
										
										
										
									echo '</div>';
								}else if( $sensor["type"] == "ZLLLightLevel" ){
									echo '<div device-id="'.$sensor["uniqueid"].'">';
										echo "<b>Light Level</b>: ".$sensor["state"]["lightlevel"]."<br />";
										echo "Dark: ".$sensor["state"]["dark"]."<br />";
										echo "Daylight:  ".$sensor["state"]["daylight"]."<br />";
										
										echo "tholddark:  ".$sensor["config"]["tholddark"]."<br />";
										echo "tholdoffset:  ".$sensor["config"]["tholdoffset"]."<br />";
										
										$datetime =  date("Y-m-d h:i:s a", strtotime(  $sensor["state"]["lastupdated"]." -5 hours" )   ) ;
										
										
										
										echo "Last Activity: ".$datetime."<br />";
										
										echo "Battery: ".$sensor["config"]["battery"];
										
										//echo '<pre>'.print_r($sensor, true).'</pre>';
										
										
									echo '</div>';
								}else{
									echo '<pre>'.print_r($sensor, true).'</pre>';
								}
								echo '<br />';	
							}
							
							
							//echo '<pre>'.print_r($this->groups, true).'</pre>';
								
							echo '</div>';
						}
					
						echo '</div>';
						echo '</div>';
						
						
						
				
						
					}
					
				
			}
		}
		
		
	}
	

	
	class hue_device extends base_device{
		public $hueID;
		public $deviceype;
		
		public $type;
		public $modelid;
		public $manufacturername;
		public $swversion;
		/*
					  [state] => Array
							(
								[on] => 1
								[bri] => 254
								[hue] => 50100
								[sat] => 254
								[effect] => none
								[xy] => Array
									(
										[0] => 0.2496
										[1] => 0.0858
									)

								[ct] => 153
								[alert] => none
								[colormode] => hs
								[reachable] => 1
							)

						[type] => Extended color light
						[name] => Couch
						[modelid] => LCT007
						[manufacturername] => Philips
						[uniqueid] => 00:17:88:01:10:31:b1:47-0b
						[swversion] => 5.50.1.19085
						*/
		
		
		function setHueID($id){
			$this->hueID = $id;
		}
		function getHueID(){
			return $this->hueID;
		}
		
		function getDeviceType(){
			return $this->deviceType;
		}
		
		function setDeviceType($type){
			$this->deviceType = $type;
		}
		
		function __construct(){
			$this->setID( "" );
			$this->setName( "HUE" );
			$this->setState( 1 );
			$this->setBrightness( 100 );
			//$this->setDeviceType( "" );
			//$this->setOnline(  1 );
			//$this->setColorID( 0 );
			//$this->setRemoteID( "" ); //number on the remote which triggers it 
			//$this->setDeviceOwner( "" );
			
		}
	
		function rgb2hex($r,$g,$b){
			return sprintf('%02x', $r) . sprintf('%02x', $g) . sprintf('%02x', $b);
		}
		
		function renderDevice(){
																				//state['reachable']
			echo '<div data-bridgeID="'.$this->getDeviceOwner().'" class="'.( $this->getOnline() == 0 ? 'unplugged' : 'plugged' ).' device '.( $this->getState() == 1 ? 'light-on' : 'light-off' ).' '.( $this->getDeviceType() == 'Light Fixture' ? 'light-fixture' : '' ).'" data-device-id="'.$this->getID().'">'; //power > 0 then enabled 
				//level = brightness
				//state = on or off
				echo '<p>'.$this->getName().'</p>';
				
				echo '<button data-device-id="'.$this->getID().'" class="onOffDeviceToggleButton buttonOn">On</button> | <button data-device-id="'.$this->getID().'" class="onOffDeviceToggleButton buttonOff">Off</button>';
				echo '<div class="clear"></div>';
				
				echo '<input data-device-id="'.$this->getID().'" class="jscolor {mode:\'HS\', position:\'right\'}" value="'.$this->rgb2hex( $this->getColorRed(),$this->getColorGreen(), $this->getColorBlue() ).'">';
				
				echo $this->getColorRed().', '.$this->getColorGreen().', '.$this->getColorBlue();
				
				echo '<div class="clear"></div>';
				
				
				echo '<p>Brightness:</p>';
				echo '<div class="device-slider" data-value="'.( ( $this->getBrightness() ) ? $this->getBrightness() : 100).'" data-device-id="'. $this->getID().'"></div>';
			echo '</div>';
			
		}
	
		
	
	
	
	
	
	
	}
	

	

?>