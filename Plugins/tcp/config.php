<?php

	$cwd = dirname( dirname(__FILE__) );
	require_once $cwd.DIRECTORY_SEPARATOR."base_bridge.php";
	
	class tcp_device extends base_device{
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
			if( is_array($device) ){
				$this->setID( $device['did'] );
				$this->setName( $device['name'] );
				$this->setState( ($device['state'] == 1 ? 1 : 0 ) );
				$this->setBrightness( (isset($device['level']) ? $device['level'] : 0) );
				$this->setDeviceType( ($device['prodtype'] == 'Light Fixture' ? 'light-fixture' : '' ) );
				$this->setOnline(  ( isset($device['offline']) && $device['offline'] == 1 ) ? 0 : 1  );
				$this->setColorID( $device['colorid'] );
				$this->setRemoteID( isset( $device['other']['rcgroup'] ) ? $device['other']['rcgroup'] : "" ); 
				
			}
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
	}
	
	class tcp_bridge extends base_bridge{
	
	
		public function __construct(){
			$this->setName("TCP");
			$this->setClassName("TCP-2");
			$this->setRequiresHTTPS( true );
			$this->setRequiresToken( true );
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

							if( is_array( $room["device"] ) ){
								$device = (array)$room["device"];
								//singular device
								if(  isset($device["did"]) ){
									//item is singular device
									$bdevice = new tcp_device();
									$bdevice->arrayPopulate( $room["device"] );
									
									$roomC->addDevice( $bdevice );
									$this->addDevice( $bdevice );
								}else{
									//array of devices
									for( $x = 0; $x < sizeof($device); $x++ ){
										if( isset($device[$x]) && is_array($device[$x]) && ! empty($device[$x]) ){
											$bdevice = new tcp_device();
											$bdevice->arrayPopulate( $device[$x] );
											
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
	}
	
	$bridge = new tcp_bridge();
	$bridge->setEnabled( false );		//SET TRUE
	$bridge->setIP("192.168.1.TCP"); 	//SET BRIDGE IP
	$bridge->setTokenPath( dirname(__FILE__).DIRECTORY_SEPARATOR.$bridge->getName().'_token' );
	$bridge->init();
	
?>