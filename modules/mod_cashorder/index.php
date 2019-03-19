<?php
defined('R88PROJ') or die ($system_error);

$type	=	$_POST['type'];

function chkbill($order, $name){
	$invID		=	createInv($name);
	$sumPrice	=	createOrder($invID,$order);

	if(isset($sumPrice) && $sumPrice != "") {
		global $system_status_success;
			$json_arr = array(
				'process'	=>	$system_status_success,
				'change' 	=>	$change,
			);
		returnJSON($json_arr);
	} else {
		global $system_status_failed;
			$json_arr = array(
				'process' => $system_status_failed,
				'msg' => 'Some Process Wrong'
			);
		returnJSON($json_arr);
	}
}
function createOrder($invID,$order){
	$orderLength	=	count($order);
	$sumPrice		=	0;
	$employee_id = accountDecrypt($_SESSION['user_id']);
	for($i = 0; $i < $orderLength; $i++) {
		$curDate = date("Y-m-d");
		$curTime = date("H:i:s");
		if($curTime < '07:00:00'){
			$lastDate = date ("Y-m-d", strtotime("-1day", strtotime($curDate)));
			$curTime = '23:59:59';
			$dateTime = $lastDate.' '.$curTime;
			//$sql = "INSERT INTO `order` (`order_inv`, `menu_id`, `unit`, `menu_desc`, `order_status`, `take_home`, `employee_id`, `start`) VALUES ('".$invoice_id."', '".$menu_id."', '".$order_unit."', '".$menu_desc."', '1', '".$take_home."', '".$user_id."', '$dateTime')";
			$sql	=	"INSERT INTO `order` (`order_inv`, `menu_id`, `unit`,  `menu_desc`, `order_status`, `take_home`, `price`, `employee_id`, `start`) VALUES ('".$invID."', '".$order[$i]['id']."', '".$order[$i]['number']."', '".$order[$i]['note']."', '1', '0', '".$order[$i]['price']."', '".$employee_id."', '$dateTime')";
		}else{
			$sql	=	"INSERT INTO `order` (`order_inv`, `menu_id`, `unit`,  `menu_desc`, `order_status`, `take_home`, `price`, `employee_id`) VALUES ('".$invID."', '".$order[$i]['id']."', '".$order[$i]['number']."', '".$order[$i]['note']."', '1', '0', '".$order[$i]['price']."', '".$employee_id."')";
		}
		//$sql	=	"INSERT INTO `order` (`order_inv`, `menu_id`, `unit`,  `menu_desc`, `order_status`, `take_home`, `price`, `employee_id`) VALUES ('".$invID."', '".$order[$i]['id']."', '".$order[$i]['number']."', '".$order[$i]['note']."', '1', '0', '".$order[$i]['price']."', '".$employee_id."')";
		$query	=	mysql_query($sql);
		$sumPrice	=	$sumPrice + $order[$i]['price'];
	}
	return $sumPrice;
}
function createInv($name){
	$sql	=	"INSERT INTO `invoice` (`zone`, `customer_value`, `zone_extra`) VALUES ('0','0','".$name."')";
	$query	=	mysql_query($sql);
	$invID	=	mysql_insert_id();
	return $invID;
}

chkSession();
switch($type){
	case 'chkbill' :
	$order		=	$_POST['order'];
	$name		=	$_POST['name'];
	if(!empty($order)) {
		chkbill($order, $name);
	} else {
		global $system_status_failed;
		$json_arr = array(
			'process' => $system_status_failed,
			'msg' => 'Missing Variable'
		);
		returnJSON($json_arr);
	}
	break;
}
?>