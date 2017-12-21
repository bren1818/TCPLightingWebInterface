<?php
include "include.php";
pageHeader("TCP Lighting Controller");

if( TOKEN != "" ){
?>
<div class="container" style="padding: 20px; background-color: #fff;">
<h3>Set Date Time</h3><br />
<?php	
	if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
		
		if( isset($_POST) && isset($_POST['dateTime']) && $_POST['dateTime'] != "" ){
		$time = $_POST['dateTime'];
			$CMD = "cmd=AccountSetDateTime&data=<gip><version>1</version><token>".TOKEN."</token><datetime>".$time.":00</datetime></gip>";
			$result = getCurlReturn($CMD);
			$array = xmlToArray($result);
			pa($array);
		}
		
	}
	
	//Get State of System Data
	$CMD = "cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>AccountGetExtras</gcmd><gdata><gip><version>1</version><token>".TOKEN."</token></gip></gdata></gwrcmd></gwrcmds>&fmt=xml";
	$result = getCurlReturn($CMD);

	$array = xmlToArray($result);

	$time = $array["gwrcmd"]["gdata"]["gip"]["datetime"];
	
	//Get State of System Data
	
	?>
	<style>
		.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
		.ui-timepicker-div dl { text-align: left; }
		.ui-timepicker-div dl dt { float: left; clear:left; padding: 0 0 0 5px; }
		.ui-timepicker-div dl dd { margin: 0 10px 10px 40%; }
		.ui-timepicker-div td { font-size: 90%; }
		.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
		.ui-timepicker-div .ui_tpicker_unit_hide{ display: none; }

		.ui-timepicker-div .ui_tpicker_time .ui_tpicker_time_input { background: none; color: inherit; border: none; outline: none; border-bottom: solid 1px #555; width: 95%; }
		.ui-timepicker-div .ui_tpicker_time .ui_tpicker_time_input:focus { border-bottom-color: #aaa; }

		.ui-timepicker-rtl{ direction: rtl; }
		.ui-timepicker-rtl dl { text-align: right; padding: 0 5px 0 0; }
		.ui-timepicker-rtl dl dt{ float: right; clear: right; }
		.ui-timepicker-rtl dl dd { margin: 0 40% 10px 10px; }

		/* Shortened version style */
		.ui-timepicker-div.ui-timepicker-oneLine { padding-right: 2px; }
		.ui-timepicker-div.ui-timepicker-oneLine .ui_tpicker_time, 
		.ui-timepicker-div.ui-timepicker-oneLine dt { display: none; }
		.ui-timepicker-div.ui-timepicker-oneLine .ui_tpicker_time_label { display: block; padding-top: 2px; }
		.ui-timepicker-div.ui-timepicker-oneLine dl { text-align: right; }
		.ui-timepicker-div.ui-timepicker-oneLine dl dd, 
		.ui-timepicker-div.ui-timepicker-oneLine dl dd > div { display:inline-block; margin:0; }
		.ui-timepicker-div.ui-timepicker-oneLine dl dd.ui_tpicker_minute:before,
		.ui-timepicker-div.ui-timepicker-oneLine dl dd.ui_tpicker_second:before { content:':'; display:inline-block; }
		.ui-timepicker-div.ui-timepicker-oneLine dl dd.ui_tpicker_millisec:before,
		.ui-timepicker-div.ui-timepicker-oneLine dl dd.ui_tpicker_microsec:before { content:'.'; display:inline-block; }
		.ui-timepicker-div.ui-timepicker-oneLine .ui_tpicker_unit_hide,
		.ui-timepicker-div.ui-timepicker-oneLine .ui_tpicker_unit_hide:before{ display: none; }
	</style>
	<script>
		$(function(){
			$('#datetimepicker').datetimepicker({
				inline:false,
				dateFormat: "yy-mm-dd",
				timeFormat: "HH:mm:ss",
				Separator: " "
			});
		});
	</script>
	<form method="post" action="setDateTime.php">
	<p>Keep in mind that the bridge uses UTC time. Based on your timezone, you should probably set the time to: <?php echo gmdate("Y-m-d H:i:s"); ?></p>
		Set Time: <input id="datetimepicker" name="dateTime" type="text" value="<?php echo $time; ?>"/>
		<input type="submit" value="Save" />
	</form>
	<?php
	echo '<br /><h3>Other Info</h3><br /><hr /><br />';
	
	
	
	$CMD = "cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>AccountGetExtras</gcmd><gdata><gip><version>1</version><token>".TOKEN."</token></gip></gdata></gwrcmd></gwrcmds>&fmt=xml";
	$result = getCurlReturn($CMD);
	$array = xmlToArray($result);
	$time = $array["gwrcmd"]["gdata"]["gip"]["datetime"];
	echo "<p>Bridge Time: " . $time. "</p>";
	
	
	$CMD = "cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>GatewayGetInfo</gcmd><gdata><gip><version>1</version><token>".TOKEN."</token></gip></gdata></gwrcmd></gwrcmds>&fmt=xml";
	$result = getCurlReturn($CMD);
	$array = xmlToArray($result);
	$array =  $array["gwrcmd"]["gdata"]["gip"]["gateway"];
	pa($array);
	
	
}
?>
</div>
<?php
pageFooter();
?>
