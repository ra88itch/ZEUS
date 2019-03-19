<?php
defined('R88PROJ') or die ($system_error);

$type	= $_REQUEST['type'];

function createInvoice($zone_key, $zone_value, $customer_value){
	$sql = "INSERT INTO `invoice` (`zone`,`zone_extra`,`customer_value`) VALUES ('".$zone_key."','".$zone_value."','".$customer_value."')";
	$query = mysql_query($sql);
	return mysql_insert_id();
}
function addToOrder($coupon_id, $unit){
	$createInvoice = createInvoice(0, "ซื้อคูปอง", 0);

	$sql = "SELECT * FROM `coupon` WHERE `id`='$coupon_id' AND `active`='1'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);

	$sql = "INSERT INTO `order_coupon` (`order_inv`,`coupon_id`,`unit`,`price`,`total`,`employee_id`) VALUES ('".$createInvoice."','".$coupon_id."','".$unit."','".$result['price']."','".($result['price']*$unit)."','".accountDecrypt($_SESSION['user_id'])."')";
	$query = mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'เพิ่มรายการเข้าระบบเรียบร้อย'
	);	
	returnJSON($json_arr);
}

chkSession();
switch($type){
	case 'addToOrder':
		$coupon_id	= mysql_real_escape_string($_REQUEST['couponID']);
		$unit	= mysql_real_escape_string($_REQUEST['unit']);
		
		addToOrder($coupon_id, $unit);
		break;
}



?>