<?php
defined('R88PROJ') or die ($system_error);

$category		= $_POST['category_id'];
$type			= $_POST['type'];
function getAddForm(){
	$html = '<h3 class="title">เพิ่มหมวดหมู่อาหาร</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">ชื่อหมวดหมู่อาหาร</span><input type="text" id="category_name" maxlength = "50"></li>';
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="button" id="submit" value="เพิ่ม"> / <span id="cancel">ยกเลิก</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
function getEditForm($category){
	$sql		= "SELECT * FROM `menu_type_cooking` WHERE `id`='$category'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);

	$html = '<h3 class="title">แก้ไขรายการอาหาร</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">ชื่อหมวดหมู่อาหาร</span><input type="text" id="category_name" maxlength = "50" value = "'.$result['type_cooking'].'"></li>';
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="hidden" id="category_id" value="'.$category.'"><input type="button" id="submit" value="แก้ไข"> / <span id="cancel">ยกเลิก</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
function uniqueCategory($category_name){
	$sql = "SELECT COUNT(*) FROM `menu_type_cooking` WHERE `type_cooking`='$category_name'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);
	if($result['COUNT(*)'] > 0){
		return false;
	}else{
		return true;
	}
}
function addNewCategory($category_name){
	$uniqueCategory = uniqueCategory($category_name);
	if($uniqueCategory == true){
		$sql	= "INSERT INTO `menu_type_cooking` (`type_cooking`) VALUES ('$category_name')";
		$query	= mysql_query($sql);
		$msg = 'เพิ่มหมวดหมู่เรียบร้อย';
	}else{
		$msg = $category_name.' มีรายการอยู่แล้ว';
	}	

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => $msg	
	);
	returnJSON($json_arr);
}
function updateCategory($category, $category_name){	
	$uniqueCategory = uniqueCategory($category_name);
	if($uniqueCategory == true){
		$sql	= "UPDATE `menu_type_cooking` SET `type_cooking`='$category_name' WHERE `id`='$category'";	
		$query	= mysql_query($sql);
		$msg = 'แก้ไขหมวดหมู่เรียบร้อย';
	}else{
		$msg = $category_name.' มีรายการอยู่แล้ว';
	}	

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => $msg
	);
	returnJSON($json_arr);
}
function changeStatus($category, $column, $perm){
	$sql = "UPDATE `menu_type_cooking` SET `$column`=IF(`$column`=1, 0, 1) WHERE `id`='$category'";
	$query	= mysql_query($sql);

	$sql = "SELECT * FROM `menu_type_cooking` WHERE `id`='$category'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);
	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'category' => $category,
		'active' => $result[$column],
		'perm' => $perm.$category
	);
	
	returnJSON($json_arr);	
}


chkSession();
switch($type){
	case 'add':
		getAddForm();
		break;
	case 'changeMemberStatus':
		changeStatus($category, 'discount_member', 'member');
		break;
	case 'changeEmployeeStatus':
		changeStatus($category, 'discount_employee', 'employee');
		break;
	case 'edit':
		getEditForm($category);
		break;
	case 'addNewCategory':
		$category_name		=	mysql_real_escape_string($_POST['category_name']);
		addNewCategory($category_name);
		break;
	case 'updateCategory':
		$category_name		=	mysql_real_escape_string($_POST['category_name']);
		updateCategory($category, $category_name);
		break;
}
?>