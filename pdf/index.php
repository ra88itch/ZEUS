<?php
define('R88PROJ',true);

session_start(); 

require('../connect.php');
//require('functionCode.php');

$report = $_GET['report'];
if(isset($_SESSION['login'])){
	if(isset($report) && $report != ''){
		$file = '_'.$report.'.php';
		if(file_exists($file)){
			require_once($file);
		}else{
			echo $file;
		}		
	}else{
		header("Location: http://192.168.1.250?logout");
	}
}else{
	header("Location: http://192.168.1.250");
}
?>