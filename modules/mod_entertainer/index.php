<?php
defined('R88PROJ') or die ($system_error);

$entertainer_id		= $_POST['entertainer_id'];
$type			= $_POST['type'];

function getAddForm(){
	$html = '<h3 class="title">Add New Entertainer</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">NAME</span><input type="text" id="entertainer_name" maxlength="50"></li>';
	$html .= '<li><span class="label">STATUS</span><select id="status"><option value="0">disable</option><option value="1" selected>enable</option></select></li>';	
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="button" id="submit" value="ADD"> or <span id="cancel">Cancel</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
function getEditForm($entertainer_id){
	$sql		= "SELECT * FROM `entertainer` WHERE `id`='$entertainer_id'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);

	$html = '<h3 class="title">Edit Entertainer Detail</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">NAME</span><input type="text" id="entertainer_name" value="'.$result['entertainer_name'].'"></li>';
	$html .= '<li><span class="label">STATUS</span><select id="status">';
	if($result['active']==0){ 
		$html .= '<option value="0" selected>disable</option>';
	}else{
		$html .= '<option value="0">disable</option>';
	}
	if($result['active']==1){ 
		$html .= '<option value="1" selected>enable</option>';
	}else{
		$html .= '<option value="1">enable</option>';
	}
	$html .= '</select></li>';	
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="hidden" id="entertainer_id" value="'.$entertainer_id.'"><input type="button" id="submit" value="SAVE"> or <span id="cancel">Cancel</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
function addNewEntertainer($entertainer_name, $active){
	$sql	= "INSERT INTO `entertainer` (`entertainer_name`, `active`) VALUES ('$entertainer_name', '$active')";
	$query	= mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'New Entertainer Add Completed.'	
	);
	returnJSON($json_arr);
}
function updateEntertainer($entertainer_name, $active, $entertainer_id){
	$sql	= "UPDATE `entertainer` SET `entertainer_name`='$entertainer_name', `active`='$active' WHERE `id`='$entertainer_id'";	
	$query	= mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'Entertainer Updated.'
	);
	returnJSON($json_arr);
}
function changeEntertainerStatus($entertainer_id){
	$sql = "UPDATE `entertainer` SET `active`=IF(`active`=1, 0, 1) WHERE `id`='$entertainer_id'";
	$query	= mysql_query($sql);

	$sql = "SELECT `active` FROM `entertainer` WHERE `id`='$entertainer_id'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);
	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'entertainer' => $entertainer_id,
		'active' => $result['active']
	);
	returnJSON($json_arr);	
}

chkSession();
switch($type){
	case 'add':
		getAddForm();
		break;
	case 'changeEntertainerStatus':
		changeEntertainerStatus($entertainer_id);
		break;
	case 'edit':
		getEditForm($entertainer_id);
		break;
	case 'addNewEntertainer':
		$entertainer_name		= mysql_real_escape_string($_POST['entertainer_name']);
		$active					= mysql_real_escape_string($_POST['status']);
		addNewEntertainer($entertainer_name, $active);
		break;
	case 'updateEntertainer':
		$entertainer_name		= mysql_real_escape_string($_POST['entertainer_name']);
		$active					= mysql_real_escape_string($_POST['status']);
		updateEntertainer($entertainer_name, $active, $entertainer_id);
		break;
}

?>