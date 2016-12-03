<?php

	$cwd = dirname( dirname(__FILE__) );
	require_once $cwd.DIRECTORY_SEPARATOR."base_bridge.php";
	
	class tcp_device extends base_device{
		
		function __construct(){
			$this->setID( "" );
			$this->setName( "" );
			$this->setState( 1 );
			$this->setBrightness( 100 );
			$this->setDeviceType( "" );
			$this->setOnline(  1 );
			$this->setColorID( 0 );
			$this->setRemoteID( "" ); //number on the remote which triggers it 
			$this->setDeviceOwner( "" );
		}
		
		/*
		[did] => 216801307632666586
		[known] => 1
		[lock] => 0
		[state] => 1
		[level] => 100
		[node] => 164
		[port] => 0
		[nodetype] => 16386
		[name] => Living Room - Couch
		[desc] => LED
		[colorid] => 3
		[imgs] => images/lighting/TCP/TCP-A19.png
		[imgm] => images/lighting/TCP/TCP-A19.png
		[imgb] => images/lighting/TCP/TCP-A19.png
		[imgs_s] => images/lighting/TCP/TCP-A19.png
		[imgm_s] => images/lighting/TCP/TCP-A19.png
		[imgb_s] => images/lighting/TCP/TCP-A19.png
		[type] => multilevel
		[rangemin] => 0
		[rangemax] => 99
		[power] => 0.011
		[poweravg] => 0
		[energy] => 0
		[score] => 0
		[productid] => 1
		[prodbrand] => TCP
		[prodmodel] => LED A19 11W
		[prodtype] => LED
		[prodtypeid] => 78
		[classid] => 2
		[class] => Array
			(
			)

		[subclassid] => 1
		[subclass] => Array
			(
			)

		[other] => Array
			(
				[rcgroup] => 1
				[manufacturer] => TCP
				[capability] => productinfo,identify,meter_power,switch_binary,switch_multilevel
				[bulbpower] => 11
			)
		*/
		
		public $deviceType;
		public $online;
		public $colorID;
		public $remoteID;
		
		function arrayPopulate($device){
			
			//echo '<pre>'.print_r($device,true).'</pre>';
			
			if( is_array($device) ){
				//echo "set";
				$this->setID( $device['did'] );
				$this->setName( $device['name'] );
				$state = ($device['state'] == 1 ) ? true : false ;
				$this->setState(  $state );
				
				$brightness = ( isset(  $device['level'] ) ? $device['level'] : 0 );
				
				$this->setBrightness( $brightness );
				
				//echo $device['level'];
				
				$this->setDeviceType( ($device['prodtype'] == 'Light Fixture' ? 'light-fixture' : $device['prodtype'] ) );
				$this->setOnline(  isset( $device['offline'] ) ? 0 : 1  );
				$this->setColorID( $device['colorid'] );
				$this->setRemoteID( isset( $device['other']['rcgroup'] ) ? $device['other']['rcgroup'] : "" ); 
				
			}
		}
		
		function renderDevice(){
			
			echo '<div data-bridgeID="'.$this->getDeviceOwner().'" class="'.( $this->getOnline() == 0 ? 'unplugged' : 'plugged' ).' device '.( $this->getState() == 1 ? 'light-on' : 'light-off' ).' '.( $this->getDeviceType() == 'Light Fixture' ? 'light-fixture' : '' ).'" data-device-id="'.$this->getID().'">'; //power > 0 then enabled 
				//level = brightness
				//state = on or off
				echo '<p>'.$this->getName().'</p>';
				
				echo '<button data-device-id="'.$this->getID().'" class="onOffDeviceToggleButton buttonOn">On</button> | <button data-device-id="'.$this->getID().'" class="onOffDeviceToggleButton buttonOff">Off</button>';
				echo '<div class="clear"></div>';
				echo '<p>Brightness:</p>';
				echo '<div class="device-slider" data-value="'.( ( $this->getBrightness() ) ? $this->getBrightness() : 100).'" data-device-id="'. $this->getID().'"></div>';
			echo '</div>';
			
		}
		
		function getDeviceType(){
			return $this->deviceType;
		}
		
		function setDeviceType($type){
			$this->deviceType = $type;
		}
		
		function setOnline($state){
			$this->online = $state;
		}
		
		function getOnline(){
			return $this->online;
		}
		
		function setColorID($col){
			$this->colorID = $col;
		}
		
		function getColorID(){
			return $thos->colorID;
		}
		
		function setRemoteID($id){
			$this->remoteID = $id;
		}
		
		function getRemoteID(){
			return $thos->colorID;
		}
	}
	
	class tcp_room extends base_collection{
		
		
		/*
		[rid] => 0
		[name] => Kitchen 
		[desc] => Array
			(
			)

		[known] => 1
		[type] => 0
		[color] => 000000
		[colorid] => 0
		[img] => images/black.png
		[power] => 0
		[poweravg] => 0
		[energy] => 0
		[device] => Array
		*/
		
		public $color;
		public $colorID;
		
		
		function setColor($color){
			$this->color = $color;
		}
		
		function getColor(){
			return $this->color;
		}
		
		function setColorID($colorID){
			$this->colorID = $colorID;
		}
		
		function getColorID(){
			return $this->colorID;
		}
		
		function renderCollection(){
			echo '<div class="roomContainer" data-bridgeID="'.$this->getCollectionOwner().'" data-room-id="'.$this->getID().'">';
				echo '<h3>'.$this->getName().' ('.$this->getDeviceCount().')</h3>';
				if( $this->getDeviceCount() > 0 ){
					
					$brightness = 0;
					$devices = 0;
					
					echo '<div class="devices">';
						
						echo '<div class="room-devices">';
						foreach( $this->getDevices() as $device ){
							$device->renderDevice();
							$devices++;
							$brightness = $brightness +  ( $device->getBrightness() ? $device->getBrightness() : 100 ) ;
						}
						echo '</div>';
						
					echo '</div>';
					
					
					echo '<div class="room-controls">';
						
						echo 'Room Brightness: <div class="room-slider" data-value="'.($brightness/$devices).'" data-bridgeID="'.$this->getCollectionOwner().'" data-room-id="'. $this->getID() .'"></div>';
						echo 'Room <button data-room-id="'. $this->getID().'" class="onOffToggleButton buttonOn">On</button> | <button data-room-id="'. $this->getID().'" class="onOffToggleButton buttonOff">Off</button>';
					echo '</div>';
					
				}
			echo '</div>';
		
		}
		
		
	}
	
	class tcp_bridge extends base_bridge{
		public $hueEmulation;
		public $forceFadeON;
		public $forceFadeOFF;
	
		public function __construct(){
			$this->setName("TCP");
			$this->setClassName("TCP");
			$this->setID("TCP");
			$this->setRequiresHTTPS( true );
			$this->setRequiresToken( true );
			$this->setHueEmulation ( true );
		}			
		
		function runCommand($CMD){
			$SCHEME = "http";
			$PORT = 80;
			if( $this->getRequiresHTTPS() ){
				$SCHEME = "https";
				$PORT = 443;
			}
			$URL = $SCHEME."://".$this->getIP().":".$PORT."/gwr/gop.php";	
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $URL);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $CMD);
			if ($SCHEME == 'https') {
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			curl_close($ch);
			return $result;
		}
		
		function xmlToArray($xml){
			$xml = simplexml_load_string($xml);
			$json = json_encode($xml);
			$array = json_decode($json,TRUE);
			return $array;
		}
		
		//get/set token etc
		function init(){
			if( ! $this->getEnabled() ){
				return;
			}				
			
			if( $this->getRequiresToken() && file_exists( $this->getTokenPath() ) && file_get_contents( $this->getTokenPath() ) != "" ){
				$this->setToken( file_get_contents( $this->getTokenPath() ) );
			}else{
				//check if can generate
				$CMD = "cmd=GWRLogin&data=<gip><version>1</version><email>"."TCP_EMAIL"."</email><password>"."TCP_PASSWORD"."</password></gip>&fmt=xml";
				$result = $this->runCommand( $CMD );
				$array = $this->xmlToArray( $result );
				if( !isset($array["token"]) ){
					echo "<p>Need to Generate ".$this->getName()." token. Press Sync Button and retry</p>";
					return;
				}else{
					if( file_put_contents( $this->getTokenPath(), $array["token"]) ){
						$this->setToken( $array["token"] );
					}else{
						echo "<p>Could not write token file ".$this->getTokenPath()."</p>";
					}
				}
			}
			
			if( $this->getToken() != "" ){
				//Get System State
				$CMD = "cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>RoomGetCarousel</gcmd><gdata><gip><version>1</version><token>".$this->getToken()."</token><fields>name,image,imageurl,control,power,product,class,realtype,status</fields></gip></gdata></gwrcmd></gwrcmds>&fmt=xml";
				$result = $this->runCommand( $CMD );
				$array = $this->xmlToArray( $result );
				//ensure Token is good
				if( !isset($array["gwrcmd"]) ){
					echo '<p>GWR Command not returned, this likely indicates your token is expired, or invalid.<p>';
					$this->setToken( "" );
					file_put_contents( $this->getTokenPath(), "");
					return;
				}
				
				if( isset( $array["gwrcmd"]["gdata"]["gip"]["room"] ) ){
					$DATA = $array["gwrcmd"]["gdata"]["gip"]["room"];
				}else{
					echo "<p>".$this->getName()." has no 'Room' Data</p>";
					$DATA = array();
				}
				
				//Populate Rooms and devices...
				if( sizeof($DATA) > 0 ){
					foreach($DATA as $room){
						if( isset($room['rid'] ) ){
							$roomC = new tcp_room();
							$roomC->setID( $room['rid'] );
							$roomC->setName( $room["name"] );
							$roomC->setColor( $room['color'] );
							$roomC->setColorID( $room['colorid'] );
							$roomC->setCollectionOwner( $this->getID() ); //set owner
							
							if( is_array( $room["device"] ) ){
								
								$device = (array)$room["device"];
								
								//singular device
								if(  isset($device["did"]) ){
									//item is singular device
									$bdevice = new tcp_device();
									$bdevice->arrayPopulate( $room["device"] );
									$bdevice->setDeviceOwner( $roomC->getCollectionOwner() );
									$roomC->addDevice( $bdevice );
									$this->addDevice( $bdevice );
									
									
								}else{
									
									//array of devices
									for( $x = 0; $x < sizeof($device); $x++ ){
										if( isset($device[$x]) && is_array($device[$x]) && ! empty($device[$x]) ){
											
											$bdevice = new tcp_device();
											$bdevice->arrayPopulate( $device[$x] );
											$bdevice->setDeviceOwner( $roomC->getCollectionOwner() );
											$roomC->addDevice( $bdevice );
											$this->addDevice( $bdevice );
										}
									}
								}
							}
						}
						$this->addCollection( $roomC );	
					}
				}
			}	
		}
		
		function setHueEmulation($bool){
			//Make the lights fade on or off when turned on or off
			$this->hueEmulation = $bool;
			if( $bool ){
				$this->setFadeOn( $bool );
				$this->setFadeOff( $bool );
			}
		}
		
		function getHueEmulation(){
			return $this->hueEmulation;
		}
		
		function setFadeOn($bool){
			$this->fadeOn = $bool;
		}
		
		function getFadeOn(){
			return $this->fadeOff;
		}
		
		function setFadeOff($bool){
			$this->fadeOff = $bool;
		}
		
		function getFadeOff(){
			return $this->fadeoff;
		}
		
	
	}
	
	$bridge = new tcp_bridge();
	$bridge->setEnabled( true );		//SET TRUE
	$bridge->setIP("192.168.1.109"); 	//SET BRIDGE IP
	$bridge->setTokenPath( dirname(__FILE__).DIRECTORY_SEPARATOR.$bridge->getName().'_token' );
	$bridge->setHueEmulation( true );
	$bridge->init();
	
?>