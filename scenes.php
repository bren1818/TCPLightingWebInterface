<?php
	/*
	 *
	 * TCP Ligthing Web UI Scenes Script - By Brendon Irwin
	 * 
	 */

	include "include.php";

	if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
		//run scene
		if( isset($_POST['scene']) && $_POST['scene'] != "" ){
			$sceneID = $_POST['scene'];
			$action = $_POST['action'];
			if( $action == "on"){
				$CMD = "cmd=SceneRun&data=<gip><version>1</version><token>".TOKEN."</token><sid>".$sceneID."</sid><val>1</val></gip>"; //val 1 is on 0 is off
			}else if( $action == "off"){
				$CMD = "cmd=SceneRun&data=<gip><version>1</version><token>".TOKEN."</token><sid>".$sceneID."</sid><val>0</val></gip>"; //val 1 is on 0 is off
			}else if($action == "delete"){
				$CMD = "cmd=SceneDelete&data=<gip><version>1</version><token>".TOKEN."</token><sid>".$sceneID."</sid></gip>"; 
			}
			
			$result = getCurlReturn($CMD);
			$array = xmlToArray($result);
			echo json_encode( array("scene" => $sceneID, "return" => $array) );
		}
		exit;
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
					Active: <?php echo $scenes[$x]["active"];  ?><br />
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
</div>
<?php
pageFooter();
?>