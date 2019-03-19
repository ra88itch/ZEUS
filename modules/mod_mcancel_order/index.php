<?php
defined('R88PROJ') or die ('SYSTEM ERROR');

$type	= $_REQUEST['type'];

function changeOrderStatus($orderID, $status){					

	if($status == 5){
		$total_sql = "SELECT `price`*`unit` AS `total` FROM `order` WHERE `id`='".$orderID."'";
		$total_query = mysql_query($total_sql);	
		$total_results = mysql_fetch_assoc($total_query);

		$order_sql = "UPDATE `order` SET `order_status`='5', `end`=NOW(), `total`='".$total_results['total']."' WHERE `id`='".$orderID."'";
		$order_query = mysql_query($order_sql);	
	}else{
		$order_sql = "UPDATE `order` SET `order_status`='$status' WHERE `id`='".$orderID."'";
		$order_query = mysql_query($order_sql);
	}
	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success
	);
	returnJSON($json_arr);
}
chkSession();
switch($type){
	case 'confirm':
		$orderID =  mysql_real_escape_string($_REQUEST['orderID']);
		//changeOrderStatus($orderID, '5');
		break;
}

?>
