<?php
	/*
	 *
	 * TCP Ligthing Web UI Scenes Create/Edit Script - By Brendon Irwin
	 * 
	 */

	include "include.php";

	if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
		
		exit;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>TCP Control Script</title>
	<link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" href="favicons/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="favicons/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="favicons/manifest.json">
	<link rel="mask-icon" href="favicons/safari-pinned-tab.svg" color="#5bbad5">
	<link rel="shortcut icon" href="favicons/favicon.ico">
	<meta name="apple-mobile-web-app-title" content="TCP Lighting">
	<meta name="application-name" content="TCP Lighting">
	<meta name="msapplication-config" content="favicons/browserconfig.xml">
	<meta name="theme-color" content="#ffffff">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" href="style.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script src="js/jquery.ui.touch-punch.min.js"></script>
	<script src="scripts.js"></script>
</head>
<body>
<div id="toolBar"><a href="index.php">Lighting Controls</a> | <a href="scheduler.php">Lighting Scheduler</a> | <a href="scenes.php">Scenes/Smart Control</a> | <a href="apitest.php">API Test Zone</a></div>
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
					window.location = "scenes.php";
				});
			}
		});
	});
</script>	
<?php
	echo '<h2>Scenes / Smart Control</h2>';
	
	$CMD = "cmd=SceneGetListDetails&data=<gip><version>1</version><token>".TOKEN."</token><bigicon>1</bigicon></gip>";
	$result = getCurlReturn($CMD);
	$array = xmlToArray($result);
	
	$scenes = $array["scene"];
	if( is_array($scenes) && isset($_REQUEST['SID']) && $_REQUEST['SID'] != ""){
		$scene =  $_REQUEST['SID'];
		for($x = 0; $x < sizeof($scenes); $x++){
			if($scenes[$x]["sid"] == $scene ){
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
				<!-- <button data-scene="<?php echo $scenes[$x]["sid"]; ?>" class="editScene">Edit</button> -->
				<button data-scene="<?php echo $scenes[$x]["sid"]; ?>" class="deleteScene">Delete</button>
			</div>
			<?php
				echo '<h2>Data Dump of Scene</h2>';
				pa($scenes[$x]);
				echo '<p>No functions to edit yet. Sorry!</p>';
			
			}
		}
		
	}else{
		?>
		<h2>Create Scene</h2>
		<p>Feature not built yet... sorry!</p>
		<?php
	}
	
	
?>
</body>
</html>