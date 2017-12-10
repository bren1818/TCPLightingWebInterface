<?php
include "include.php";
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
pageHeader("Log Viewer");
?>
<div class="container" style="padding: 20px; background-color: #fff; border: 1px solid #000;">
<?php 
if( LOG_ACTIONS == 1 || LOG_API_CALLS == 1){ 
	
	$curDir = dirname(__FILE__);
	//make the log directory relative to the web path.
	$REL_LOG_DIR = substr(LOG_DIR, strlen($curDir) + 1 );

	
?>
	<p>Logging Directory set to: <?php echo LOG_DIR; ?></p>
<?php	
	if( LOG_ACTIONS == 1 ){
	?>	
	<h1>Action Logs - schedule.actioned from <a href="scheduler.php">Scheduler</a></h1>
	<?php if( file_exists(LOG_DIR . DIRECTORY_SEPARATOR . "Schedule-Actioned.log") ){ ?>
		<iframe src="<?php echo $REL_LOG_DIR . '/' . "Schedule-Actioned.log"  ?>" style="width: 100%; height: 400px;"></iframe>
	
	<?php
		}else{
			echo '<p>No Schedule.actioned Log found</p>';
		}
	}
	
	if( LOG_API_CALLS == 1){
	?>
	<h1>API Logs - api.log</h1>
	<?php if( file_exists(LOG_DIR . DIRECTORY_SEPARATOR . "API-Request.log") ){ ?>
	<iframe src="<?php echo $REL_LOG_DIR . '/' . "API-Request.log"  ?>" style="width: 100%; height: 400px;"></iframe>
	
	<?php
		}else{
			echo '<p>No API File Log found</p>';
		}
	}
}else{
?>
	<p>You haven't enabled logging.</p>

<?php	
}
?>
</div>

<?php
pageFooter();
?>