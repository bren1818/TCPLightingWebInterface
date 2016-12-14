<?php
	/*
		Base Bridge Object
	*/
	
	class home{
		public $ip;
		public $home;
		public $devices;
		
		
		public function __construct(){
			$this->devices = [];
		}
		
		function getIP(){
			return $this->ip;
		}
		
		function setIP($ip){
			$this->ip = $ip;
		}		
		
		function getName(){
			return $this->name;
		}
		
		function setName($name){
			$this->name = $name;
		}
		
		function addBridge($bridge){
			$this->devices[] = $bridge;
		}
		
		function getDevices(){
			return $this->devices;
		}
		
		function setDevices($bridge){
			$this->devices = $bridge;
		}
		
	}
	
	
	class base_bridge{
		public $id;
		public $enabled;
		public $name;
		public $className;
		public $ip;
		
		public $requiresToken;
		public $requiresHTTPS;
		
		public $devices;
		public $collections;
		
		public $tokenPath;
		public $token;
		
		public $logActions;
		
		public function __construct(){
			$this->enabled = 0;
		}
		
		function getEnabled(){
			return $this->enabled;
		}
		
		function setEnabled($bool){
			$this->enabled = $bool;
		}			

		function getIP(){
			return $this->ip;
		}
		
		function setIP($ip){
			$this->ip = $ip;
		}		
		
		function getName(){
			return $this->name;
		}
		
		function setName($name){
			$this->name = $name;
		}
		
		function getClassName(){
			return $this->className;
		}
		
		function setClassName($name){
			$this->className = $name;
		}
		
		function getTokenPath(){
			return $this->tokenPath;
		}
		
		function setTokenPath($tokenPath){
			$this->tokenPath = $tokenPath;
		}
		
		function getRequiresHTTPS(){
			return $this->requiresHTTPS;
		}
		
		function setRequiresHTTPS($bool){
			$this->requiresHTTPS = $bool;
		}
		
		function getRequiresToken(){
			return $this->requiresToken;
		}
		
		function setRequiresToken($bool){
			$this->requiresToken = $bool;
		}
		
		function getToken(){
			return $this->token;
		}
		
		function setToken($token){
			$this->token = $token;
		}
		

		function addDevice($device){
			$this->devices[] = $device;
		}
		
		function getDevices(){
			return $this->devices;
		}
		
		function addCollection($collection){
			$this->collections[] = $collection;
		}
		
		function getCollection(){
			return $this->collections;
		}
		
		
		function init(){
			echo "This method should be overriden";
		}
		
		function setID($id){
			$this->id = md5($id);
		}
		
		function getID(){
			return $this->id;
		}
		
		function renderDevices(){
			//echo '<div class="roomContainer plugin-container">';
			
			//echo '<h3>'.$this->getName().' ('.sizeof($this->getDevices() ).') Devices</h3>'; 
			
			if( sizeof($this->getDevices() ) > 0 ){
				//echo '<div class="devices">';
			
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
							foreach( $this->getDevices() as $device ){
								$device->renderDevice();
							}
						echo '</div>';
						echo '</div>';
						echo '</div>';
						
						
						
				
						
					}
					
				//echo '</div>';
			}
			//echo '</div>';
			
		}
		
		function getLogActions(){
			return $this->logActions;
		}
		
		function setLogActions($bool){
			$this->logActions = $bool;
		}
		
		function turnDeviceOn( $ID ){ /*Override*/ }
		function turnDeviceOff( $ID ){ /*Override*/ }
		function dimDevice( $ID, $opacity){ /*Override*/ }
		function turnCollectionOn( $ID ){ /*Override*/}
		function turnCollectionOff( $ID ){ /*Override*/ }
		function dimCollection( $ID, $opacity){ /*Override*/ }
		function turnAllOn(){ /*Override*/ }
		function turnAllOff(){ /*Override*/ }
		function dimAll($opacity){ /*Override*/ }
		
	}

	/*
		Base Collection or container object / room object
	*/

	class base_collection{
		public $id;
		public $name;
		public $deviceCount;
		public $devices;
		public $collectionOwner; //bridge
		
		public function __construct(){
			$this->deviceCount = 0;
			$this->devices = array();
		}
		
		function getID(){
			return $this->id;
		}
		
		function setID($id){
			$this->id = $id;
		}
		
		function getName(){
			return $this->name;
		}
		
		function setName($name){
			$this->name = $name;
		}
		
		function setDeviceCount($count){
			$this->deviceCount = (int)$count;
		}
		
		function getDeviceCount(){
			return $this->deviceCount;
		}
		
		function addDevice($device){
			array_push( $this->devices,  $device );
			$this->setDeviceCount( sizeof($this->devices) );
		}
		
		function getDevices(){
			return $this->devices;
		}
		
		function getCollectionOwner(){
			return $this->collectionOwner;
		}
		
		function setCollectionOwner( $id ){
			$this->collectionOwner = $id;
		}
		
		
		function renderCollection(){
			//should be overridden...
			
			
			echo '<div class="roomContainer collection" data-collection-id="'.$this->getID().'">';
				echo '<h3>'.$this->getName().' ('.$this->getDeviceCount().')</h3>';
				
				if( $this->getDeviceCount() > 0 ){
					echo '<div class="devices">';
						
						foreach( $this->getDevices() as $device ){
							$device->renderDevice();
						}
						
					echo '</div>';
					
				}
			echo '</div>';
			
		}
		
	}

	
	
	
	
	/*
		Base Device object
	*/
	
	class base_device{
		public $id;
		public $name;
		public $brightness;
		public $state;
		public $deviceOwner; //bridge
		
		
		public function __construct(){
			$this->state = 0;
		}
		
		function getID(){
			return $this->id;
		}
		
		function setID($id){
			$this->id = $id;
		}
		
		function getName(){
			return $this->name;
		}
		
		function setName($name){
			$this->name = $name;
		}
		
		function setBrightness($level){
			if( $level >= 0 && $level <= 100){
				$this->brightness = $level;
			}else{
			
			}
		}
		
		function getBrightness(){
			return $this->brightness;
		}
		
		function getState(){
			return $this->state;
		}
		
		function setState($state){
			if( $state == 1 || $state == true ){
				$state = 1;
			}else{
				$state = 0;
			}
			$this->state = $state;
		}
		
		//override
		function renderDevice(){
			echo $this->getName().'<br />';
			
			/*
			echo '<div class="'.( (isset($device['offline']) && $device['offline'] == 1) ? 'unplugged' : 'plugged' ).' device '.($device['state'] == 1 ? 'light-on' : 'light-off' ).' '.($device['prodtype'] == 'Light Fixture' ? 'light-fixture' : '' ).'" data-device-id="'.$device['did'].'">'; //power > 0 then enabled 
			//level = brightness
			//state = on or off
				echo '<p>'.$device['name'].' <a href="info.php?did='.$device['did'].'"><img src="/images/info.png"/></a></p>';
				echo '<button data-device-id="'.$device['did'].'" class="onOffDeviceToggleButton buttonOn">On</button> | <button data-device-id="'.$device['did'].'" class="onOffDeviceToggleButton buttonOff">Off</button>';
				echo '<div class="clear"></div>';
				echo '<p>Brightness:</p>';
				echo '<div class="device-slider" data-value="'.(isset($device['level']) ? $device['level'] : 100).'" data-device-id="'. $device["did"].'"></div>';
			echo '</div>';
			
			*/
			
			
		}
		
		function getDeviceOwner(){
			return $this->deviceOwner;
		}
		
		function setDeviceOwner( $id ){
			$this->deviceOwner = $id;
		}
		
	}
	
	
	
?>