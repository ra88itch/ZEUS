<?php
defined('R88PROJ') or die ($system_error);

$login_username = $_REQUEST['username'];
$login_password = $_REQUEST['password'];

function chkLogin($username, $password){
	updateMenuPrice();
	$username = mysql_real_escape_string($username);
	$password = mysql_real_escape_string(md5($password));
	$sql = "SELECT *, COUNT(*) AS `count` FROM `account` WHERE `username`='$username' AND `password`='$password' AND `active`='1' LIMIT 1";
	$query = mysql_query($sql);
	$results = mysql_fetch_assoc($query);
	if( $results['count']==0 ){
		global $system_status_failed;
		$json_arr = array(
			'process' => $system_status_failed
		);
	}else{
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'user_id' => accountEncrypt($results['id']),
			'user_name' => $results['username'],
			'name' => $results['firstname'].' '.$results['lastname']
		);
		$_SESSION['login'] = $system_status_success;
		$_SESSION['user_id'] = accountEncrypt($results['id']);
		$_SESSION['user_name'] = $results['username'];
		$_SESSION['type'] = $results['type'];
	}
	returnJSON($json_arr);
}

if( isset($login_username) && isset($login_password) ){
	chkLogin($login_username, $login_password);
}else{
	die($system_error);
}


?>