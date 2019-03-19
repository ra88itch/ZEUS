<?php
defined('R88PROJ') or die ($system_error);

$account_id		= $_POST['account_id'];
$type					= $_POST['type'];

function getAddForm(){
	$html = '<h3 class="title">Add New Account</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">USERNAME</span><input type="text" id="username" maxlength="16"></li>';
	$html .= '<li><span class="label">CLASS</span><select id="class"><option value="1">ADMINISTRATOR</option><option value="2" selected>USER</option></select></li>';
	$html .= '<li><span class="label">PASSWORD</span><input type="password" id="password" maxlength="12"></li>';
	$html .= '<li><span class="label">CONFIRM PASSWORD</span><input type="password" id="cfmpassword" maxlength="12"></li>';
	$html .= '<li><span class="label">FIRSTNAME</span><input type="text" id="firstname" maxlength="20"></li>';
	$html .= '<li><span class="label">LASTNAME</span><input type="text" id="lastname" maxlength="30"></li>';
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
function getEditForm($account_id){
	$sql		= "SELECT * FROM `account` WHERE `id`='$account_id'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);

	$html = '<h3 class="title">Edit Account Detail</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">USERNAME</span><input type="text" value="'.$result['username'].'" disabled></li>';
	$html .= '<li><span class="label">CLASS</span><select id="class">';
	if($result['type']==1){ 
		$html .= '<option value="1" selected>ADMINISTRATOR</option>';
	}else{
		$html .= '<option value="1">ADMINISTRATOR</option>';
	}
	if($result['type']==2){ 
		$html .= '<option value="2" selected>USER</option>';
	}else{
		$html .= '<option value="2">USER</option>';
	}	
	$html .= '</select></li>';
	$html .= '<li><span class="label">NEW PASSWORD</span><input type="password" id="password" maxlength="12"></li>';
	$html .= '<li><span class="label">CONFIRM NEW PASSWORD</span><input type="password" id="cfmpassword" maxlength="12"></li>';
	$html .= '<li><span class="label">FIRSTNAME</span><input type="text" id="firstname" maxlength="20" value="'.$result['firstname'].'"></li>';
	$html .= '<li><span class="label">LASTNAME</span><input type="text" id="lastname" maxlength="30" value="'.$result['lastname'].'"></li>';
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
	$html .= '<div class="submit"><input type="hidden" id="account" value="'.$account_id.'"><input type="button" id="submit" value="SAVE"> or <span id="cancel">Cancel</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
function addNewAccount($username, $password, $firstname, $lastname, $active, $class){
	$sql	= "INSERT INTO `account` (`username`, `password`, `firstname`, `lastname`, `active`, `type`) VALUES ('$username', md5('$password'), '$firstname', '$lastname', '$active', '$class')";
	$query	= mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'New Account Add Completed.'	
	);
	returnJSON($json_arr);
}
function updateAccount($password, $firstname, $lastname, $active, $class, $account){
	if($password==''){
		$sql	= "UPDATE `account` SET `firstname`='$firstname', `lastname`='$lastname', `active`='$active', `type`='$class' WHERE `id`='$account'";	
	}else{
		$sql	= "UPDATE `account` SET `password`=md5('$password'), `firstname`='$firstname', `lastname`='$lastname', `active`='$active', `type`='$class' WHERE `id`='$account'";	
	}
	$query	= mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'Account Updated.'
	);
	returnJSON($json_arr);
}
function changeAccountStatus($account){
	if($account > 1){
		$sql = "UPDATE `account` SET `active`=IF(`active`=1, 0, 1) WHERE `id`='$account'";
		$query	= mysql_query($sql);

		$sql = "SELECT `active` FROM `account` WHERE `id`='$account'";
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
	$sql = "SELECT COUNT(*) FROM `account` WHERE `username`='$username'";
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
		changeAccountStatus($account_id);
		break;
	case 'edit':
		getEditForm($account_id);
		break;
	case 'addNewAccount':
		$username			= mysql_real_escape_string($_POST['username']);
		$password				= mysql_real_escape_string($_POST['password']);
		$firstname				= mysql_real_escape_string($_POST['firstname']);
		$lastname				= mysql_real_escape_string($_POST['lastname']);
		$active					= mysql_real_escape_string($_POST['status']);
		$class					= mysql_real_escape_string($_POST['acc_class']);
		if(uniqueUsername($username) == true){
			addNewAccount($username, $password, $firstname, $lastname, $active, $class);
		}else{
			global $system_status_failed;
			$json_arr = array(
				'process' => $system_status_failed,
				'msg' => $username.' มีผู้ใช้งานอยู่แล้ว'
			);
			returnJSON($json_arr);
		}
		break;
	case 'updateAccount':
		$password				= mysql_real_escape_string($_POST['password']);
		$firstname				= mysql_real_escape_string($_POST['firstname']);
		$lastname				= mysql_real_escape_string($_POST['lastname']);
		$active					= mysql_real_escape_string($_POST['status']);
		$class					= mysql_real_escape_string($_POST['acc_class']);
		$account				= mysql_real_escape_string($_POST['account']);
		updateAccount($password, $firstname, $lastname, $active, $class, $account);
		break;
}
?>