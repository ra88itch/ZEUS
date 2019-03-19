<?php
defined('R88PROJ') or die ($system_error);

$type			= $_POST['type'];

function getAddForm(){
	$html = '<h3 class="title">Add New Salary</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">NAME</span><input type="text" id="name" maxlength="20"></li>';
	$html .= '<li><span class="label">START DATE</span><input type="text" id="start_date" maxlength="0"></li>';
	$html .= '<li><span class="label">END DATE</span><input type="text" id="end_date" maxlength="0"></li>';
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="button" id="submit" value="ADD"> or <span id="cancel">Cancel</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}

function addNewSalary($name, $start_date, $end_date) {
	$userID		=	$_SESSION['user_id'];
	$sql		=	"INSERT INTO `salary` (`name`,`start_date`, `end_date`, `create_by`) VALUES ('$name','$start_date','$end_date', '$userID')";
	$query		=	mysql_query($sql);
	$salaryID	=	mysql_insert_id();
	
	$em_sql		=	"SELECT * FROM `employee` WHERE `active` = 1";
	$em_query	=	mysql_query($em_sql);
	while($em_result = mysql_fetch_assoc($em_query)) {
		$sql	=	"INSERT INTO `salary_detail` (`salary_ref`, `employee_id`, `position`, `create_by`) VALUES ('$salaryID', '".$em_result['id']."', '".$em_result['position']."', '$userID')";
		$query	=	mysql_query($sql);
	}
	
	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'New Salary Completed.'	
	);
	returnJSON($json_arr);
}

function getEditForm($salaryID){
	$sql	=	"SELECT * FROM `salary_detail` WHERE `id` = '$salaryID'";
	$query	=	mysql_query($sql);
	$result	=	mysql_fetch_assoc($query);
	$employeeData	=	getEmployee($result['employee_id']);
	$html = '<h3 class="title">Edit Salary</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">EMPLOYEE NAME</span><input type="text" id="emID" maxlength="0" disabled value = "'.$employeeData[0].'"> </li>';
	$html .= '<li><span class="label">SALARY</span><input type="text" id="salary" maxlength="0"disabled value = "'.$employeeData[1].'"></li>';
	$html .= '<li><span class="label">DAY OFF</span><input type="text" id="dayoff" maxlength="2" value = "'.$result['day_off'].'"></li>';
	$html .= '<li><span class="label">LATE TIME</span><input type="text" id="latetime" maxlength="3" value = "'.$result['late'].'"></li>';
	$html .= '<input type = "hidden" id = "salaryDetailID" value = "'.$salaryID.'">';
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="button" id="submit" value="ADD"> or <span id="cancel">Cancel</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}

function getEmployee($employeeID){
	$sql = "SELECT * FROM `employee` WHERE `id`='$employeeID'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	$name = $result['firstname'].' '.$result['lastname'];
	return array($name, $result['salary']);
}

function updateSalary($salaryID,$dayoff,$late){
	$sql	=	"UPDATE `salary_detail` SET `day_off` = '$dayoff', `late` = '$late' WHERE `id` = '$salaryID'";
	$query	=	mysql_query($sql);
	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		//'msg' => $sql
		'msg' => 'Update Salary Success'
	);
	returnJSON($json_arr);
}

chkSession();
switch($type){
	case 'add':
		getAddForm();
		break;
	case 'addNewSalary':
		$name				= mysql_real_escape_string($_POST['name']);
		$start_date			= mysql_real_escape_string($_POST['start_date']);
		$end_date			= mysql_real_escape_string($_POST['end_date']);
		addNewSalary($name, $start_date, $end_date);
		break;
	case 'edit':
		$salaryID			= mysql_real_escape_string($_POST['salaryID']);
		getEditForm($salaryID);
		break;
	case 'updateSalary':
		$salaryID			= mysql_real_escape_string($_POST['salaryID']);
		$dayoff				= mysql_real_escape_string($_POST['dayoff']);
		$late				= mysql_real_escape_string($_POST['late']);
		updateSalary($salaryID,$dayoff,$late);
		break;
}
?>