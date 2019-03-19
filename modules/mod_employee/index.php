<?php
defined('R88PROJ') or die ($system_error);

$employeeID		= $_POST['employeeID'];
$type			= $_POST['type'];



function getSelectPosition($selected){
	$selectStr	=	"<select id = 'position'>";
	$sql	=	"SELECT * FROM `employee_position` ORDER BY `id` ASC";
	$query	=	mysql_query($sql);
	while($result = mysql_fetch_assoc($query)) {
		if(isset($selected) && $selected == $result['id']) {
			$selectedStr = "selected";
		} else {
			$selectedStr = '';
		}
		$selectStr.=	'<option value = "'.$result['id'].'" '.$selectedStr.' >'.$result['position_name'].'</option>';
	}
	$selectStr.=	"</select>";
	return $selectStr;
}

function getAddForm(){
	$html = '<h3 class="title">Add New Employee</h3>';
	$html .= '<form id = "addMenu" enctype="multipart/form-data" method="post" action="">';
	$html .= '<div class = "preview"><img id = "img_preview"></div>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">EMPLOYEE CODE</span><input type="text" id="emCode" name = "code" maxlength = "10"></li>';
	$html .= '<li><span class="label">NICK NAME</span><input type="text" id="nickname" maxlength = "50"></li>';
	$html .= '<li><span class="label">FIRSTNAME</span><input type="text" id="firstname" maxlength = "50"></li>';
	$html .= '<li><span class="label">LASTNAME</span><input type="text" id="lastname" maxlength = "50"></li>';
	//$html .= '<li><span class="label">DATE OF BIRTH</span><input type = "text" id = "dob"></li>';
	//$html .= '<li><span class="label">ADDRESS</span><textarea id = "address"></textarea></li>';
	$html .= '<li><span class="label">POSITION</span>'.getSelectPosition(0).'</li>';
	//$html .= '<li><span class="label">SALARY</span><input type="text" id="salary" maxlength = "10"></li>';
	$html .= '<li><span class="label">STATUS</span><select id = "active"><option value = "0">Disable</option><option value = "1" selected>Enable</option></select></li>';
	$html .= '<li><span class="label">IMAGE</span><input type="file" id="file" name = "file"></li>';
	$html .= '</ul>';
	$html .= '<input type = "hidden" id = "mod" name = "mod" value = "employee">';
	$html .= '<input type = "hidden" id = "type" name = "type" value = "uploadImage">';
	$html .= '</form>';
	$html .= '<div class="submit"><input type="button" id="submit" value="ADD"> or <span id="cancel">Cancel</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
function getEditForm($employeeID){
	$sql		= "SELECT * FROM `employee` WHERE `id`='$employeeID'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);

	$html = '<h3 class="title">Edit Menu Detail</h3>';
	$html .= '<form id = "editEmployee" enctype="multipart/form-data" method="post" action="">';
	if($result['images']=='') {
		$html .= '<div class = "preview"><img id = "img_preview"></div>';
	} else {
		$html .= '<div class = "preview"><img id = "img_preview" src = "images/employee/'.$result['images'].'"></div>';
	}
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">CODE</span><input type="text" id="code" maxlength = "5" value = "'.$result['code'].'" disabled></li>';
	$html .= '<li><span class="label">NICKNAME</span><input type="text" id="nickname" maxlength = "50" value = "'.$result['nickname'].'"></li>';
	$html .= '<li><span class="label">FIRSTNAME</span><input type="text" id="firstname" maxlength = "50" value = "'.$result['firstname'].'"></li>';
	$html .= '<li><span class="label">LASTNAME</span><input type="text" id="lastname" maxlength = "50" value = "'.$result['lastname'].'"></li>';
	//$html .= '<li><span class="label">DATE OF BIRTH</span><input type="text" id="dob" maxlength = "20" value = "'.$result['dob'].'"></li>';
	//$html .= '<li><span class="label">ADDRESS</span><textarea id = "address">'.$result['address'].'</textarea></li>';
	$html .= '<li><span class="label">POSITION</span>'.getSelectPosition($result['position']).'</li>';
	//$html .= '<li><span class="label">SALARY</span><input type="text" id="salary" maxlength = "10" value = "'.$result['salary'].'"></li>';
	$html .= '<li><span class="label">STATUS</span><select id = "active">';
	if($result['active']==0){ 
		$html .= '<option value="0" selected>Disable</option>';
	}else{
		$html .= '<option value="0">Disable</option>';
	}
	if($result['active']==1){ 
		$html .= '<option value="1" selected>Enable</option>';
	}else{
		$html .= '<option value="1">Enable</option>';
	}
	$html .= '</select></li>';	
	$html .= '<li><span class="label">IMAGE</span><input type="file" id="file" name = "file"></li>';
	$html .= '</ul>';
	$html .= '<input type = "hidden" id = "mod" name = "mod" value = "employee">';
	$html .= '<input type = "hidden" id = "type" name = "type" value = "uploadImage">';
	$html .= '<input type = "hidden" id = "code" name = "code" value = "'.$result['code'].'">';
	$html .= '</form>';
	$html .= '<div class="submit"><input type="hidden" id="employeeID" value="'.$employeeID.'"><input type="button" id="submit" value="SAVE"> or <span id="cancel">Cancel</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
function addNewEmployee($code, $nickname, $firstname, $lastname, $dob, $position, $salary, $active, $file, $address) {
	if($file=='') {
		$sql	= "INSERT INTO `employee` (`code`, `nickname`, `firstname`, `lastname`, `dob`, `position`, `salary`, `active`, `address`) VALUES ('$code', '$nickname', '$firstname', '$lastname', '$dob', '$position', '$salary', '$active', '$address')";
	} else {
		$file = $code.".jpg";
		$sql	= "INSERT INTO `employee` (`code`, `nickname`, `firstname`, `lastname`, `dob`, `position`, `salary`, `active`, `images`, `address`) VALUES ('$code', '$nickname', '$firstname', '$lastname', '$dob', '$position', '$salary', '$active', '$file', '$address')";
	}
	$query	= mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		//'msg' => $sql
		'msg' => 'New Employee Completed.'	
	);
	returnJSON($json_arr);
}
function updateEmployee($employeeID, $code, $nickname, $firstname, $lastname, $dob, $address, $position, $active, $file, $salary){
	if($file=='') {
		$sql	= "UPDATE `employee` SET `nickname`='$nickname', `firstname`='$firstname', `lastname`='$lastname', `dob`='$dob', `address`='$address', `position`='$position', `active`='$active', `salary` = '$salary' WHERE `id`='$employeeID'";	
	} else {
		$file = $code.".jpg";
		$sql	= "UPDATE `employee` SET `nickname`='$nickname', `firstname`='$firstname', `lastname`='$lastname', `dob`='$dob', `address`='$address', `position`='$position', `active`='$active', `images` = '$file' `salary` = '$salary' WHERE `id`='$employeeID'";		
	}
	$query	= mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'Employee Updated.'
		//'msg'	=>	$sql
	);
	returnJSON($json_arr);
}
function changeEmployeeStatus($employeeID){
	if($employeeID > 1){
		$sql = "UPDATE `employee` SET `active`=IF(`active`=1, 0, 1) WHERE `id`='$employeeID'";
		$query	= mysql_query($sql);

		$sql = "SELECT `active` FROM `employee` WHERE `id`='$employeeID'";
		$query	= mysql_query($sql);
		$result	= mysql_fetch_assoc($query);
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'employee' => $employeeID,
			'active' => $result['active']
		);
	}else{
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'employee' => $employeeID,
			'active' => 1
		);
	}
	returnJSON($json_arr);
	
}
function uniqueEmployeeCode($code){
	$sql = "SELECT COUNT(*) FROM `employee` WHERE `code`='$code'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);
	if($result['COUNT(*)'] > 0){
		return false;
	}else{
		return true;
	}
}

function uploadImage($code){
	$imgPathName = "images/employee/".$code.".jpg";
	if( isset( $_FILES["file"]["type"] ) ){

	$validextensions = array("jpeg", "jpg",);
	$temporary = explode(".", $_FILES["file"]["name"]);
	$file_extension = $temporary[1];
	if ((($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg")
	) && ($_FILES["file"]["size"] < 2000000)//Approx. 100kb files can be uploaded.
	&& in_array($file_extension, $validextensions)) {
		if ($_FILES["file"]["error"] > 0)	{
			global $system_status_failed;
			$msg = 'error';
			$success = $system_status_failed;
		}	else	{
			if (file_exists($imgPathName)) {
				unlink($imgPathName);// Do something
			}
			$sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
			$targetPath = $imgPathName; // Target path where file is to be stored
			move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file
			$msg =  'upload image success';
			global $system_status_success;
			$success =  $system_status_success;
		}
	}	else	{
		global $system_status_failed;
		$msg =  'invalid file extension or file too large';
		$success = $system_status_failed;
	}
}
$json_arr = array(
					'success' => $success, 
					//'imgPath' => 'images/profile/'.$_SESSION['userid'].'.jpg',
					'msg' => $msg
				);
echo $msg;
//returnJSON($json_arr);
}

chkSession();
switch($type){
	case 'add':
		getAddForm();
		break;
	case 'changeEmployeeStatus':
		changeEmployeeStatus($employeeID);
		break;
	case 'edit':
		getEditForm($employeeID);
		break;
	case 'addNewEmployee':
		$code			=	mysql_real_escape_string($_POST['code']);
		$nickname		=	mysql_real_escape_string($_POST['nickname']);
		$firstname		=	mysql_real_escape_string($_POST['firstname']);
		$lastname		=	mysql_real_escape_string($_POST['lastname']);
		$dob			=	mysql_real_escape_string($_POST['dob']);
		$address		=	mysql_real_escape_string($_POST['address']);
		$position		=	mysql_real_escape_string($_POST['position']);
		$salary			=	mysql_real_escape_string($_POST['salary']);
		$active			=	mysql_real_escape_string($_POST['active']);
		$file			=	mysql_real_escape_string($_POST['file']);
		
		$salary = '';
		$dob = '';
		$address = '';

		if(uniqueEmployeeCode($code) == true){
			addNewEmployee($code, $nickname, $firstname, $lastname, $dob, $position, $salary, $active, $file, $address);
		}else{
			global $system_status_failed;
			$json_arr = array(
				'process' => $system_status_failed,
				'msg' => $code.' already register.'
			);
			returnJSON($json_arr);
		}
		break;
	case 'updateEmployee':
		$employeeID		=	mysql_real_escape_string($_POST['employeeID']);
		$code			=	mysql_real_escape_string($_POST['code']);
		$nickname		=	mysql_real_escape_string($_POST['nickname']);
		$firstname		=	mysql_real_escape_string($_POST['firstname']);
		$lastname		=	mysql_real_escape_string($_POST['lastname']);
		$dob			=	mysql_real_escape_string($_POST['dob']);
		$address		=	mysql_real_escape_string($_POST['address']);
		$position		=	mysql_real_escape_string($_POST['position']);
		$salary			=	mysql_real_escape_string($_POST['salary']);
		$active			=	mysql_real_escape_string($_POST['active']);
		$file			=	mysql_real_escape_string($_POST['file']);
		updateEmployee($employeeID, $code, $nickname, $firstname, $lastname, $dob, $address, $position, $active, $file, $salary);
		break;
	case 'uploadImage':
		$code			=	mysql_real_escape_string($_POST['code']);
		uploadImage($code);
		break;
	case 'list' :
		getListMenu();
		break;
}
?>