<?php
defined('R88PROJ') or die ($system_error);

$menu_id		= $_POST['menu_id'];
$type			= $_POST['type'];

function getSelectCooking($selected){
	$selectStr	=	"<select id = 'type_by_cooking'>";
	$sql	=	"SELECT * FROM `menu_type_cooking` ORDER BY `id` ASC";
	$query	=	mysql_query($sql);
	while($result = mysql_fetch_assoc($query)) {
		if(isset($selected) && $selected == $result['id']) {
			$selectedStr = "selected";
		} else {
			$selectedStr = '';
		}
		$selectStr.=	'<option value = "'.$result['id'].'" '.$selectedStr.' >'.$result['type_cooking'].'</option>';
	}
	$selectStr.=	"</select>";
	return $selectStr;
}
function getSelectMeat($selected){
	$selectStr	=	"<select id = 'type_by_meat'>";
	$sql	=	"SELECT * FROM `menu_type_meat` ORDER BY `id` ASC";
	$query	=	mysql_query($sql);
	while($result = mysql_fetch_assoc($query)) {
		if(isset($selected) && $selected == $result['id']) {
			$selectedStr = "selected";
		} else {
			$selectedStr = '';
		}
		$selectStr.=	'<option value = "'.$result['id'].'" '.$selectedStr.'>'.$result['type_meat'].'</option>';
	}
	$selectStr.=	"</select>";
	return $selectStr;
}
function getSelectStock($selected){
	$selectStr	=	"<select id = 'stock_id'>";
	$selectStr.=	'<option value = "0">เลือกรายการ</option>';
	$sql	=	"SELECT * FROM `stock` WHERE `store_stock`='0' ORDER BY `id` ASC";
	$query	=	mysql_query($sql);
	while($result = mysql_fetch_assoc($query)) {
		if(isset($selected) && $selected == $result['id']) {
			$selectedStr = "selected";
		} else {
			$selectedStr = '';
		}
		$selectStr.=	'<option value = "'.$result['id'].'" '.$selectedStr.'>'.$result['name'].'</option>';
	}
	$selectStr.=	"</select>";
	return $selectStr;
}
function getSelectUnit($selected){
	$selectStr	=	"<select id = 'unit'>";
	$sql	=	"SELECT * FROM `menu_unit` ORDER BY `id` ASC";
	$query	=	mysql_query($sql);
	while($result = mysql_fetch_assoc($query)) {
		if(isset($selected) && $selected == $result['id']) {
			$selectedStr = "selected";
		} else {
			$selectedStr = '';
		}
		$selectStr.=	'<option value = "'.$result['id'].'" '.$selectedStr.'>'.$result['unit_name'].'</option>';
	}
	$selectStr.=	"</select>";
	return $selectStr;
}
function getAddForm(){
	$html = '<h3 class="title">เพิ่มรายการอาหาร</h3>';
	$html .= '<form id="addMenu" enctype="multipart/form-data" method="post" action="">';
	$html .= '<div class="preview"><img id="img_preview"></div>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">ชื่อรายการอาหาร</span><input type="text" id="menu_name_th" maxlength = "50"></li>';
	$html .= '<li><span class="label">รายละเอียด</span><textarea id="menu_desc" maxlength = "500"></textarea></li>';
	$html .= '<li><span class="label">ประเภทอาหาร</span>'.getSelectCooking(0).' / ประเภทเนื้อสัตว์ '.getSelectMeat(0).'</li>';
	//$html .= '<li></li>';
	$html .= '<li><span class="label">ราคา</span><input type="text" id="price" maxlength = "5"></li>';
	$html .= '<li><span class="label">หน่วยนับ</span>'.getSelectUnit(0).'  / หักรายการจาก สต๊อก'.getSelectStock(0).'</li>';
	//$html .= '<li></li>';
	$html .= '<li><span class="label">สถานะ</span><select id = "active"><option value = "0">Disable</option><option value = "1" selected>Enable</option></select></li>';
	$html .= '<li><span class="label">กำหนดราคาล่วงหน้า</span><input type="text" id="future_price" maxlength="5" style="width:;"></li>';
	$html .= '<li><span class="label">วันกำหนดราคาล่วงหน้า</span><input type="text" id="future_date"></li>';
	$html .= '<li><span class="label">รูป</span><input type="file" id="file" name = "file"></li>';
	$html .= '</ul>';
	$html .= '<input type="hidden" id="mod" name="mod" value="mmenu">';
	$html .= '<input type="hidden" id="type" name="type" value="uploadImage">';
	$html .= '</form>';
	$html .= '<div class="submit"><input type="button" id="submit" value="เพิ่ม"> / <span id="cancel">ยกเลิก</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
function getEditForm($menu_id){
	$sql		= "SELECT * FROM `menu` WHERE `id`='$menu_id'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);

	$html = '<h3 class="title">แก้ไขรายการอาหาร</h3>';
	$html .= '<form id = "editMenu" enctype="multipart/form-data" method="post" action="">';
	
	$html .= '<div class="preview"><img id="img_preview" src="images/menu/'.$menu_id.'.jpg"></div>';
	
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">ชื่อรายการอาหาร</span><input type="text" id="menu_name_th" maxlength = "50" value = "'.$result['menu_name_th'].'"></li>';
	$html .= '<li><span class="label">รายละเอียด</span><textarea  id="menu_desc" maxlength = "500">'.$result['menu_desc'].'</textarea></li>';
	$html .= '<li><span class="label">ประเภทอาหาร</span>'.getSelectCooking($result['type_by_cooking']).' / ประเภทเนื้อสัตว์ '.getSelectMeat($result['type_by_meat']).'</li>';
	//$html .= '<li></li>';
	$html .= '<li><span class="label">ราคา</span><input type="text" id="price" maxlength = "5" value = "'.$result['price'].'"></li>';
	$html .= '<li><span class="label">หน่วยนับ</span>'.getSelectUnit($result['unit']).' / หักรายการจากสต๊อก '.getSelectStock($result['stock_id']).'</li>';
	//$html .= '<li></li>';
	$html .= '<li><span class="label">สถานะ</span><select id = "active">';
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
	$html .= '<li><span class="label">กำหนดราคาล่วงหน้า</span><input type="text" id="future_price" maxlength="5" value="'.$result['future_price'].'"></li>';
	$html .= '<li><span class="label">วันกำหนดราคาล่วงหน้า</span><input type="text" id="future_date" value="'.$result['future_date'].'"></li>';
	$html .= '<li><span class="label">รูป</span><input type="file" id="file" name = "file"></li>';
	$html .= '</ul>';
	$html .= '<input type = "hidden" id = "mod" name = "mod" value = "mmenu">';
	$html .= '<input type = "hidden" id = "type" name = "type" value = "uploadImage">';
	$html .= '<input type = "hidden" id = "code" name = "menu_code" value = "'.$menu_id.'">';
	$html .= '</form>';
	$html .= '<div class="submit"><input type="hidden" id="menu_id" value="'.$menu_id.'"><input type="button" id="submit" value="แก้ไข"> / <span id="cancel">ยกเลิก</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
function uniqueMenu($menu_name_th){
	$sql = "SELECT COUNT(*) FROM `menu` WHERE `menu_name_th`='$menu_name_th'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);
	if($result['COUNT(*)'] > 0){
		$sql = "SELECT `id` FROM `menu` WHERE `menu_name_th`='$menu_name_th' LIMIT 1";
		$query	= mysql_query($sql);
		$result	= mysql_fetch_assoc($query);
		return $result['id'];
	}else{
		return true;
	}
}
function addNewMenu($menu_name_th, $menu_desc, $type_by_cooking, $type_by_meat, $price, $unit, $active, $future_price, $future_date, $stock_id){
	$uniqueMenu = uniqueMenu($menu_name_th);
	if($uniqueMenu == true){
		$sql	= "INSERT INTO `menu` (`menu_name_th`, `menu_desc`, `type_by_cooking`, `type_by_meat`, `price`, `unit`, `special`, `active`, `future_date`, `future_price`, `stock_id`) VALUES ('$menu_name_th', '$menu_desc', '$type_by_cooking', '$type_by_meat', '$price', '$unit', '$special', '$active', '$future_date', '$future_price', '$stock_id')";
		$query	= mysql_query($sql);
		$msg = 'เพิ่มเมนูเรียบร้อย';
	}else{
		$msg = $menu_name_th.' มีรายการอยู่แล้ว';
	}	

	global $system_status_success;
	if($uniqueMenu !== true){
		$system_status_success = 'already';
	}
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => $msg,
		'menu_id' => $uniqueMenu	
	);
	returnJSON($json_arr);
}
function updateMenu($menu_id, $menu_name_th, $menu_desc, $type_by_cooking, $type_by_meat, $price, $unit, $active, $future_price, $future_date, $stock_id){
	$sql	= "UPDATE `menu` SET `menu_name_th`='$menu_name_th', `menu_name_en`='$menu_name_en', `menu_desc`='$menu_desc', `type_by_cooking`='$type_by_cooking', `type_by_meat`='$type_by_meat', `price`='$price', `unit`='$unit', `special`='$special', `active`='$active', `future_date`='$future_date', `future_price`='$future_price', `stock_id`='$stock_id' WHERE `id`='$menu_id'";	
	$query	= mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'แก้ไขเมนูเรียบร้อย'
	);
	returnJSON($json_arr);
}
function changeMenuStatus($menu_id){
	$sql = "UPDATE `menu` SET `active`=IF(`active`=1, 0, 1) WHERE `id`='$menu_id'";
	$query	= mysql_query($sql);

	$sql = "SELECT `active` FROM `menu` WHERE `id`='$menu_id'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);
	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'menu' => $menu_id,
		'active' => $result['active']
	);
	
	returnJSON($json_arr);	
}

function uploadImage($menuCode){
	$imgPathName = "images/menu/".$menuCode.".jpg";
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
		}
	}	else	{
		$msg =  'invalid file extension or file too large';
	}
}

echo $msg;
//returnJSON($json_arr);
}

chkSession();
switch($type){
	case 'add':
		getAddForm();
		break;
	case 'changeMenuStatus':
		changeMenuStatus($menu_id);
		break;
	case 'edit':
		getEditForm($menu_id);
		break;
	case 'addNewMenu':
		$menu_name_th		=	mysql_real_escape_string($_POST['name_th']);
		$menu_desc			=	mysql_real_escape_string($_POST['menu_desc']);
		$type_by_cooking	=	mysql_real_escape_string($_POST['typeCook']);
		$type_by_meat		=	mysql_real_escape_string($_POST['typeMeat']);
		$price				=	mysql_real_escape_string($_POST['price']);
		$unit				=	mysql_real_escape_string($_POST['unit']);
		$stock_id			=	mysql_real_escape_string($_POST['stock_id']);
		$active				=	mysql_real_escape_string($_POST['status']);
		$future_price		=	mysql_real_escape_string($_POST['future_price']);
		$future_date		=	mysql_real_escape_string($_POST['future_date']);
		$file				=	mysql_real_escape_string($_POST['file']);
		addNewMenu($menu_name_th, $menu_desc, $type_by_cooking, $type_by_meat, $price, $unit, $active, $future_price, $future_date, $stock_id);
		break;
	case 'updateMenu':
		$menu_id			=	mysql_real_escape_string($_POST['menu_id']);
		$menu_name_th		=	mysql_real_escape_string($_POST['name_th']);
		$menu_desc			=	mysql_real_escape_string($_POST['menu_desc']);
		$type_by_cooking	=	mysql_real_escape_string($_POST['typeCook']);
		$type_by_meat		=	mysql_real_escape_string($_POST['typeMeat']);
		$price				=	mysql_real_escape_string($_POST['price']);
		$unit				=	mysql_real_escape_string($_POST['unit']);
		$stock_id			=	mysql_real_escape_string($_POST['stock_id']);
		$active				=	mysql_real_escape_string($_POST['status']);
		$future_price		=	mysql_real_escape_string($_POST['future_price']);
		$future_date		=	mysql_real_escape_string($_POST['future_date']);
		$file				=	mysql_real_escape_string($_POST['file']);

		updateMenu($menu_id, $menu_name_th, $menu_desc, $type_by_cooking, $type_by_meat, $price, $unit, $active, $future_price, $future_date, $stock_id);
		break;
	case 'uploadImage':
		$menu_code			=	mysql_real_escape_string($_POST['menu_code']);
		if($menu_code!='') {
			uploadImage($menu_code);
		} else {
			echo $menu_code."d";
			echo "Something wrong";
		}
		break;
}
?>