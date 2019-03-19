<?php
defined('R88PROJ') or die ($system_error);

$massager_id		= $_POST['massager_id'];
$type					= $_POST['type'];

function getAddForm(){
	$html = '<h3 class="title">Add New Massager</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">NO</span><input type="text" id="massager_no" maxlength="16"></li>';
	$html .= '<li><span class="label">NICKNAME</span><input type="text" id="nickname" maxlength="12"></li>';
	$html .= '<li><span class="label">FIRSTNAME</span><input type="text" id="firstname" maxlength="20"></li>';
	$html .= '<li><span class="label">LASTNAME</span><input type="text" id="lastname" maxlength="30"></li>';
	$html .= '<li><span class="label">BANK ACCOUNT</span><input type="text" id="bank_account" maxlength="100"></li>';
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
function getEditForm($massager_id){
	$sql		= "SELECT * FROM `employee` WHERE `id`='$massager_id'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);

	$html = '<h3 class="title">Edit Massager Detail</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">NO</span><input type="text" id="massager_no" maxlength="16" value="'.$result['code'].'"></li>';
	$html .= '<li><span class="label">NICKNAME</span><input type="text" id="nickname" maxlength="12" value="'.$result['nickname'].'"></li>';
	$html .= '<li><span class="label">FIRSTNAME</span><input type="text" id="firstname" maxlength="20" value="'.$result['firstname'].'"></li>';
	$html .= '<li><span class="label">LASTNAME</span><input type="text" id="lastname" maxlength="30" value="'.$result['lastname'].'"></li>';
	$html .= '<li><span class="label">BANK ACCOUNT</span><input type="text" id="bank_account" maxlength="100" value="'.$result['bank_account'].'"></li>';
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
	$html .= '<div class="submit"><input type="hidden" id="massager_id" value="'.$massager_id.'"><input type="button" id="submit" value="SAVE"> or <span id="cancel">Cancel</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
function addNewAccount($massager_no, $nickname, $firstname, $lastname, $bank_account, $active){
	$sql	= "INSERT INTO `employee` (`code`, `nickname`, `firstname`, `lastname`, `bank_account`, `active`,`position`) VALUES ('$massager_no', '$nickname', '$firstname', '$lastname', '$bank_account', '$active','1')";
	$query	= mysql_query($sql);
	//`position`='0'

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'New Massager Add Completed.'	
	);
	returnJSON($json_arr);
}
function updateAccount($massager_no, $nickname, $firstname, $lastname, $bank_account, $active, $massager_id){
	$sql	= "UPDATE `employee` SET `code`='$massager_no', `nickname`='$nickname', `firstname`='$firstname', `lastname`='$lastname', `bank_account`='$bank_account', `active`='$active' WHERE `id`='$massager_id'";	
	$query	= mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'Massager Updated.'
	);
	returnJSON($json_arr);
}
function changeAccountStatus($account){
	if($account > 1){
		$sql = "UPDATE `employee` SET `active`=IF(`active`=1, 0, 1) WHERE `id`='$account'";
		$query	= mysql_query($sql);

		$sql = "SELECT `active` FROM `employee` WHERE `id`='$account'";
		$query	= mysql_query($sql);
		$result	= mysql_fetch_assoc($query);
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'account' => $account,
			'active' => $result['active']
		);
	}else{
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'account' => $account,
			'active' => 1
		);
	}
	returnJSON($json_arr);
	
}
function uniqueUsername($username){
	$sql = "SELECT COUNT(*) FROM `employee` WHERE `username`='$username'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);
	if($result['COUNT(*)'] > 0){
		return false;
	}else{
		return true;
	}
}

chkSession();
switch($type){
	case 'add':
		getAddForm();
		break;
	case 'changeAccountStatus':
		changeAccountStatus($massager_id);
		break;
	case 'edit':
		getEditForm($massager_id);
		break;
	case 'addNewAccount':
		$massager_no		= mysql_real_escape_string($_POST['massager_no']);
		$nickname			= mysql_real_escape_string($_POST['nickname']);
		$firstname			= mysql_real_escape_string($_POST['firstname']);
		$lastname			= mysql_real_escape_string($_POST['lastname']);
		$bank_account		= mysql_real_escape_string($_POST['bank_account']);
		$active				= mysql_real_escape_string($_POST['status']);
		addNewAccount($massager_no, $nickname, $firstname, $lastname, $bank_account, $active);
		break;
	case 'updateAccount':
		$massager_no		= mysql_real_escape_string($_POST['massager_no']);
		$nickname			= mysql_real_escape_string($_POST['nickname']);
		$firstname			= mysql_real_escape_string($_POST['firstname']);
		$lastname			= mysql_real_escape_string($_POST['lastname']);
		$bank_account		= mysql_real_escape_string($_POST['bank_account']);
		$active				= mysql_real_escape_string($_POST['status']);
		updateAccount($massager_no, $nickname, $firstname, $lastname, $bank_account, $active, $massager_id);
		break;
}
?>