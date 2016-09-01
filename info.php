<?php
/*
 *
 * TCP Ligthing Web UI Info Script - By Brendon Irwin
 * 
 */

include "include.php";

if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
	if(isset($_POST['did']) && $_POST['did'] != "" ){
		$did = $_POST['did'];
		$name = $_POST['name'];
		$color = $_POST['color'];
		$imdata = "";
		
		if( isset($_FILES["image"]) && $_FILES["image"]["tmp_name"] != "" ){ 	
			$imageFileType = pathinfo( basename($_FILES["image"]["name"]) ,PATHINFO_EXTENSION);
			$check = getimagesize($_FILES["image"]["tmp_name"]);
			if($check !== false) {
				if ($_FILES["image"]["size"] > 500000) {
					echo "Sorry, your file is too large.";
				}else{
					if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
						echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
					}else{
						//needs to be 100 x 100?
						$imdata = base64_encode( file_get_contents($_FILES["image"]["tmp_name"] ) );      
						//echo $imdata;
						echo '<p><img src="data:image/png;base64,'.$imdata.'" /></p>';
					//	$imdata = "data:image/png;base64,".$imdata;
					//	$imdata = htmlentities($imdata);
					}
				}
			}
		}		
		
		
		
		$CMD = "cmd=DeviceSetInfo&data=<gip><version>1</version><token>".TOKEN."</token><did>".$did."</did><name>".$name."</name><color>".$color."</color>".($imdata != "" ? "<image>".$imdata."</image>" : "")."</gip>";
		
		echo htmlentities($CMD);
		$result = getCurlReturn($CMD);
		pa( $result );
		$array = xmlToArray($result);
		pa( $array );
		
		$_REQUEST['did'] = $did;
	}
}


if( isset($_REQUEST['did']) && $_REQUEST['did'] != "" ){
	$did = $_REQUEST['did'];
	echo '<h2>Device Info</h2>';
	echo '<p><b>Device ID:'.$did.'</b></p>';
	$CMD = "cmd=DeviceGetInfo&data=<gip><version>1</version><token>".TOKEN."</token><did>".$did."</did><fields>name,power,product,class,image,control,realtype,other,status</fields></gip>";
	
	$result = getCurlReturn($CMD);
	$array = xmlToArray($result);
	if( isset( $array["image"] ) && ! is_array( $array["image"]) ){
		echo '<p><img src="data:image/png;base64,'.$array["image"].'" /></p>';
	}
	
	pa( $array );
	?>
	<form method="post" action="info.php" enctype="multipart/form-data">
		<fieldset>
			<legend>Update Device</legend>
		<label for="name">Name: <input name="name" id="name" value="<?php echo $array["name"] ?>" /></label><br />
		<label for="image">Image: <input type="file" name="image" id="image"></label><br />
		<label for="color">Color: 
			<select name="color">
				<option value="0" <?php echo ($array["colorid"] == 0 ? "selected" : ""); ?>>Black</option>
				<option value="1" <?php echo ($array["colorid"] == 1 ? "selected" : ""); ?>>Green</option>
				<option value="2" <?php echo ($array["colorid"] == 2 ? "selected" : ""); ?>>Dark Blue</option>
				<option value="3" <?php echo ($array["colorid"] == 3 ? "selected" : ""); ?>>Red</option>
				<option value="4" <?php echo ($array["colorid"] == 4 ? "selected" : ""); ?>>Yellow</option>
				<option value="5" <?php echo ($array["colorid"] == 5 ? "selected" : ""); ?>>Purple</option>
				<option value="6" <?php echo ($array["colorid"] == 6 ? "selected" : ""); ?>>Orange</option>
				<option value="7" <?php echo ($array["colorid"] == 7 ? "selected" : ""); ?>>Light Blue</option>
				<option value="8" <?php echo ($array["colorid"] == 8 ? "selected" : ""); ?>>Pink</option>
			</select>
		</label><br />
		<input type="hidden" name="did" value="<?php echo $did; ?>" /><br />
		<input type="submit" value="save" />
		</fieldset>
	</form>
	
	<?php
	
}
	
if( isset($_REQUEST['rid']) && $_REQUEST['rid'] != "" ){	
	$rid = $_REQUEST['rid'];
	echo '<h2>Room Information</h2>';
	echo '<p><b>Room ID:'.$rid.'</b></p>';
	
	$CMD = "cmd=RoomGetInfoAll&data=<gip><version>1</version><token>".TOKEN."</token><rid>".$rid."</rid><fields>name,power,product,class,image,imageurl,control,other</fields></gip>";
	
	$result = getCurlReturn($CMD);
	$array = xmlToArray($result);
	
	pa( $array );
}
?>