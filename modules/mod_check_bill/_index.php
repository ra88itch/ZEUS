<?php
defined('R88PROJ') or die ('SYSTEM ERROR');

$type	= $_REQUEST['type'];

function service_detail($invoiceID){
	$sql = "SELECT * FROM `invoice` WHERE `id`='".$invoiceID."'";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	if($results['zone']=='0'){
		return array('ลูกค้าเงินสด '.$invoiceID, $results['checkin']);
	}else{
		$getZoneDetail = getZoneDetail($results['zone']);
		return array($getZoneDetail[0], $results['checkin']);
	}
}
function dateDiff($strDate1,$strDate2){
	return (strtotime($strDate2) - strtotime($strDate1))/  ( 60 * 60 * 24 );  // 1 day = 60*60*24
}
function chkMember($memberID){
	$sql = "SELECT * FROM `customer` WHERE `id`='".$memberID."' AND `active`='1' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	if($results['customer_type']=='1'){
		$exp_date = date ("Y-m-d", strtotime("+1 month", strtotime($results['register'])));
	}else{
		$exp_date = date ("Y-m-d", strtotime("+1 year", strtotime($results['register'])));
	}

	$today = date("Y-m-d");
	$date_diff = dateDiff($today, $exp_date);
	
	if($date_diff < 0){
		$date_diff = 0;
	}else{
		if($results['customer_type']=='1'){
			$date_diff = '5';
		}elseif($results['customer_type']=='2'){
			$date_diff = '10';
		}
	}
	return $date_diff;
}
function getZoneDetail($zone_id){
	$sql = "SELECT * FROM `zone` WHERE `id`='".$zone_id."'";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return array($results['zone'], $results['zone_category']);
	//return $sql;
}
function employeeName($employee_id){
	$sql = "SELECT * FROM `employee` WHERE `id`='".$employee_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['nickname'];
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
function chkBillDetails($invoiceID, $memberID, $print, $cash, $change, $invoiceBill){
	$details = array();

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

	$massage_total = 0;
	$massage_sql = "SELECT * FROM `order_massage` WHERE `order_inv` = '".$invoiceID."'";
	$massage_query = mysql_query($massage_sql);	
	while($results = mysql_fetch_assoc($massage_query)) {
		$massage_total = $massage_total+$results['total'];
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

	$snooker_total = 0;
	$snooker_sql = "SELECT * FROM `order_snooker` WHERE `order_inv` = '".$invoiceID."'";
	$snooker_query = mysql_query($snooker_sql);	
	while($results = mysql_fetch_assoc($snooker_query)) {
		$snooker_total = $snooker_total+$results['total'];
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

	$sauna_total = 0;
	$sauna_sql = "SELECT * FROM `order_sauna` WHERE `order_inv` = '".$invoiceID."'";
	$sauna_query = mysql_query($sauna_sql);	
	while($results = mysql_fetch_assoc($sauna_query)) {
		$sauna_total = $sauna_total+$results['total'];
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
				'thisis'			=>	'sauna'
			)
		);	
	}

	$food_total = 0;
	$order_sql = "SELECT * FROM `order` WHERE `order_inv` = '".$invoiceID."'";
	$order_query = mysql_query($order_sql);	
	while($results = mysql_fetch_assoc($order_query)) {
		$food_total = $food_total+$results['total'];
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	menuName($results['menu_id']),
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

	$member_total = 0;
	$member_sql = "SELECT * FROM `order_member` WHERE `order_inv` = '".$invoiceID."'";
	$member_query = mysql_query($member_sql);	
	while($results = mysql_fetch_assoc($member_query)) {
		$member_total = $member_total+$results['total'];
		$customerName = getCustomerDetail($results['customer_id']);
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	$customerName,
				'employee_name'		=>	employeeName($results['employee_id']),
				'order_start'		=>	$results['start'],
				'order_end'			=>	$results['end'],
				'unit'				=>	'1',
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
		$chkMember = chkMember($memberID);
		if($chkMember != '0'){
			$member_status = true;
			$total = $massage_total+$snooker_total+$sauna_total+$food_total+$member_total; // true
			$discount = $food_total*($chkMember/100); // true
			$total_discount = $total-$discount; 
			//$service_charge = $total_discount*0.1;
			//$service_charge = ($food_total-$discount)*0.1; // true
			//$total_service_charge = $total_discount+$service_charge;


			//$vat = $total_service_charge*0.07;
			//$grand_total = $total_service_charge+$vat;
		}else{
			$member_status = false;
			$total = $massage_total+$snooker_total+$sauna_total+$food_total+$member_total;	// true
			$discount = 0; // true
			//$service_charge = $total*0.1;
			//$service_charge = ($food_total-$discount)*0.1; // true
			//$total_service_charge = $total+$service_charge;


			//$vat = $total_service_charge*0.07;
			//$grand_total = $total_service_charge+$vat;
		}
		$service_detail = service_detail($invoiceID);
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'service_name' => $service_detail[0],
			'date' => $service_detail[1],
			'details' => $details,
			'member_id' => $memberID,
			'member_status' => $member_status,
			'total' => $total,
			'discount' => $discount,
			'service_charge' => 0,
			'vat' => 0,
			'grand_total' => $total,
			'invoice_id' => $invoiceID,
			'chkMember' => $chkMember,
			'invoice_bill' => $invoiceBill,
			'pok' => $print
		);
		if($cash > 0 && $cash!=''){
			$json_arr['receive'] = $cash;
			$json_arr['change'] = $change;
		}
	}
	returnJSON($json_arr);
}

function cash($invoiceID, $memberID, $grandTotal, $paymentMethod, $cash, $vat, $discount){
	$sql = "SELECT * FROM `invoice_bill` WHERE `inv_ref`='$invoiceID'";
	$query = mysql_query($sql);	
	$row = mysql_num_rows($query);
	$user_id = accountDecrypt($_SESSION['user_id']);
	if($row == 0){
		$sql = "INSERT INTO `invoice_bill` (`inv_ref`,`customer_id`,`grand_total`,`payment`,`received`, `vat`, `discount`, `employee_id`) VALUES ('$invoiceID','$memberID','$grandTotal','$paymentMethod','$cash', '$vat', '$discount', '$user_id')";
		$query = mysql_query($sql);
		$invoiceBill = mysql_insert_id();
	}else{
		$result = mysql_fetch_accoc($query);
		$invoiceBill = $result['id'];

		$sql = "UPDATE `invoice_bill` SET `customer_id`='$memberID', `grand_total`='$grandTotal', `payment`='$paymentMethod', `received`='$cash', `vat`='$vat', `discount`='$discount', `employee_id`='$user_id' WHERE `inv_ref`='$invoiceID'";	
		$query = mysql_query($sql);
	}
	

	$sql = "UPDATE `invoice` SET `checkout`=NOW() WHERE `id`='$invoiceID'";
	$query = mysql_query($sql);	

	$change = $cash - $grandTotal;
	
	chkBillDetails($invoiceID, $memberID, true, $cash, $change, $invoiceBill);

}

chkSession();
switch($type){
	case 'chkBillDetails':
		$invoiceID =  mysql_real_escape_string($_POST['invoiceID']);
		$memberID =  mysql_real_escape_string($_POST['memberID']);
		chkBillDetails($invoiceID, $memberID, false, 0, 0, 0);
		break;
	case 'cash':
		$invoiceID		=  mysql_real_escape_string($_POST['invoiceID']);
		$memberID		=  mysql_real_escape_string($_POST['memberID']);
		$grandTotal		=  mysql_real_escape_string($_POST['grandTotal']);
		$paymentMethod	=  mysql_real_escape_string($_POST['paymentMethod']);
		$cash			=  mysql_real_escape_string($_POST['cash']);
		$vat			=  mysql_real_escape_string($_POST['vat']);
		$discount		=  mysql_real_escape_string($_POST['discount']);
		cash($invoiceID, $memberID, $grandTotal, $paymentMethod, $cash, $vat, $discount);
		break;
}

?>
