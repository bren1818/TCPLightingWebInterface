<?php
	/*
		Base Bridge Object
	*/
	class base_bridge{
		
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
		
		function addCollection($collection){
			$this->collections[] = $collection;
		}
		
		
		
		
		function init(){
			echo "This method should be overriden";
		}
		
		
		function renderDevices(){
		
		}
		
		function renderDevice($ID){
		
		}
		
	
	}

	/*
		Base Collection or container object / room object
	*/

	class base_collection{
		public $id;
		public $name;
		public $deviceCount;
		public $devices;
		
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
		
	}

	
	
	
	
	/*
		Base Device object
	*/
	
	class base_device{
		public $id;
		public $name;
		public $brightness;
		public $state;
		
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
			if( $level > 0 && $level < 100){
				$this->level = $level;
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
			if( $state == 1 or $state == true ){
				$state = 1;
			}else{
				$state = 0;
			}
		}
		
	}
	
	
	
?>