<?php
defined('R88PROJ') or die ('SYSTEM ERROR');

$type	= $_REQUEST['type'];

function service_detail($invoiceID){
	$sql = "SELECT * FROM `invoice` WHERE `id`='".$invoiceID."'";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	$getZoneDetail = getZoneDetail($results['zone']);
	return array($getZoneDetail[0], $results['checkin']);
}
function getZoneDetail($zone_id){
	$sql = "SELECT * FROM `zone` WHERE `id`='".$zone_id."'";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return array($results['zone'], $results['zone_category']);
	//return $sql;
}
function getMemberDetail($member_id){
	$sql = "SELECT * FROM `customer` WHERE `id`='".$member_id."'";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return array($results['firstname'].' '.$results['lastname'].' ('.$member_id.')', '5');
}
function getInvoice(){
	$invoice = array();
	$sql = "SELECT * FROM `invoice` WHERE `checkout` = '0000-00-00 00:00:00'";
	$query = mysql_query($sql);	
	while($results = mysql_fetch_assoc($query)) {
		
		//$detail = getZoneDetail($results['zone']);
		// ADD NEW
		if($results['zone'] > 0){
			$detail = getZoneDetail($results['zone']);
		}else if($results['zone'] == 0 && $results['member_id'] == 0){
			$detail = array($results['zone_extra'], '0');
		}else{
			$detail = getMemberDetail($results['member_id']);
		}

		array_push($invoice,array(
				'id' 				=>	$results['id'],
				'zone'				=>	$detail[0],
				'zone_category'		=>	$detail[1],
				'checkin'			=>	$results['checkin']
			)
		);	
	}
	if(empty($invoice)) {
		global $system_status_failed;
		$json_arr = array(
			'process' => $system_status_failed
		);
	} else {
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'invoices' => $invoice
		);
	}
	returnJSON($json_arr);
}

function employeeName($employee_id){
	$sql = "SELECT * FROM `employee` WHERE `id`='".$employee_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['nickname'];
}
function accountName($employee_id){
	$sql = "SELECT * FROM `account` WHERE `id`='".$employee_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['firstname'].' '.$results['lastname'];
}
function orderStatusName($order_status){
	$sql = "SELECT * FROM `order_status` WHERE `id`='".$order_status."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['orderstatusname'];
}
function menuName($menu_id){
	$sql = "SELECT * FROM `menu` WHERE `id`='".$menu_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['menu_name_th'];
}
function getCustomerDetail($customer_id){
	$sql = "SELECT * FROM `customer` WHERE `id`='".$customer_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['firstname'].' '.$results['lastname'];
}
function geteCouponDetail($coupon_id){
	$sql = "SELECT * FROM `ecoupon` WHERE `id`='".$coupon_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['ecoupon_name'];
}
function getCouponDetail($coupon_id){
	$sql = "SELECT * FROM `coupon` WHERE `id`='".$coupon_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['coupon_name'];
}
function getInvoiceDetails($invoiceID){
	$details = array();

	$cash_sql = "SELECT * FROM `order_cash` WHERE `order_inv` = '".$invoiceID."'";
	$cash_query = mysql_query($cash_sql);	
	while($results = mysql_fetch_assoc($cash_query)) {
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	'เบิกเงินสด',
				'order_start'		=>	$results['start'],
				'order_end'			=>	$results['end'],
				'unit'				=>	'1',
				'price'				=>	$results['price'],
				'takehome'			=>	'0',
				'total'				=>	$results['price'],
				'thisis'			=>	'cash'
			)
		);
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	'ค่าบริการเบิกเงินสด',
				'order_start'		=>	$results['start'],
				'order_end'			=>	$results['end'],
				'unit'				=>	'1',
				'price'				=>	$results['charge'],
				'takehome'			=>	'0',
				'total'				=>	$results['charge'],
				'thisis'			=>	'cash'
			)
		);
	}

	$ecoupon_total = 0;
	$ecoupon_sql = "SELECT * FROM `order_ecoupon` WHERE `order_inv` = '".$invoiceID."'";
	$ecoupon_query = mysql_query($ecoupon_sql);	
	while($results = mysql_fetch_assoc($ecoupon_query)) {
		$ecoupon_total = $ecoupon_total+$results['total'];
		$ecouponDetail = geteCouponDetail($results['coupon_id']);
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	$ecouponDetail,
				'unit'				=>	$results['unit'],
				'price'				=>	$results['price'],
				'total'				=>	$results['total'],
				'thisis'			=>	'ecoupon'
			)
		);	
	}
	
	$coupon_sql = "SELECT * FROM `order_coupon` WHERE `order_inv` = '".$invoiceID."'";
	$coupon_query = mysql_query($coupon_sql);	
	while($results = mysql_fetch_assoc($coupon_query)) {
		$couponDetail = getCouponDetail($results['coupon_id']);
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	$couponDetail,
				'unit'				=>	$results['unit'],
				'price'				=>	$results['price'],
				'total'				=>	$results['total'],
				'thisis'			=>	'coupon'
			)
		);	
	}

	$restaurant_sql = "SELECT * FROM `order_restaurant` WHERE `order_inv` = '".$invoiceID."'";
	$restaurant_query = mysql_query($restaurant_sql);	
	while($results = mysql_fetch_assoc($restaurant_query)) {
		$zoneName = getZoneDetail($results['zone_id']);
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	$zoneName[0],
				'order_start'		=>	$results['start'],
				'order_end'			=>	$results['end'],
				'unit'				=>	'1',
				'price'				=>	'',
				'takehome'			=>	'0',
				'times_min'			=>	$results['times_in_min'],
				'total'				=>	$results['total'],
				'thisis'			=>	'restaurant'
			)
		);	
	}

	$massage_sql = "SELECT * FROM `order_massage` WHERE `order_inv` = '".$invoiceID."'";
	$massage_query = mysql_query($massage_sql);	
	while($results = mysql_fetch_assoc($massage_query)) {
		$zoneName = getZoneDetail($results['zone_id']);
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	$zoneName[0],
				'employee_name'		=>	employeeName($results['employee_id']),
				'order_start'		=>	$results['start'],
				'order_end'			=>	$results['end'],
				'unit'				=>	'1',
				'price'				=>	'',
				'takehome'			=>	'0',
				'times_min'			=>	$results['times_in_min'],
				'total'				=>	$results['total'],
				'thisis'			=>	'massage'
			)
		);	
	}

	$snooker_sql = "SELECT * FROM `order_snooker` WHERE `order_inv` = '".$invoiceID."'";
	$snooker_query = mysql_query($snooker_sql);	
	while($results = mysql_fetch_assoc($snooker_query)) {
		
		$zoneName = getZoneDetail($results['zone_id']);
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	$zoneName[0],
				'employee_name'		=>	employeeName($results['employee_id']),
				'order_start'		=>	$results['start'],
				'order_end'			=>	$results['end'],
				'unit'				=>	'1',
				'price'				=>	'',
				'takehome'			=>	'0',
				'times_min'			=>	$results['times_in_min'],
				'total'				=>	$results['total'],
				'thisis'			=>	'snooker'
			)
		);	
	}

	$sauna_sql = "SELECT * FROM `order_sauna` WHERE `order_inv` = '".$invoiceID."'";
	$sauna_query = mysql_query($sauna_sql);	
	while($results = mysql_fetch_assoc($sauna_query)) {
		if($results['zone_id'] > 0){
			$zoneName = getZoneDetail($results['zone_id']);
		}else{
			$zoneName = getMemberDetail($results['customer_id']);
		}
		//$zoneName = getZoneDetail($results['zone_id']);
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	$zoneName[0],
				'employee_name'		=>	employeeName($results['employee_id']),
				'order_start'		=>	$results['start'],
				'order_end'			=>	$results['end'],
				'unit'				=>	'1',
				'price'				=>	'',
				'takehome'			=>	'0',
				'customer_id'		=>	$results['customer_id'],
				'times_min'			=>	$results['times_in_min'],
				'total'				=>	$results['total'],
				'thisis'			=>	'sauna'
			)
		);	
	}

	$order_sql = "SELECT * FROM `order` WHERE `order_inv` = '".$invoiceID."'";
	$order_query = mysql_query($order_sql);	
	while($results = mysql_fetch_assoc($order_query)) {
		$employeeName = accountName($results['employee_id']);
		$menuName = menuName($results['menu_id']);
		$menuName .= '['.$employeeName.']';
		if($results['order_status']=='7'){
			$results['price'] = 0;
		}
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	$menuName,
				'employee_name'		=>	employeeName($results['employee_id']),
				'order_start'		=>	$results['start'],
				'order_end'			=>	$results['end'],
				'unit'				=>	$results['unit'],
				'price'				=>	$results['price'],
				'status'			=>	orderStatusName($results['order_status']),
				'status_id'			=>	$results['order_status'],
				'takehome'			=>	$results['takehome'],
				'total'				=>	$results['total'],
				'thisis'			=>	'order'
			)
		);	
	}

	$sauna_sql = "SELECT * FROM `order_member` WHERE `order_inv` = '".$invoiceID."'";
	$sauna_query = mysql_query($sauna_sql);	
	while($results = mysql_fetch_assoc($sauna_query)) {
		$customerName = getCustomerDetail($results['customer_id']);
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	$customerName,
				'employee_name'		=>	employeeName($results['employee_id']),
				'order_start'		=>	$results['start'],
				'order_end'			=>	$results['end'],
				'unit'				=>	'',
				'price'				=>	'',
				'takehome'			=>	'0',
				'total'				=>	$results['total'],
				'thisis'			=>	'member'
			)
		);	
	}


	if(empty($details)) {
		global $system_status_failed;
		$json_arr = array(
			'process' => $system_status_failed
		);
	} else {
		global $system_status_success;
		$service_detail = service_detail($invoiceID);
		$json_arr = array(
			'process' => $system_status_success,
			'invoice_id' => $invoiceID,
			'service_name' => $service_detail[0],
			'date' => $service_detail[1],
			'details' => $details
		);
	}
	returnJSON($json_arr);
}
function chkOrderPrice($menuID){
	$price_sql = "SELECT `price` FROM `menu` WHERE `id`='".$menuID."'";
	$price_query = mysql_query($price_sql);
	$result = mysql_fetch_assoc($price_query);
	return $result['price'];
}
function addOrder($invoiceID, $units, $menuID, $takeHome, $orderDesc){
	$order_sql = "INSERT INTO `order` (`order_inv`, `menu_id`, `unit`, `menu_desc`, `take_home`, `employee_id`,`price`) VALUES ('".$invoiceID."', '".$menuID."', '".$units."', '".$orderDesc."', '".$takeHome."','".accountDecrypt($_SESSION['user_id'])."','".chkOrderPrice($menuID)."')";
	$order_query = mysql_query($order_sql);	
	$lastID = mysql_insert_id();

	$details = array();
	$order_sql = "SELECT * FROM `order` WHERE `id` = '".$lastID."'";
	$order_query = mysql_query($order_sql);	
	while($results = mysql_fetch_assoc($order_query)) {
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	menuName($results['menu_id']),
				'employee_name'		=>	employeeName($results['employee_id']),
				'order_start'		=>	$results['start'],
				'order_end'			=>	$results['end'],
				'unit'				=>	$results['unit'],
				'price'				=>	$results['price'],
				'total'				=>	$results['total'],
				'status'			=>	orderStatusName($results['order_status']),
				'status_id'			=>	$results['order_status'],
				'takehome'			=>	$results['takehome'],
				'thisis'			=>	'order'
			)
		);	
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
function NONO_deleteOrder($orderID){

	$order_sql = "SELECT * FROM `order` WHERE `id`='".$orderID."'";
	$order_query = mysql_query($order_sql);	
	$results = mysql_fetch_assoc($order_query);

	cancelCancel($orderID);

	$getMenuName = getMenuName($results['menu_id']);
	addLog('ลบรายการอาหาร - '.$getMenuName, $results['order_inv']);

	$order_sql = "DELETE FROM `order` WHERE `id`='".$orderID."'";
	$order_query = mysql_query($order_sql);	

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'invoice_id' => $results['order_inv']
	);
	returnJSON($json_arr);
}
function cancelOrder($orderID){
	$order_sql = "UPDATE `order` SET `order_status`='7' WHERE `id`='".$orderID."'";
	$order_query = mysql_query($order_sql);	

	cancelCancel($orderID);

	$order_sql = "SELECT * FROM `order` WHERE `id`='".$orderID."'";
	$order_query = mysql_query($order_sql);	
	$results = mysql_fetch_assoc($order_query);

	$getMenuName = getMenuName($results['menu_id']);
	addLog('ลูกค้ายกเลิกรายการอาหาร - '.$getMenuName);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'invoice_id' => $results['order_inv']
	);
	returnJSON($json_arr);
}
function finishOrder($orderID){
	$total_sql = "SELECT `price`*`unit` AS `total` FROM `order` WHERE `id`='".$orderID."'";
	$total_query = mysql_query($total_sql);	
	$total_results = mysql_fetch_assoc($total_query);

	$order_sql = "UPDATE `order` SET `order_status`='5', `end`=NOW(), `total`='".$total_results['total']."' WHERE `id`='".$orderID."'";
	$order_query = mysql_query($order_sql);	

	$order_sql = "SELECT `order_inv` FROM `order` WHERE `id`='".$orderID."'";
	$order_query = mysql_query($order_sql);	
	$results = mysql_fetch_assoc($order_query);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'invoice_id' => $results['order_inv']
	);
	returnJSON($json_arr);
}
function calTime($orderID, $tableName){
	if($tableName=='order_massage'){
		$get_times_sql = "SELECT `order_inv`, `zone_id`, `price`, `employee_id`, TIMESTAMPDIFF(MINUTE,`start`,`end`) AS `minutes` FROM `".$tableName."` WHERE `id`='".$orderID."'";
	}else{
		$get_times_sql = "SELECT `order_inv`, `zone_id`, `price`, TIMESTAMPDIFF(MINUTE,`start`,`end`) AS `minutes` FROM `".$tableName."` WHERE `id`='".$orderID."'";
	}	
	$get_times_query = mysql_query($get_times_sql);
	$results = mysql_fetch_assoc($get_times_query);
	$minutes = $results['minutes'];
	
	if($tableName=='order_massage'){
		resetEmployeeReserved($results['employee_id']);
		$minutes = 120;
		$min_2_hour = 2;
		$update_total_sql = "UPDATE `".$tableName."` SET `times_in_min`='".$minutes."', `total`='".$results['price']*$min_2_hour."' WHERE `id`='".$orderID."'";
		$update_total_query = mysql_query($update_total_sql);
		resetZoneReserved($results['zone_id']);
	}else if($tableName=='order_snooker'){

		addLog('หยุดเวลาใช้บริการโต๊ะสนุ๊ก', $results['order_inv']);

		$update_total_sql = "UPDATE `".$tableName."` SET `times_in_min`='".$minutes."', `total`='".$results['price']*($minutes/60)."' WHERE `id`='".$orderID."'"; 
		$update_total_query = mysql_query($update_total_sql);

		if($results['zone_id'] > 1000){
			resetZoneReserved($results['zone_id']);
		}
		/*include('LXTelnet.php');
		$lx = new LXTelnet();
		if($lx->init() === false){ return; }
		if($lx->login() === false){ 
			return; 
		}else{ 
			$cmd = getCmdByZone($results['zone_id']);
			$resp = $lx->control($cmd);
			$lx->close();
			if($resp!=false){
				$update_total_sql = "UPDATE `".$tableName."` SET `times_in_min`='".$minutes."', `total`='".$results['price']*($minutes/60)."' WHERE `id`='".$orderID."'"; 
				$update_total_query = mysql_query($update_total_sql);
				resetZoneReserved($results['zone_id']);
			}			
		}	*/	
	}else{
		$update_total_sql = "UPDATE `".$tableName."` SET `times_in_min`='".$minutes."', `total`='".$results['price']."' WHERE `id`='".$orderID."'";
		$update_total_query = mysql_query($update_total_sql);
		resetZoneReserved($results['zone_id']);
	}
	//$update_total_query = mysql_query($update_total_sql);
	return $results['order_inv'];
}
function resetZoneReserved($zone_id){
	
	$sql = "UPDATE `zone` SET `reserved`='0' WHERE `id`='".$zone_id."'";
	if($zone_id >= 52 && $zone_id <= 1550){
		//$sql = "UPDATE `zone` SET `reserved`='0', `active`='0' WHERE `id`='".$zone_id."'";
	}
	//$sql = "UPDATE `zone` SET `reserved`='0' WHERE `id`='".$zone_id."'";
	$query = mysql_query($sql);	
}
function resetEmployeeReserved($employee_id){
	$sql = "UPDATE `employee` SET `reserved`='0' WHERE `id`='".$employee_id."'";
	$query = mysql_query($sql);	
}
function finishMassage($orderID){
	$order_sql = "UPDATE `order_massage` SET `end`=NOW() WHERE `id`='".$orderID."'";
	$order_query = mysql_query($order_sql);	

	$inv_id = calTime($orderID, 'order_massage');

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'invoice_id' => $inv_id
	);
	returnJSON($json_arr);
}
function finishSauna($orderID){
	$order_sql = "UPDATE `order_sauna` SET `end`=NOW() WHERE `id`='".$orderID."'";
	$order_query = mysql_query($order_sql);
	
	$inv_id = calTime($orderID, 'order_sauna');

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'invoice_id' => $inv_id
	);
	returnJSON($json_arr);
}
function finishRestaurant($orderID){
	$order_sql = "UPDATE `order_restaurant` SET `end`=NOW() WHERE `id`='".$orderID."'";
	$order_query = mysql_query($order_sql);
	
	$inv_id = calTime($orderID, 'order_restaurant');

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'invoice_id' => $inv_id
	);
	returnJSON($json_arr);
}
function finishSnooker($orderID){
	$order_sql = "UPDATE `order_snooker` SET `end`=NOW() WHERE `id`='".$orderID."'";
	$order_query = mysql_query($order_sql);
	
	$inv_id = calTime($orderID, 'order_snooker');

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'invoice_id' => $inv_id
	);
	returnJSON($json_arr);
}
function getCmdByZone($zone){
//$cmd_closes	= ['$00W01F#17', '$00W02F#14', '$00W03F#15', '$00W04F#12', '$00W05F#13', '$00W06F#10', '$00W07F#11', '$00W08F#1E', '$00W09F#1F', '$00W10F#17', '$00W11F#16', '$00W12F#15', '$00W13F#14', '$00W14F#13', '$00W15F#12'];

	switch($zone){
	case '39':
		$cmd = '$00W01F#17';
		break;
	case '40':
		$cmd = '$00W02F#14';
		break;
	case '41':
		$cmd = '$00W03F#15';
		break;
	case '42':
		$cmd = '$00W04F#12';
		break;
	case '43':
		$cmd = '$00W05F#13';
		break;
	case '44':
		$cmd = '$00W06F#10';
		break;
	case '45':
		$cmd = '$00W07F#11';
		break;
	case '46':
		$cmd = '$00W08F#1E';
		break;
	case '47':
		$cmd = '$00W11F#16';
		break;
	case '48':
		$cmd = '$00W12F#15';
		break;
	case '49':
		$cmd = '$00W13F#14';
		break;
	case '50':
		$cmd = '$00W14F#13';
		break;
	case '51':
		$cmd = '$00W15F#12';
		break;
	}
	return $cmd;
}
function finishMassageHours($orderID, $massage_type, $massager_id, $hours, $total, $coupon){
	$minutes = $hours*60;
	$order_sql = "UPDATE `order_massage` SET `end`=NOW(), `massage_type`='".$massage_type."', `employee_id`='".$massager_id."', `times_in_min`='$minutes', `total`='".$total."', `coupon`='".$coupon."'  WHERE `id`='".$orderID."'";
	$order_query = mysql_query($order_sql);	

	$inv_id = manualTime($orderID, 'order_massage');

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'invoice_id' => $inv_id
	);
	returnJSON($json_arr);
}
function manualTime($orderID, $tableName){
	$get_times_sql = "SELECT `order_inv`, `zone_id`, `price`, `employee_id`, `times_in_min` FROM `".$tableName."` WHERE `id`='".$orderID."'";
	$get_times_query = mysql_query($get_times_sql);
	$results = mysql_fetch_assoc($get_times_query);

	resetZoneReserved($results['zone_id']);

	return $results['order_inv'];
}

chkSession();
switch($type){
	case 'getInvoice':
		getInvoice();
		break;
	case 'getInvoiceDetails':
		$invoiceID =  mysql_real_escape_string($_REQUEST['invoiceID']);
		getInvoiceDetails($invoiceID);
		break;
	case 'addOrder':
		$invoiceID =  mysql_real_escape_string($_REQUEST['invoiceID']);
		$units =  mysql_real_escape_string($_REQUEST['units']);
		$menuID =  mysql_real_escape_string($_REQUEST['menuID']);
		$takeHome =  mysql_real_escape_string($_REQUEST['takeHome']);
		$orderDesc =  mysql_real_escape_string($_REQUEST['orderDesc']);
		addOrder($invoiceID, $units, $menuID, $takeHome, $orderDesc);
		break;
	case 'cancelOrder':
		$orderID =  mysql_real_escape_string($_REQUEST['orderID']);
		cancelOrder($orderID);
		break;
	case 'finishOrder':
		$orderID =  mysql_real_escape_string($_REQUEST['orderID']);
		finishOrder($orderID);
		break;
	case 'finishMassage':
		//$orderID =  mysql_real_escape_string($_REQUEST['orderID']);
		//finishMassage($orderID);
		break;
	case 'finishSauna':
		$orderID =  mysql_real_escape_string($_REQUEST['orderID']);
		finishSauna($orderID);
		break;
	case 'finishSnooker':
		$orderID =  mysql_real_escape_string($_REQUEST['orderID']);
		finishSnooker($orderID);
		break;
	case 'finishRestaurant':
		$orderID =  mysql_real_escape_string($_REQUEST['orderID']);
		finishRestaurant($orderID);
		break;
	
	// ADD NEW
	case 'addMoreZone':
		$invoiceID =  mysql_real_escape_string($_REQUEST['invoiceID']);
		addMoreZone($invoiceID);
		break;
	case 'finishMassageHours':
		$orderID		=  mysql_real_escape_string($_REQUEST['orderID']);
		$massage_type	=  mysql_real_escape_string($_REQUEST['massage_type']);
		$massager_id	=  mysql_real_escape_string($_REQUEST['massager_id']);
		$hours			=  mysql_real_escape_string($_REQUEST['hours']);
		$total			=  mysql_real_escape_string($_REQUEST['total']);		
		$coupon			=  mysql_real_escape_string($_REQUEST['coupon']);
		finishMassageHours($orderID, $massage_type, $massager_id, $hours, $total, $coupon);
		break;
	/*case 'deleteOrder':
		$orderID =  mysql_real_escape_string($_REQUEST['orderID']);
		cancelOrder($orderID);
		break;*/
	case 'deleteSauna':
		$orderID =  mysql_real_escape_string($_REQUEST['orderID']);
		deleteSauna($orderID);
		break;
	case 'deleteMassage':
		$orderID =  mysql_real_escape_string($_REQUEST['orderID']);
		deleteMassage($orderID);
		break;
}

// ADD NEW
function deleteSauna($orderID){

	$order_sql = "SELECT `order_inv`, `zone_id` FROM `order_sauna` WHERE `id`='".$orderID."'";
	$order_query = mysql_query($order_sql);	
	$results = mysql_fetch_assoc($order_query);
	resetZoneReserved($results['zone_id']);

	addLog('ลบรายการซาวน่า', $results['order_inv']);

	$order_sql = "DELETE FROM `order_sauna` WHERE `id`='".$orderID."'";
	$order_query = mysql_query($order_sql);	

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'invoice_id' => $results['order_inv']
	);
	returnJSON($json_arr);
}
function deleteMassage($orderID){

	$order_sql = "SELECT `order_inv`, `zone_id` FROM `order_massage` WHERE `id`='".$orderID."'";
	$order_query = mysql_query($order_sql);	
	$results = mysql_fetch_assoc($order_query);

	resetZoneReserved($results['zone_id']);

	addLog('ลบรายการนวด', $results['order_inv']);

	$order_sql = "DELETE FROM `order_massage` WHERE `id`='".$orderID."'";
	$order_query = mysql_query($order_sql);	

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'invoice_id' => $results['order_inv']
	);
	returnJSON($json_arr);
}
function addMoreZone($invoiceID){
	$html = '<p style="padding:50px 0 60px; text-align:center;">';
	$html .= '<a style="display:inline-block; padding:40px 20px;" href="?mod=restaurant&inv='.$invoiceID.'"><img src="images/mod_restaurant.png"></a>';
	$html .= '<a style="display:inline-block; padding:40px 20px;" href="?mod=snooker&inv='.$invoiceID.'"><img src="images/mod_snooker.png"></a>';
	$html .= '<a style="display:inline-block; padding:40px 20px;" href="?mod=massage&inv='.$invoiceID.'"><img src="images/mod_massage.png"></a>';
	$html .= '<a style="display:inline-block; padding:40px 20px;" href="?mod=sauna&inv='.$invoiceID.'"><img src="images/mod_sauna.png"></a>';
	$html .= '</p>';
	
	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
function cancelCancel($orderID){
	$order_sql = "SELECT * FROM `order` WHERE `id`='".$orderID."'";
	$order_query = mysql_query($order_sql);	
	$results = mysql_fetch_assoc($order_query);

	$serve = 0;
	if($results['order_status']>4){
		$serve = 1;
	}

	$cancel_sql = "INSERT INTO `order_cancel` (`order_ref`, `menu_id`, `unit`, `employee_id`, `serve`) VALUES ('".$orderID."','".$results['menu_id']."','".$results['unit']."','".accountDecrypt($_SESSION['user_id'])."', '".$serve."')";
	$cancel_query = mysql_query($cancel_sql);
}
?>
