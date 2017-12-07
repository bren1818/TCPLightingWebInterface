<?php
	/*
	 *
	 * TCP Ligthing Web UI Scenes Create/Edit Script - By Brendon Irwin
	 * 
	 */

	include "include.php";
	pageHeader("TCP Lighting - Scene Controller");
	if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
		
		exit;
	}
?>
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
	echo '<div class="roomContainer">';
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
			<div class="scene-container" id="scene-id-<?php echo $scenes[$x]["sid"]; ?>">
                	<!--<div class="scene-info"><a href="scenescreatedit.php?SID=<?php echo $scenes[$x]["sid"]; ?>"><img src="images/info.png"/></a></div>-->
					<p><b><?php echo $scenes[$x]["name"]; ?></b> (<?php echo is_array($scenes[$x]["device"]) ? sizeof($scenes[$x]["device"]) : ""; ?>)</p>
					<p><img src="css/<?php echo $scenes[$x]["icon"]; ?>" /></p>
					<p>
                        <button data-scene-mode="run" data-scene-id="<?php echo $scenes[$x]["sid"]; ?>" class="runScene">Run Scene</button> 
                        <button data-scene-mode="off" data-scene-id="<?php echo $scenes[$x]["sid"]; ?>" class="runScene">Scene Devices Off</button> 
                        <button data-scene-mode="on" data-scene-id="<?php echo $scenes[$x]["sid"]; ?>" class="runScene">Scene Devices On</button>
                    </p>
			</div>
            <div class="clear"></div>
            <div style="padding: 20px;">    
			<?php
				echo '<h2>Data Dump of Scene</h2>';
				pa($scenes[$x]);
				echo '<p>No functions to edit yet. Sorry!</p>';
				echo '</div>';
			}
		}
		
	}else{
		?>
        <div style="padding: 20px;"> 
		<h2>Create Scene</h2>
		<p>Feature not built yet... sorry!</p>
        </div>
		<?php
	}
	echo '</div>';
pageFooter();
?>