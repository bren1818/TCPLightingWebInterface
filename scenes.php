<?php
	/*
	 *
	 * TCP Ligthing Web UI Scenes Script - By Brendon Irwin
	 * 
	 */

	include "include.php";

	global $REMOTE_IP;
	
	if( REQUIRE_EXTERNAL_API_PASSWORD && ! isLocalIPAddress($REMOTE_IP)){
		$password = 		isset($_REQUEST['password']) ? $_REQUEST['password'] : "";		//passed password
		if( $password != EXTERNAL_API_PASSWORD ){
			//invalid password
			echo "Invalid API Password";
			exit;
		}
	}
	
	if( RESTRICT_EXTERNAL_PORT == 1 && ! isLocalIPAddress($REMOTE_IP) ){
		if( $_SERVER['SERVER_PORT'] != EXTERNAL_PORT ){
			echo "Invalid Port";	
			exit;
		}
	}
	
	
	if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
		$method = "POST";
		
		$sceneID = $_POST['scene'];
		$action = $_POST['action'];
	}
	
	if (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET'){
		$method = "GET";
		if( isset($_REQUEST) && isset($_REQUEST['scene']) ){
			$sceneID = $_REQUEST['scene'];
			$action = $_REQUEST['action'];
		}
	}
	
	if( isset($method) ){
		
		
		//run scene
		if( isset($sceneID) && $sceneID != "" ){
			
			if( $action == "on"){
				$CMD = "cmd=SceneRun&data=<gip><version>1</version><token>".TOKEN."</token><sid>".$sceneID."</sid><val>1</val></gip>"; //val 1 is on 0 is off
			}else if( $action == "off"){
				$CMD = "cmd=SceneRun&data=<gip><version>1</version><token>".TOKEN."</token><sid>".$sceneID."</sid><val>0</val></gip>"; //val 1 is on 0 is off
			}else if($action == "delete"){
				if(  isLocalIPAddress($REMOTE_IP) ){
					$CMD = "cmd=SceneDelete&data=<gip><version>1</version><token>".TOKEN."</token><sid>".$sceneID."</sid></gip>"; 
				}else{
					//cant remotely delete scenes.
					
					
				}
				
			}
			
			$result = getCurlReturn($CMD);
			$array = xmlToArray($result);
			echo json_encode( array("scene" => $sceneID, "return" => $array) );
			exit;
		}
		
		
	}

	pageHeader("TCP Lighting - Scene Controller");
?>

<div id="toolBar"><a href="index.php">Lighting Controls</a> | <a href="scheduler.php">Lighting Scheduler</a> | <a href="apitest.php">API Test Zone</a></div>
<div class="container">
<script>
	$(function(){
		$('.activateScene').click(function(event){
			event.preventDefault();
			var sceneID = $(this).data("scene");
			$.post( "scenes.php", { scene: sceneID, action: 'on' })
			  .done(function( data ) {
				console.log( "Response " + data );
			});
		});
		
		$('.deactivateScene').click(function(event){
			event.preventDefault();
			var sceneID = $(this).data("scene");
			$.post( "scenes.php", { scene: sceneID, action: 'off' })
			  .done(function( data ) {
				console.log( "Response " + data );
			});
		});
		
		$('.deleteScene').click(function(event){
			event.preventDefault();
			var sceneID = $(this).data("scene");
			if (window.confirm("Are you sure?")) {
				$.post( "scenes.php", { scene: sceneID, action: 'delete' })
				  .done(function( data ) {
					console.log( "Response " + data );
					location.reload();
				});
			}
		});
		
		$('.editScene').click(function(event){
			event.preventDefault();
			var sceneID = $(this).data("scene");
			window.location = "scenescreatedit.php?SID=" + sceneID;
		});
		
	});
</script>	
<?php
	echo '<h2>Scenes / Smart Control</h2>';
	
	$CMD = "cmd=SceneGetListDetails&data=<gip><version>1</version><token>".TOKEN."</token><bigicon>1</bigicon></gip>";
	$result = getCurlReturn($CMD);
	$array = xmlToArray($result);
	
	$scenes = $array["scene"];
	if( is_array($scenes) ){
		for($x = 0; $x < sizeof($scenes); $x++){
			?>
			<div class="" id="scene-id-<?php echo $scenes[$x]["sid"]; ?>">
				<h3><?php echo $scenes[$x]["name"]; ?></h3>
				<img src="<?php echo IMAGE_PATH.$scenes[$x]["icon"]; ?>" />
				<p>
					<?php /*echo "Active: " . $scenes[$x]["active"]; */  echo "Scene ID: ". $scenes[$x]["sid"]; ?><br />
					Devices: <?php echo is_array($scenes[$x]["device"]) ? sizeof($scenes[$x]["device"]) : ""; ?>
					<?php
					/* Type R = Room, D = device*/
					
					
					?>
					
				</p>
				<button data-scene="<?php echo $scenes[$x]["sid"]; ?>" class="activateScene">Activate</button>
				<button data-scene="<?php echo $scenes[$x]["sid"]; ?>" class="deactivateScene">De-Activate</button>
				<button data-scene="<?php echo $scenes[$x]["sid"]; ?>" class="editScene">Edit</button> 
				<button data-scene="<?php echo $scenes[$x]["sid"]; ?>" class="deleteScene">Delete</button>
			</div>
			<?php
			
			
			
		}
		
	}
	
	//pa($array);
?>
<br />
<a href="scenescreatedit.php">Create Scene</a>

<p>API Calls are structured like so: http://{{your-web-address}}:{{port}}/scenes.php?scene={{Scene Number}}&action={{on|off}}&password={{api-password}}
<p>**Please note, if images above are not showing open this <a href="<?php echo IMAGE_PATH.$scenes[sizeof($scenes) - 1]["icon"] ?>">link</a> to your bridge and accept the expired certificate.</p>
</div>
<?php
pageFooter();
?>