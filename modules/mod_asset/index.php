<?php
defined('R88PROJ') or die ($system_error);

$stock_id		=	$_POST['stock_id'];
$type			=	$_POST['type'];

function getSelectUnit($selected){
	$selectStr	=	'<select id="unit">';
	if($selected == 0){
		$selectStr.=	'<option value="0">Add New Unit</option>';
	}	
	$sql		=	"SELECT * FROM `stock_unit` WHERE `unit_name`!=''";
	$query		=	mysql_query($sql);
	while($result = mysql_fetch_assoc($query)) {
		if(isset($selected) && $selected == $result['id']) {
			$selectedStr = "selected";
		} else {
			$selectedStr = '';
		}
		$selectStr.=	'<option value = "'.$result['id'].'" '.$selectedStr.'>'.$result['unit_name'].'</option>';
	}
	$selectStr.=	'</select>';
	return $selectStr;
}

function getStockName($stock_id){
	$sql	=	"SELECT `name` FROM `stock` WHERE `id` = '$stock_id'";
	$query	=	mysql_query($sql);
	$result	=	mysql_fetch_assoc($query);
	return $result['name'];
}
function getAddForm(){
	$html = '<h3 class="title">Add New Stock</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">รหัส</span><input type="text" id="code" maxlength="20"></li>';
	$html .= '<li><span class="label">รายการ</span><input type="text" id="name" maxlength="50"></li>';
	$html .= '<li><span class="label">หน่วยนับ</span>'.getSelectUnit(0).' / <input type="text" id="new_unit" maxlength="10" style="width:150px;"></li>';
	$html .= '<li><span class="label">จำนวนขั้นต่ำ</span><input type="text" id="minimum" maxlength="10" style="width:150px;"></li>';
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="button" id="submit" value="ADD"> or <span id="cancel">Cancel</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}

function getEditForm($stock_id){
	$sql	= "SELECT * FROM `stock` WHERE `id`='$stock_id'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);

	$html = '<h3 class="title">Edit Stock Name</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">รหัส</span><input type="text" id="code" maxlength="20" value="'.$result['code'].'"></li>';
	$html .= '<li><span class="label">รายการ</span><input type="text" id="name" maxlength = "50" value = "'.$result['name'].'"></li>';
	$html .= '<li><span class="label">หน่วยนับ</span>'.getSelectUnit($result['unit']).'</li>';
	$html .= '<li><span class="label">จำนวนปัจจุบัน</span><input type="text" id="amount" maxlength="10" style="width:150px;" value="'.$result['amount'].'"></li>';
	$html .= '<li><span class="label">จำนวนขั้นต่ำ</span><input type="text" id="minimum" maxlength="10" style="width:150px;" value="'.$result['minimum'].'"></li>';$html .= '</ul>';
	$html .= '<div class="submit"><input type="hidden" id="stock_id" value="'.$stock_id.'"><input type="button" id="submit" value="SAVE"> or <span id="cancel">Cancel</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}

/*function getHistory($stock_id){
	$sql	=	"SELECT * FROM `stock_detail` WHERE `stock_id`='$stock_id' ORDER BY `date_time` DESC";
	$query	=	mysql_query($sql);
	
	$html	=	'<h3 class="title">History of '.getStockName($stock_id).'</h3>';
	$html	.=	'<table id = "history"><thead><tr><th>INCREASE</th><th>DECREASE</th><th>AMOUNT</th><th>TIME</th></tr></thead>';
	while($result = mysql_fetch_assoc($query)) {
		$html	.=	'<tr><td>'.$result['in'].'</td><td>'.$result['out'].'</td><td>'.$result['total'].'</td><td>'.$result['date_time'].'</td></tr>';
	}
	$html	.=	'</table">';
	$html	.=	'<div class="submit"><span id="cancel">Close</span></div>';
	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}*/
function selectDepartment(){
	$html = '<select>';
	$sql		= "SELECT * FROM `employee_position`";
	$query		= mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		$html .= '<option value="'.$result['id'].'">'.$result['position_name'].'</option>';
	}
	$html .= '</select>';
	return $html;
}
function getTransactionForm($dotype,$stock_id){
	if($dotype == 1) {
		$typeStr	=	"เพิ่มรายการ";
	} else {
		$typeStr	=	"เบิกรายการ";
	}
	$sql		= "SELECT * FROM `stock` WHERE `id`='$stock_id'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);
	
	$html = '<h3 class="title">'.$typeStr.' '.$result['name'].'</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">วันที่</span><input type="text" id="date"></li>';
	
	if($dotype != 1) {
		$html .= '<li><span class="label">แผนก</span>'.selectDepartment().'</li>';
	}
	$html .= '<li><span class="label">จำนวน</span><input type="text" id="quantity" maxlength = "5" value = "0"></li>';
	$html .= '<li><span class="label">ยอดคงเหลือ</span><span id = "amount">'.$result['amount'].'</span></li>';
	if($dotype != 1) {		
		$html .= '<li><span class="label">อธิบาย</span><input type="text" id="comment" maxlength = "256" value = ""></li>';
	}
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="hidden" id="dotype" value="'.$dotype.'"><input type="hidden" id="stock_id" value="'.$stock_id.'"><input type="button" id="submit" value="SUBMIT"> or <span id="cancel">Cancel</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}

function addNewUnit($new_unit){
	$sql	= "INSERT INTO `stock_unit` (`unit_name`) VALUES ('$new_unit')";
	$query	= mysql_query($sql);
	$unit	= mysql_insert_id();
	return $unit;
}
function addNewStock($name, $unit, $new_unit, $minimum, $code) {
	if($unit=='0'){
		$unit = addNewUnit($new_unit);
	}else{
		$unit = $unit;
	}
	$sql	= "INSERT INTO `stock` (`name`, `amount`, `unit`, `store_stock`, `minimum`, `code`) VALUES ('$name', '0', '$unit', '2', '$minimum', '$code')";
	$query	= mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'New Stock Add Completed.'	
	);
	returnJSON($json_arr);
}
function updateStock($stock_id, $name, $unit, $minimum, $amount, $code){
	$sql	= "UPDATE `stock` SET `name`='$name', `unit`='$unit', `minimum`='$minimum', `amount`='$amount', `code`='$code' WHERE `id`='$stock_id'";	
	$query	= mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'Stock Updated.'
	);
	returnJSON($json_arr);
}

function addNewTransaction($stock_id,$quantity,$dotype,$employee_position,$comment) {
	$sql	=	"SELECT `amount` FROM `stock` WHERE `id` = '$stock_id'";
	$query	=	mysql_query($sql);
	$result =	mysql_fetch_assoc($query);
	$amount =	$result['amount'];
	
	if($dotype == 1) {
		$amount	=	$amount + $quantity;
		$uplog_sql	=	"INSERT INTO `stock_detail` (`stock_id`, `in`, `department`, `total`) VALUES ('$stock_id', '$quantity', '0', '$amount')";
		$uplog_query	=	mysql_query($uplog_sql);
	} else {
		$amount =	$amount - $quantity;
		$uplog_sql	=	"INSERT INTO `stock_detail` (`stock_id`, `out`, `department`, `total`, `comment`) VALUES ('$stock_id', '$quantity', '$employee_position', '$amount', '$comment')";
		$uplog_query	=	mysql_query($uplog_sql);
	}
	
	$sql	=	"UPDATE `stock` SET `amount`='$amount' WHERE `id`='$stock_id'";
	$query	=	mysql_query($sql);
	
	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'Stock Updated.'
	);
	returnJSON($json_arr);
}
chkSession();
switch($type){
	case 'add':
		getAddForm();
		break;
	case 'edit':
		getEditForm($stock_id);
		break;
	case 'addNewStock':
		$code		=	mysql_real_escape_string($_POST['code']);
		$name		=	mysql_real_escape_string($_POST['name']);
		$unit		=	mysql_real_escape_string($_POST['unit']);
		$new_unit	=	mysql_real_escape_string($_POST['new_unit']);
		$minimum	=	mysql_real_escape_string($_POST['minimum']);
		if(isset($name) && $name != '' && isset($unit) && $unit != ''){
			addNewStock($name, $unit, $new_unit, $minimum, $code);
		}else{
			global $system_status_failed;
			$json_arr = array(
				'process' => $system_status_failed,
				'msg' => 'Missing Variable'
			);
			returnJSON($json_arr);
		}
		break;
	case 'updateStock':
		$stock_id	=	mysql_real_escape_string($_POST['stock_id']);
		$code		=	mysql_real_escape_string($_POST['code']);
		$name		=	mysql_real_escape_string($_POST['name']);
		$unit		=	mysql_real_escape_string($_POST['unit']);
		$minimum	=	mysql_real_escape_string($_POST['minimum']);
		$amount		=	mysql_real_escape_string($_POST['amount']);

		updateStock($stock_id, $name, $unit, $minimum, $amount, $code);
		break;
	case 'transaction' :
		$stock_id	=	mysql_real_escape_string($_POST['stock_id']);
		$dotype		=	mysql_real_escape_string($_POST['dotype']);
		getTransactionForm($dotype,$stock_id);
		break;
	case 'addNewTransaction' :
		$stock_id	=	mysql_real_escape_string($_POST['stock_id']);
		$quantity	=	mysql_real_escape_string($_POST['quantity']);
		$dotype		=	mysql_real_escape_string($_POST['dotype']);		
		$comment	=	mysql_real_escape_string($_POST['comment']);
		$employee_position		=	mysql_real_escape_string($_POST['employee_position']);
		addNewTransaction($stock_id,$quantity,$dotype,$employee_position,$comment);
		break;
}
?>