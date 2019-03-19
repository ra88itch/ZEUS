<?php
defined('R88PROJ') or die ('SYSTEM ERROR');

$old_pass	= mysql_real_escape_string($_POST['oldPass']);
$new_pass	= mysql_real_escape_string($_POST['newPass']);

function changePassword($old_password, $new_password){
	$userid = accountDecrypt($_SESSION['user_id']);
	$sql = "SELECT COUNT(*) FROM `account` WHERE `id` = '".$userid."' AND `password`=md5('".$old_password."')";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);

	if($results['COUNT(*)']<1) {
		global $system_status_failed;
		$json_arr = array(
			'process' => $system_status_failed
		);
	} else {
		$sql = "UPDATE `account` SET `password`=md5('".$new_password."') WHERE `id` = '".$userid."'";
		$query = mysql_query($sql);	

		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success
		);
	}
	returnJSON($json_arr);
}
chkSession();
changePassword($old_pass, $new_pass);
?>