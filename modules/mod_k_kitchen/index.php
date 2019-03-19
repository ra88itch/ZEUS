<?php
defined('R88PROJ') or die ('SYSTEM ERROR');

$type	= $_REQUEST['type'];
function getZoneName($invoice){
	$sql = "SELECT * FROM `invoice` WHERE `id`='".$invoice."'";
	$query = mysql_query($sql);
	$results = mysql_fetch_assoc($query);
	$zone_id = $results['zone'];
	
	/*if($results['zone'] > 0){
			$detail = getZoneDetail($results['zone']);
		}else{
			$detail = array($results['zone_extra'], '0');
		}*/
	if($zone_id>'0'){
		$sql = "SELECT * FROM `zone` WHERE `id`='".$zone_id."'";
		$query = mysql_query($sql);	
		$results = mysql_fetch_assoc($query);		
		return $results['zone'];
	}else{
		return $results['zone_extra'];
	}
}
function menuName($menu_id){
	$sql = "SELECT * FROM `menu` WHERE `id`='".$menu_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return array($results['menu_name_th'], $results['type_by_cooking']);
}
function getOrderList(){
	$details = array();
	$sql = "SELECT * FROM `order` WHERE ((`order_status`<'4' AND `end`='0000-00-00 00:00:00') OR `order_status`='7')  AND DATE(`start`) = DATE(NOW()) ORDER BY `start` DESC";
	$query = mysql_query($sql);	
	while($results = mysql_fetch_assoc($query)) {
		$zoneName = getZoneName($results['order_inv']);
		$menuDetail = menuName($results['menu_id']);
		if($menuDetail[1] == '8'){
			
		}else if($menuDetail[1] == '9'){
		
		}else if($menuDetail[1] == '16'){
		
		}else{
			array_push($details,array(
					'id' 				=>	$results['id'],
					'zone_name'			=>	$zoneName,
					'order_name'		=>	$menuDetail[0],
					'order_detail'		=>	$results['menu_desc'],
					'order_start'		=>	$results['start'],
					'order_status'		=>	$results['order_status'],
					'unit'				=>	$results['unit'],
					'take_home'			=>	$results['take_home'],
					'printed'			=>	$results['printed']
				)
			);
		}
		/*array_push($details,array(
				'id' 				=>	$results['id'],
				'zone_name'			=>	$zoneName,
				'order_name'		=>	$menuDetail[0],
				'order_detail'		=>	$results['menu_desc'],
				'order_start'		=>	$results['start'],
				'order_status'		=>	$results['order_status'],
				'unit'				=>	$results['unit'],
				'take_home'			=>	$results['take_home'],
				'printed'			=>	$results['printed']
			)
		);*/
	}

	if(empty($details)) {
		global $system_status_failed;
		$json_arr = array(
			'process' => $system_status_failed
		);
	} else {
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'details' => $details
		);
	}
	returnJSON($json_arr);
}
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
function deleteOrder($orderID){

	/*$order_sql = "SELECT * FROM `order` WHERE `id`='".$orderID."'";
	$order_query = mysql_query($order_sql);	
	$results = mysql_fetch_assoc($order_query);

	$getMenuName = getMenuName($results['menu_id']);
	addLog('ลบรายการอาหาร - '.$getMenuName, $results['order_inv']);

	$order_sql = "DELETE FROM `order` WHERE `id`='".$orderID."'";
	$order_query = mysql_query($order_sql);	*/

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success
	);
	returnJSON($json_arr);
} 
chkSession();
switch($type){
	case 'getOrderList':
		getOrderList();
		break;
	case 'cooking':
		$orderID =  mysql_real_escape_string($_REQUEST['orderID']);
		changeOrderStatus($orderID, '3');
		break;
	case 'already':
		$orderID =  mysql_real_escape_string($_REQUEST['orderID']);
		changeOrderStatus($orderID, '4');
		break;
	case 'finish':
		$orderID =  mysql_real_escape_string($_REQUEST['orderID']);
		changeOrderStatus($orderID, '5');
		break;
	case 'cancel':
		$orderID =  mysql_real_escape_string($_REQUEST['orderID']);
		changeOrderStatus($orderID, '6');
		break;
	case 'deleteOrder':
		$orderID =  mysql_real_escape_string($_REQUEST['orderID']);
		deleteOrder($orderID);
		break;
}

?>
