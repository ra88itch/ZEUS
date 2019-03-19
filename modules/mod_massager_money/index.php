<?php
defined('R88PROJ') or die ($system_error);


$type				= $_POST['type'];

function getAddForm(){
	$html = '<h3 class="title">Create New Profile</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">DATE</span><input type="text" id="date" maxlength="12"></li>';
	$html .= '<li><span class="label">MASSAGER NO.</span><textarea id="massager"></textarea></li>';
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="button" id="submit" value="CREATE"> or <span id="cancel">Cancel</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
function getEditForm($profile_id){
	$sql		= "SELECT * FROM `massage_money` WHERE `id`='$profile_id'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);

	$html = '<h3 class="title">Edit Profile</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">DATE</span><input type="text" id="date" maxlength="12" value="'.$result['date'].'"></li>';
	$html .= '<li><span class="label">MASSAGER NO.</span><textarea id="massager">'.$result['massager_no'].'</textarea></li>';
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="hidden" id="profile_id" value="'.$profile_id.'"><input type="button" id="submit" value="SAVE"> or <span id="cancel">Cancel</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
function addMassagerProfile($date, $massager_no){
	$user_id = accountDecrypt($_SESSION['user_id']);
	$sql	= "INSERT INTO `massage_money` (`date`, `massager_no`, `employee_id`) VALUES ('$date', '$massager_no', '$user_id')";
	$query	= mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'Create Massager Profile Completed.'	
	);
	returnJSON($json_arr);
}
function editMassagerProfile($date, $massager_no, $id){
	$user_id = accountDecrypt($_SESSION['user_id']);
	$sql	= "UPDATE `massage_money` SET `date`='$date', `massager_no`='$massager_no', `employee_id`='$user_id' WHERE `id`='$id'";	
	$query	= mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'Massager Profile Updated.'
	);
	returnJSON($json_arr);
}



chkSession();
switch($type){
	case 'add':
		getAddForm();
		break;
	case 'edit':
		$profile_id			= mysql_real_escape_string($_POST['profile_id']);
		getEditForm($profile_id);
		break;
	case 'addMassagerProfile':
		$date				= mysql_real_escape_string($_POST['date']);
		$massager_no		= mysql_real_escape_string($_POST['massager']);
		addMassagerProfile($date, $massager_no, $id);
		break;
	case 'editMassagerProfile':
		$date				= mysql_real_escape_string($_POST['date']);
		$massager_no		= mysql_real_escape_string($_POST['massager']);
		$profile_id			= mysql_real_escape_string($_POST['profile_id']);
		editMassagerProfile($date, $massager_no, $profile_id);
		break;
}
?>