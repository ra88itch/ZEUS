<?php
defined('R88PROJ') or die ('SYSTEM ERROR');

function addOrder($invoice_id, $menu_id, $order_unit, $menu_desc, $take_home){
	$sql = "SELECT COUNT(*) FROM `order` WHERE `id`='".$order_id."' AND `order_status`='6' AND DATE(`start`)=DATE(NOW())";
	$query = mysql_query($sql);
	$count_result = mysql_fetch_assoc($query);

	if($count_result['COUNT(*)'] != '0'){
		global $system_status_failed;
		$json_arr = array(
			'process' => $system_status_failed
		);
	} else {
		$user_id = accountDecrypt($_SESSION['user_id']);

		$curDate = date("Y-m-d");
		$curTime = date("H:i:s");
		if($curTime < '07:00:00'){
			$lastDate = date ("Y-m-d", strtotime("-1day", strtotime($curDate)));
			$curTime = '23:59:59';
			$dateTime = $lastDate.' '.$curTime;
			$sql = "INSERT INTO `order` (`order_inv`, `menu_id`, `unit`, `menu_desc`, `order_status`, `take_home`, `employee_id`, `start`) VALUES ('".$invoice_id."', '".$menu_id."', '".$order_unit."', '".$menu_desc."', '1', '".$take_home."', '".$user_id."', '$dateTime')";
		}else{
			$sql = "INSERT INTO `order` (`order_inv`, `menu_id`, `unit`, `menu_desc`, `order_status`, `take_home`, `employee_id`) VALUES ('".$invoice_id."', '".$menu_id."', '".$order_unit."', '".$menu_desc."', '1', '".$take_home."', '".$user_id."')";
		}
		//$sql = "INSERT INTO `order` (`order_inv`, `menu_id`, `unit`, `menu_desc`, `order_status`, `take_home`, `employee_id`) VALUES ('".$invoice_id."', '".$menu_id."', '".$order_unit."', '".$menu_desc."', '1', '".$take_home."', '".$user_id."')";
		$query = mysql_query($sql);

		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success
		);
	}
	returnJSON($json_arr);
}

chkSession();

addOrder($invoice_id, $menu_id, $order_unit, $menu_desc, $take_home);
switch($type){
	case 'addOrder':
		$invoice_id	= mysql_real_escape_string($_REQUEST['invoiceID']);
		$order_id	= mysql_real_escape_string($_REQUEST['menuID']);
		$order_unit	= mysql_real_escape_string($_REQUEST['orderUnit']);
		$menu_desc	= mysql_real_escape_string($_REQUEST['menuDesc']);
		$take_home	= mysql_real_escape_string($_REQUEST['takeHome']);
		addOrder($invoice_id, $menu_id, $order_unit, $menu_desc, $take_home);
		break;
	case 'updateStatus':
		$order_id	= mysql_real_escape_string($_REQUEST['orderID']);
		$order_status	= mysql_real_escape_string($_REQUEST['orderStatus']);
		updateStatus($order_id, $order_status);
		break;
}
?>