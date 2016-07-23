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
 </body>
 </html>