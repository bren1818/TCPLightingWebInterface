<?php
	/*
		Plugins Script
	*/
	$cwd = dirname(__FILE__); 
	$dirs = array_filter( glob( $cwd.DIRECTORY_SEPARATOR.'*' ), 'is_dir');
	$plugins = array();
	
	if( sizeof($dirs) > 0 ){
		foreach($dirs as $dir){ 
			//echo $dir;
			$file = $dir.DIRECTORY_SEPARATOR."config.php";
			if( file_exists( $file ) ){
				include $file;
				$plugins[] = $bridge;
			}
		}
	}
	
	
	

?>