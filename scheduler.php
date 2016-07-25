<?php include "include.php"; ?>
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
	<style>
		/* The switch - the box around the slider */
		.switch {
		  position: relative;
		  display: inline-block;
		  width: 60px;
		  height: 34px;
		}

		/* Hide default HTML checkbox */
		.switch input {display:none;}

		/* The slider */
		.slider {
		  position: absolute;
		  cursor: pointer;
		  top: 0;
		  left: 0;
		  right: 0;
		  bottom: 0;
		  background-color: #ccc;
		  -webkit-transition: .4s;
		  transition: .4s;
		}

		.slider:before {
		  position: absolute;
		  content: "";
		  height: 26px;
		  width: 26px;
		  left: 4px;
		  bottom: 4px;
		  background-color: white;
		  -webkit-transition: .4s;
		  transition: .4s;
		}

		input:checked + .slider {
		  background-color: #2196F3;
		}

		input:focus + .slider {
		  box-shadow: 0 0 1px #2196F3;
		}

		input:checked + .slider:before {
		  -webkit-transform: translateX(26px);
		  -ms-transform: translateX(26px);
		  transform: translateX(26px);
		}

		/* Rounded sliders */
		.slider.round {
		  border-radius: 34px;
		}

		.slider.round:before {
		  border-radius: 50%;
		}
		
		/****/
		
		.scheduledTask{
			margin: 20px 0px;
		}
		
		.scheduledTask  > div{ margin: 10px 0; }
		
		.fxTo{ width: 200px; }
		
		.ifDim,
		.ifSwitch{ display: none; }
		
		.scheduledTask.fx-Dim .ifDim{ display: block; }
		.scheduledTask.fx-Switch .ifSwitch{ display: block; }
		
		.scheduledTask { border: 1px solid #000; padding: 20px; }
		
	</style>
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script>
		$(function(){
			function runSchedule(){
				var d = new Date();
				console.log("Running Schedule " + d);
				$.get("/runSchedule.php", function( data ) {
					console.log(data);
				});
			}
			
			$('#runOnce').click(function(){
				runSchedule();
			});
			
			$('#poll').click(function(){
				$('#runOnce').attr('disabled', true);
				$(this).unbind();
				$(this).addClass('running');
				setInterval(runSchedule, 1000);
			});
		
		
		/* Output Add Scheduled Task */
		<?php
		
		?>	
		<?php ob_start(); ?>
		<div class="scheduledTask">
			<div class="daysOfWeek">
				<label><input type="checkbox" name="DAY_MON[]" /> Monday</label>
				<label><input type="checkbox" name="DAY_TUE[]" /> Tuesday</label>
				<label><input type="checkbox" name="DAY_WED[]" /> Wednesday</label>
				<label><input type="checkbox" name="DAY_THU[]" /> Thursday</label>
				<label><input type="checkbox" name="DAY_FRI[]" /> Friday</label>
				<label><input type="checkbox" name="DAY_SAT[]" /> Saturday</label>
				<label><input type="checkbox" name="DAY_SUN[]" /> Sunday</label>
				<label><input type="checkbox" name="DAY_ALL[]" /> Everyday</label>
			</div>
			<div class="timeOfDay">
				<label>Hour: 
					<select name="HOUR[]">
						<?php for($x=0; $x<=23; $x++){
							if( $x == 0 ){ 
								echo '<option value="'.$x.'">12 AM - MIDNIGHT</option>';
							}else if($x == 12){
								echo '<option value="'.$x.'">12 PM - NOON</option>';	
							}else{
								if( $x > 12 ){
									echo '<option value="'.$x.'">'. ($x - 12) .( $x >= 12 ? ' PM' : ' AM' ).'</option>';
								}else{
									echo '<option value="'.$x.'">'.$x.( $x >= 12 ? ' PM' : ' AM' ).'</option>';
								}
							}
						} ?>
					</select>
				</label>
				<label>Minute: <select name="MIN[]"><?php for($x=1; $x<=59; $x++){ echo '<option value="'.$x.'">'.sprintf("%02d",$x).'</option>'; } ?></select></label>
			</div>
			<div class="functionTrigger">
				<label>Function: <select name="FX"><option value="DIM">DIM</option><option value="SWITCH">SWITCH</option></select></label>
			</div>
			<div class="fxTo">
				<div class="ifDim">
					<div class="schedule-slider" data-device-id="all"></div>
					<input name="DIM_SCHED[]" type="hidden" value="" />
				</div>
				<div class="ifSwitch">
					<label class="switch">
					  <input name="SWITCH_SCHED[]" value="1" type="checkbox">
					  <div class="slider round"></div>
					</label>
				</div>
			</div>
			<div class="deviceList">
			<table>
				<tr>
					<td>Available</td><td>&nbsp;</td><td>Selected</td>
				</tr>
				<tr>
					<td>
						<select size="9"  name="available[]" multiple>
						<?php
							$devices = getDevices();
							foreach($devices as $device){
								echo '<option value="'.$device['did'].'">'.$device['prodtype'].' - '.$device['name'].'</option>';
							}
						?>
						</select>
					</td>
					<td>
						<input type="Button" value="Add >>" class="btnAdd"><br />
						<br />
						<input type="Button" value="<< Remove" class="btnRemove">
					</td>
					<td>
						<select size="9"  name="selected[]" multiple>
						<?php
						
						?>
						</select>
					</td>
				</tr>
			</table>
			
			</div>
		</div>
		<?php 
		$schedule = ob_get_clean();
		?>	
	
		var addSchedule = '<?php echo preg_replace( "/\r|\n/", "",$schedule); ?>';	
		
		function bindEvents(){
		
			$('select[name="FX"]').change(function(){
				$(this).closest('.scheduledTask').removeClass('fx-Dim');
				$(this).closest('.scheduledTask').removeClass('fx-Switch');
				
				if( $(this).find('option:selected').val() == "DIM" ){
					$(this).closest('.scheduledTask').addClass('fx-Dim');
				}else{
					$(this).closest('.scheduledTask').addClass('fx-Switch');
				}
			});
			
			$('.schedule-slider').slider({
				range: "min",
				min: 0,
				max: 100,
				//value: $(this).attr('data-level'),
				create: function( event, ui ){
					
				},
				stop: function(event, ui) {
					
				},
				slide: function( event, ui ) {
					
				}
			});
			
			$('select[name="FX"]').change();
		
		}
		
		$('#add').click(function(){
			console.log("Adding " + addSchedule);
			$('#events').append(addSchedule);
			bindEvents();
		});
		
		bindEvents();
	});
	</script>
</head>
<body>
<?php
	/*
	 *
	 * TCP Ligthing Web UI Test Script - By Brendon Irwin
	 * 
	 
	 */
 
	echo '<div id="toolBar"><a href="index.php">Lighting Controls</a> <a href="APITEST.php">API Test Zone</a></div>';
	
	echo '<div class="container">';
		echo '<h1>Device Schedule</h1>';
		echo '<button id="runOnce">Run Now</button> <button id="poll">Poll continuously</button>';
	echo '</div>';

	
 ?>
<div id="events" class="container"> 
	<?php
		echo $schedule;
	?>
 </div>
 <button id="save">Save</button>
 <button id="add">Add Task</button> 
 
 </body>
 </html>