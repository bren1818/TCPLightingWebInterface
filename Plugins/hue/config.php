<?php

	$cwd = dirname( dirname(__FILE__) );
	require_once $cwd.DIRECTORY_SEPARATOR."base_bridge.php";
	
	class hue_bridge extends base_bridge{
		
		public function __construct(){
			$this->requiresToken = true;
			$this->supportsColor = true;
			$this->requiresHTTPS = false;
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
						//hue
						//sat
						
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
			 $this->runCommand('/api/'.$this->getToken().'/lights/'.$ID.'/state', '{"on":true, "sat":254, "bri":'.intval( ($opacity / 100) * 255 ).',"hue":10000}', "PUT");
		}
		
		
		
		
		function turnCollectionOn( $ID ){ /*Override*/}
		function turnCollectionOff( $ID ){ /*Override*/ }
		function dimCollection( $ID, $opacity){ /*Override*/ }
		
		function turnAllOn(){ /*Override*/ 
			for($x=1; $x<= sizeof($this->getDevices()); $x++){
				$this->runCommand('/api/'.$this->getToken().'/lights/'.$x.'/state', '{"on":true}', "PUT");
			}
		}
		
		function turnAllOff(){ /*Override*/ 
			for($x=1; $x<= sizeof($this->getDevices()); $x++){
				$this->runCommand('/api/'.$this->getToken().'/lights/'.$x.'/state', '{"on":false}', "PUT");
			}
		}
		
		
		function dimAll($opacity){ /*Override*/ }
		
		
	}
	
	class hue_device extends base_device{
		public $hueID;
		public $deviceype;
		
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
	
	
		function renderDevice(){
			
			echo '<div data-bridgeID="'.$this->getDeviceOwner().'" class="'.( $this->getState() == 0 ? 'unplugged' : 'plugged' ).' device '.( $this->getState() == 1 ? 'light-on' : 'light-off' ).' '.( $this->getDeviceType() == 'Light Fixture' ? 'light-fixture' : '' ).'" data-device-id="'.$this->getID().'">'; //power > 0 then enabled 
				//level = brightness
				//state = on or off
				echo '<p>'.$this->getName().'</p>';
				
				echo '<button data-device-id="'.$this->getID().'" class="onOffDeviceToggleButton buttonOn">On</button> | <button data-device-id="'.$this->getID().'" class="onOffDeviceToggleButton buttonOff">Off</button>';
				echo '<div class="clear"></div>';
				echo '<p>Brightness:</p>';
				echo '<div class="device-slider" data-value="'.( ( $this->getBrightness() ) ? $this->getBrightness() : 100).'" data-device-id="'. $this->getID().'"></div>';
			echo '</div>';
			
		}
	
	
	
	
	
	
	
	
	}
	

	

?>