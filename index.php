<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<?php
error_reporting(0);
define('R88PROJ',true);

session_start(); 

if(isset($_GET['logout'])){
	session_destroy(); 
	header('Location: http://'.$_SERVER['HTTP_HOST']);
}
require('includes/responsed.php');
require('connect.php');
require('includes/function.php');
function getMassagePrice($zone_category){
	$sql = "SELECT * FROM `zone_category` WHERE `id`='".$zone_category."'";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	$response = 'var msgPrice'.$zone_category.' = '.$results['charge'].';';
	return $response;
}

if(!isset($_GET['dev'])){
	//chkAccessDevice($_SERVER['REMOTE_ADDR']);
	//echo 'chkAccessDevice('.$_SERVER['REMOTE_ADDR'].')';
}
if(isset($_GET['mod'])){
$module = $_GET['mod'];
}
if(isset($_GET['target'])){
$target = $_GET['target'];
}
//$target = $_GET['target'];
if(isset($_SESSION['login'])){
	/*if($_SESSION['user_name']=='kitchen'){
		$module = 'k_'.$module;
	}*/
	switch($_SESSION['user_name']){
		case 'kitchen':
			$module = 'k_'.$module;
			break;
		case 'boxing':
			$module = 'b_'.$module;
			break;
	}
	if(isset($module) && $module != ''){
		if(file_exists('modules/mod_'. $module .'/'.$module.'.php')){
			$module = strtolower ( $module );
		}else{
			$module = 'dashboard';
			/*if($_SESSION['user_name']=='kitchen'){
				$module = 'k_'.$module;
			}*/
			switch($_SESSION['user_name']){
				case 'kitchen':
					$module = 'k_'.$module;
					break;
				case 'boxing':
					$module = 'b_'.$module;
					break;
			}
		}			
	}else{
		$module = 'dashboard';
		/*if($_SESSION['user_name']=='kitchen'){
			$module = 'k_'.$module;
		}*/
		switch($_SESSION['user_name']){
			case 'kitchen':
				$module = 'k_'.$module;
				break;
			case 'boxing':
				$module = 'b_'.$module;
				break;
		}
	}
	
}else{
	$module = 'login';
}

if(file_exists('languages/mod_'. $module .'.php')){
	require('languages/mod_'. $module .'.php');
}
if(file_exists('css/mod_'. $module .'.css')){
	$importCss		= '<link rel="stylesheet" href="css/mod_'. $module .'.css?v15"/>';
}
if(file_exists('js/mod_'. $module .'.js')){
	$importScript	= '<script type="text/javascript" src="js/mod_'.$module.'.js?v15"></script>';
}	
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="Author" content="natt@ra88itch.com">

<title><?php if(isset($txt_site_title)) {echo $txt_site_title;}else{ echo 'STAR THUNDER';} ?></title>
<link type="text/css" rel="stylesheet" href="css/style.css">
<link type="text/css" rel="stylesheet" href="css/icon.css">
<link type="text/css" rel="stylesheet" href="css/pop.css">
<link type="text/css" rel="stylesheet" href="css/ui-lightness/jquery-ui-1.8.10.custom.css">	
<?php if($importCss!=''){ echo $importCss; } ?>
<?php
if($module=='employee'){
	echo '<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>';
}else{
	echo '<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.10.offset.datepicker.min.js"></script>';
}
?>
<script type="text/javascript" src="js/script.js"></script>
<script type="text/javascript">
	<?php echo getMassagePrice('1'); ?>
	<?php echo getMassagePrice('2'); ?>
	<?php echo getMassagePrice('12'); ?>
	<?php echo getMassagePrice('13'); ?>
</script>
</head>
<body>
<?php
if(isset($_SESSION['login'])){
	require('includes/header.php');
	if(isset($target) && $target != '') {
		require('modules/mod_'.$module.'/'.$target.'.php');
	} else {
		require('modules/mod_'. $module .'/'.$module.'.php');
	}
	require('includes/footer.php');
}else{
	require('modules/mod_login/login.php');
}
?>
	<?php if($importScript!=''){ echo $importScript; } ?>
</body>
</html>