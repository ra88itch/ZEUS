<?php
defined('R88PROJ') or die ($system_error);

$stock_id		=	$_POST['stock_id'];
$type			=	$_POST['type'];

function getSelectUnit($selected){
	$selectStr	=	"<select id = 'unit'>";
	$sql		=	"SELECT * FROM `stock_unit` ORDER BY `unit_name` ASC";
	$query		=	mysql_query($sql);
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

function getStockName($stock_id){
	$sql	=	"SELECT `name` FROM `stock` WHERE `id` = '$stock_id'";
	$query	=	mysql_query($sql);
	$result	=	mysql_fetch_assoc($query);
	return $result['name'];
}
function getAddForm(){
	$html = '<h3 class="title">Add New Stock</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">NAME</span><input type="text" id="name" name = maxlength = "50"></li>';
	$html .= '<li><span class="label">UNIT</span>'.getSelectUnit(0).'</li>';
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
	$html .= '<li><span class="label">NAME</span><input type="text" id="name" maxlength = "50" value = "'.$result['name'].'"></li>';
	$html .= '<li><span class="label">UNIT</span>'.getSelectUnit($result['unit']).'</li>';
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="hidden" id="stock_id" value="'.$stock_id.'"><input type="button" id="submit" value="SAVE"> or <span id="cancel">Cancel</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}

function getHistory($stock_id){
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
}

function getTransactionForm($dotype,$stock_id){
	if($dotype == 1) {
		$typeStr	=	"INCREASE";
	} else {
		$typeStr	=	"DECREASE";
	}
	$sql		= "SELECT * FROM `stock` WHERE `id`='$stock_id'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);
	
	$html = '<h3 class="title">'.$typeStr.' '.$result['name'].'</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">QUANTITY</span><input type="text" id="quantity" maxlength = "5" value = "0"></li>';
	$html .= '<li><span class="label">AMOUNT</span><span id = "amount">'.$result['amount'].'</span></li>';
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="hidden" id="dotype" value="'.$dotype.'"><input type="hidden" id="stock_id" value="'.$stock_id.'"><input type="button" id="submit" value="SUBMIT"> or <span id="cancel">Cancel</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}

function addNewStock($name, $unit) {
	$sql	= "INSERT INTO `stock` (`name`, `amount`, `unit`) VALUES ('$name', '0', '$unit')";
	$query	= mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		//'msg' => $sql
		'msg' => 'New Stock Add Completed.'	
	);
	returnJSON($json_arr);
}
function updateStock($stock_id, $name, $unit){
	$sql	= "UPDATE `stock` SET `name`='$name', `unit`='$unit' WHERE `id`='$stock_id'";	
	$query	= mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'Stock Updated.'
	);
	returnJSON($json_arr);
}

function addNewTransaction($stock_id,$quantity,$dotype) {
	$sql	=	"SELECT `amount` FROM `stock` WHERE `id` = '$stock_id'";
	$query	=	mysql_query($sql);
	$result =	mysql_fetch_assoc($query);
	$amount =	$result['amount'];
	
	if($dotype == 1) {
		$amount	=	$amount + $quantity;
		$uplog_sql	=	"INSERT INTO `stock_detail` (`stock_id`, `in`, `employee_id`, `total`) VALUES ('$stock_id', '$quantity', '".$_SESSION['user_id']."', '$amount')";
		$uplog_query	=	mysql_query($uplog_sql);
	} else {
		$amount =	$amount - $quantity;
		$uplog_sql	=	"INSERT INTO `stock_detail` (`stock_id`, `out`, `employee_id`, `total`) VALUES ('$stock_id', '$quantity', '".$_SESSION['user_id']."', '$amount')";
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
		$name		=	mysql_real_escape_string($_POST['name']);
		$unit		=	mysql_real_escape_string($_POST['unit']);	
		if(isset($name) && $name != '' && isset($unit) && $unit != ''){
			addNewStock($name, $unit);
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
		$name		=	mysql_real_escape_string($_POST['name']);
		$unit		=	mysql_real_escape_string($_POST['unit']);
		updateStock($stock_id, $name, $unit);
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
		addNewTransaction($stock_id,$quantity,$dotype);
		break;
	case 'history' :
		$stock_id	=	mysql_real_escape_string($_POST['stock_id']);
		if(!isset($stock_id) || $stock_id < 0) {
			global $system_status_failed;
			$json_arr = array(
				'process' => $system_status_failed,
				'msg' => 'Missing Stock ID'
			);
			returnJSON($json_arr);
		} else {
			getHistory($stock_id);
		}
		break;
}
?>