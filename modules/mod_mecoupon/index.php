<?php
defined('R88PROJ') or die ($system_error);

$coupon_id				= $_POST['coupon_id'];
$type					= $_POST['type'];

function getAddForm(){
	$html = '<h3 class="title">Add New Coupon</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">COUPON NAME</span><input type="text" id="coupon_name" maxlength="50"></li>';
	$html .= '<li><span class="label">QTY.</span><input type="text" id="times" maxlength="20"></li>';
	$html .= '<li><span class="label">PRICE</span><input type="text" id="price" maxlength="30"></li>';
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
function getEditForm($coupon_id){
	$sql		= "SELECT * FROM `ecoupon` WHERE `id`='$coupon_id'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);

	$html = '<h3 class="title">Edit Massager Detail</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">COUPON NAME</span><input type="text" id="coupon_name" maxlength="50" value="'.$result['coupon_name'].'"></li>';
	$html .= '<li><span class="label">QTY.</span><input type="text" id="times" maxlength="20" value="'.$result['times'].'"></li>';
	$html .= '<li><span class="label">PRICE</span><input type="text" id="price" maxlength="30" value="'.$result['price'].'"></li>';
	$html .= '<li><span class="label">STATUS</span><select id="status">';
	if($result['status']==0){ 
		$html .= '<option value="0" selected>disable</option>';
	}else{
		$html .= '<option value="0">disable</option>';
	}
	if($result['status']==1){ 
		$html .= '<option value="1" selected>enable</option>';
	}else{
		$html .= '<option value="1">enable</option>';
	}
	$html .= '</select></li>';	
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="hidden" id="coupon_id" value="'.$coupon_id.'"><input type="button" id="submit" value="SAVE"> or <span id="cancel">Cancel</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
function addNewCoupon($coupon_name, $times, $price, $status){
	$sql	= "INSERT INTO `ecoupon` (`ecoupon_name`, `price`, `status`) VALUES ('$coupon_name', '$price', '$status')";
	$query	= mysql_query($sql);
	//`position`='0'

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'New Coupon Add Completed.'	
	);
	returnJSON($json_arr);
}
function updateCoupon($coupon_name, $times, $price, $status, $coupon_id){
	$sql	= "UPDATE `ecoupon` SET `ecoupon_name`='$coupon_name', `price`='$price', `status`='$status' WHERE `id`='$coupon_id'";	
	$query	= mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'Coupon Updated.'
	);
	returnJSON($json_arr);
}
function changeCouponStatus($coupon){
	$sql = "UPDATE `ecoupon` SET `status`=IF(`status`=1, 0, 1) WHERE `id`='$coupon'";
	$query	= mysql_query($sql);

	$sql = "SELECT `status` FROM `ecoupon` WHERE `id`='$coupon'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);
	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'coupon' => $coupon,
		'status' => $result['status']
	);
	returnJSON($json_arr);
	
}

chkSession();
switch($type){
	case 'add':
		getAddForm();
		break;
	case 'changeCouponStatus':
		changeCouponStatus($coupon_id);
		break;
	case 'edit':
		getEditForm($coupon_id);
		break;
	case 'addNewCoupon':
		$coupon_name		= mysql_real_escape_string($_POST['coupon_name']);
		$times			= mysql_real_escape_string($_POST['times']);
		$price			= mysql_real_escape_string($_POST['price']);
		$status			= mysql_real_escape_string($_POST['status']);
		addNewCoupon($coupon_name, $times, $price, $status);
		break;
	case 'updateCoupon':
		// $coupon_name, $times, $price, $status, $coupon_id
		$coupon_name		= mysql_real_escape_string($_POST['coupon_name']);
		$times			= mysql_real_escape_string($_POST['times']);
		$price			= mysql_real_escape_string($_POST['price']);
		$status			= mysql_real_escape_string($_POST['status']);
		updateCoupon($coupon_name, $times, $price, $status, $coupon_id);
		break;
}
?>