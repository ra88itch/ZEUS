<?php
error_reporting(0);
define('R88PROJ',true);

session_start(); 

require('includes/responsed.php');
require('connect.php');
require('includes/function.php');

/*if(!isset($_GET['dev'])){
	chkAccessDevice($_SERVER['REMOTE_ADDR']);
}*/
$module = $_REQUEST['mod'];
$target = $_REQUEST['target'];

if(isset($module) && $module != ''){
	$module = strtolower ( $module );
	if(!isset($target) || $target == '') {	
		if(file_exists('modules/mod_'. $module .'/index.php')){
			require('modules/mod_'. $module .'/index.php');
		}else{
			die($system_error);
		}
	} else {
		if(file_exists('modules/mod_'. $module .'/'.$target.'.php')){
			require('modules/mod_'. $module .'/'.$target.'.php');
		} else {
			die($system_error);
		}
	}
}else{
	die($system_error);
}

?>