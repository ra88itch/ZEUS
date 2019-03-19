<?php
defined('R88PROJ') or die ('SYSTEM ERROR');

$type	= $_REQUEST['type'];

function service_detail($invoiceID){
	$sql = "SELECT * FROM `invoice` WHERE `id`='".$invoiceID."'";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	if($results['zone']=='0'){
		return array($results['zone_extra'], $results['checkin']);
	}else{
		$getZoneDetail = getZoneDetail($results['zone']);
		return array($getZoneDetail[0], $results['checkin']);
	}
}
function getMemberDetail($member_id){
	$sql = "SELECT * FROM `customer` WHERE `id`='".$member_id."'";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return array($results['firstname'].' '.$results['lastname'].' ('.$member_id.')', '5');
}
function dateDiff($today,$expire){
	return (strtotime($today) - strtotime($expire))/  ( 60 * 60 * 24 );  // 1 day = 60*60*24
}
function chkMember($memberID){
	$sql = "SELECT * FROM `customer` WHERE `id`='".$memberID."' AND `active`='1' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	$num_rows = mysql_num_rows($query);
	if($num_rows == 1){
		$exp_date = date("Y-m-d", $results['expire']);

		$today = date("Y-m-d");
		$date_diff = dateDiff($today, $exp_date);
		
		//echo $date_diff.'<br>';

		if($date_diff < 0){
			$date_diff = 0;
		}else{
			$sql = "SELECT * FROM `customer_type` WHERE `id`='".$results['customer_type']."' LIMIT 1";
			$query = mysql_query($sql);	
			$results = mysql_fetch_assoc($query);
			$date_diff = $results['discount_percent'];
		}
	}else{
		$date_diff = 0;
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
	return array($results['menu_name_th'], $results['type_by_cooking']);
}
function getCustomerDetail($customer_id){
	$sql = "SELECT * FROM `customer` WHERE `id`='".$customer_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['firstname'].' '.$results['lastname'];
}
function getLockerDetail($customer_id){
	$sql = "SELECT * FROM `locker` WHERE `id`='".$customer_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['firstname'].' '.$results['lastname'];
}
function getCouponDetail2($ecoupon_id){
	$sql = "SELECT * FROM `ecoupon` WHERE `id`='".$ecoupon_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return '[ ecoupon ] '.$results['ecoupon_name'].' ['.$results['qty'].' ใบ]';
}
function getCouponDetail($coupon_id){
	$sql = "SELECT * FROM `coupon` WHERE `id`='".$coupon_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['coupon_name'];
}
//getDiscountDetail
function getDiscountDetail($discount_id){
	$sql = "SELECT * FROM `discount` WHERE `id`='".$discount_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['name'];
}
function getDiscountType($memberID){
	$getDiscountType = array();
	if($memberID=='0'){
		$sql = "SELECT * FROM `menu_type_cooking` WHERE `discount_employee`='1'";
	}elseif($memberID=='zeus30'){
		$sql = "SELECT * FROM `menu_type_cooking` WHERE `discount_member`='1'";
	}elseif($memberID>'0'){
		$sql = "SELECT * FROM `menu_type_cooking` WHERE `discount_member`='1'";
	}
	$query = mysql_query($sql);	
	while($results = mysql_fetch_assoc($query)) {
		array_push($getDiscountType, $results['id']);	
	}
	return $getDiscountType;
}
function chkBillDetails($invoiceID, $memberID, $print, $cash, $change, $invoiceBill){
	$getDiscountType = array();
	if($memberID=='0'){
		$sql = "SELECT * FROM `customer_type` WHERE `id`='".$memberID."' LIMIT 1";
		$query = mysql_query($sql);	
		$results = mysql_fetch_assoc($query);

		$chkMember = $results['discount_percent'];
		$memberID = '0';
	}elseif($memberID=='zeus30'){
		$chkMember = 30;
	}else{
		$chkMember = chkMember($memberID);		
	}
	if($chkMember>0){
		$getDiscountType = getDiscountType($memberID);
	}


	$details = array();
	
	$cash_total = 0;
	$cash_sql = "SELECT * FROM `order_cash` WHERE `order_inv` = '".$invoiceID."'";
	$cash_query = mysql_query($cash_sql);	
	while($results = mysql_fetch_assoc($cash_query)) {
		$cash_total = $cash_total+$results['price'];
		$cash_total = $cash_total+$results['charge'];
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
		$ecouponDetail = getCouponDetail2($results['coupon_id']);
		//$ecouponDetail = getECouponDetail($results['coupon_id']);
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

	$coupon_total = 0;
	$coupon_sql = "SELECT * FROM `order_coupon` WHERE `order_inv` = '".$invoiceID."'";
	$coupon_query = mysql_query($coupon_sql);	
	while($results = mysql_fetch_assoc($coupon_query)) {
		$coupon_total = $coupon_total+$results['total'];
		$couponDetail = getCouponDetail($results['coupon_id']);
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	$couponDetail,
				'unit'				=>	$results['unit'],
				'price'				=>	$results['price'],
				'total'				=>	$results['total'],
				'thisis'			=>	'ecoupon'
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

	$massage_total = 0;
	$massage_sql = "SELECT * FROM `order_massage` WHERE `order_inv` = '".$invoiceID."'";
	$massage_query = mysql_query($massage_sql);	
	while($results = mysql_fetch_assoc($massage_query)) {
		$massage_total = $massage_total+$results['total'];
		$zoneName = getZoneDetail($results['zone_id']);
		
		$employeeName = $results['employee_id'];

		if($results['end'] == '0000-00-00 00:00:00'){
			$employeeName = employeeName($results['employee_id']);
		}
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	$zoneName[0],
				'employee_name'		=>	$employeeName,
				'order_start'		=>	$results['start'],
				'order_end'			=>	$results['end'],
				'unit'				=>	'1',
				'price'				=>	'',
				'takehome'			=>	'0',			
				'times_min'			=>	$results['times_in_min'],
				'total'				=>	$results['total'],
				'thisis'			=>	'massage',
				'coupon'			=>	$results['coupon']
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
				'price'				=>	$results['price'],
				'takehome'			=>	'0',
				'times_min'			=>	$results['times_in_min'],
				'total'				=>	$results['total'],
				'thisis'			=>	'snooker',
				'coupon'			=>	$results['coupon']
			)
		);	
	}

	$sauna_total = 0;
	$sauna_sql = "SELECT * FROM `order_sauna` WHERE `order_inv` = '".$invoiceID."'";
	$sauna_query = mysql_query($sauna_sql);	
	while($results = mysql_fetch_assoc($sauna_query)) {
		$sauna_total = $sauna_total+$results['total'];
		if($results['zone_id'] > 0){
			$zoneName = getZoneDetail($results['zone_id']);
		}else{
			$zoneName = getMemberDetail($results['customer_id']);
		}
		/*if($results['order_status']=='5'){
			$results['total'] = $results['total'];
		}else{
			$results['total'] = 0;
		}*/
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
				'thisis'			=>	'sauna',
				'coupon'			=>	$results['coupon']
			)
		);	
	}

	$food_total = 0; //Discount
	$drink_total = 0; //Undiscount
	//$discountOrderType = discountOrderType();
	$order_sql = "SELECT * FROM `order` WHERE `order_inv` = '".$invoiceID."'";
	$order_query = mysql_query($order_sql);	
	while($results = mysql_fetch_assoc($order_query)) {
		$menuDetail = menuName($results['menu_id']);
		//$chk_order_type = chkOrderType();
		if($results['order_status']=='5'){
			$results['total'] = $results['total'];
		}else{
			$results['total'] = 0;
		}
		if(!in_array($menuDetail[1], $getDiscountType)){
			$drink_total = $drink_total+$results['total'];
		}else{
			$food_total = $food_total+$results['total'];
		}
		
		//$food_total = $food_total+$results['total'];
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	$menuDetail[0],
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

	$locker_total = 0;
	$locker_sql = "SELECT * FROM `order_locker` WHERE `order_inv` = '".$invoiceID."'";
	$locker_query = mysql_query($locker_sql);	
	while($results = mysql_fetch_assoc($locker_query)) {
		$locker_total = $locker_total+$results['total'];
		$customerName = getLockerDetail($results['customer_id']);
		if($results['renew'] == '1'){
			$customerName .= '(ต่ออายุ '.$results['unit'].' เดือน)';
		}
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	$customerName,
				'employee_name'		=>	employeeName($results['employee_id']),
				'order_start'		=>	$results['start'],
				'order_end'			=>	$results['end'],
				'unit'				=>	$results['unit'],
				'price'				=>	'',
				'takehome'			=>	'0',
				'total'				=>	$results['total'],
				'thisis'			=>	'locker'
			)
		);	
	}

	$discount_total = 0;
	$discount_sql = "SELECT * FROM `order_discount` WHERE `order_inv` = '".$invoiceID."'";
	$discount_query = mysql_query($discount_sql);	
	while($results = mysql_fetch_assoc($discount_query)) {
		$discount_total = $discount_total+$results['total'];
		$zoneName = getDiscountDetail($results['discount_id']);
		if($results['ecoupon_id'] > 0){
			$zoneName = getEcouponDetail($results['ecoupon_id']);
		}
		
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	$zoneName,
				'employee_name'		=>	employeeName($results['order_person']),
				'order_start'		=>	$results['start'],
				'unit'				=>	'1',
				'price'				=>	$results['price'],
				'total'				=>	$results['total'],
				'thisis'			=>	'discount'
			)
		);	
	}
	if(empty($details)) {
		$grand_total = 0;
		$service_detail = service_detail($invoiceID);
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'service_name' => $service_detail[0],
			'date' => $service_detail[1],
			'details' => $details,
			'total' => 0,
			'discount' => 0,
			'service_charge' => 0,
			'vat' => 0,
			'grand_total' => $grand_total,
			'invoice_id' => $invoiceID,
			'chkMember' => '0',
			'invoice_bill' => $invoiceBill,
			'pok' => $print,
			'entertainer' => getEntertainer()
		);
	} else {
		if($memberID=='0'){
			$sql = "SELECT * FROM `customer_type` WHERE `id`='".$memberID."' LIMIT 1";
			$query = mysql_query($sql);	
			$results = mysql_fetch_assoc($query);

			$chkMember = $results['discount_percent'];
			$memberID = '0';
		}elseif($memberID=='zeus30'){
			$chkMember = 30;
		}else if($memberID > '0'){
			$chkMember = chkMember($memberID);
		}

		if($chkMember != '0'){
			if($memberID=='zeus30'){
				$member_status = true;
			}elseif($memberID >= '0'){
				$member_status = true;
			}else{
				$member_status = false;
			}
			$total = $massage_total+$snooker_total+$sauna_total+$food_total+$member_total+$drink_total+$cash_total+$ecoupon_total+$coupon_total+$locker_total-$discount_total; // true
			$discount = floor($food_total*($chkMember/100)); // true
		}else{
			$member_status = false;
			$total = $massage_total+$snooker_total+$sauna_total+$food_total+$member_total+$drink_total+$cash_total+$ecoupon_total+$coupon_total+$locker_total-$discount_total;	// true
			$discount = 0; // true
	
		}
		if($total<0){
			$total = 0;
		}
		$grand_total = $total-$discount;
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
			'grand_total' => $grand_total,
			'invoice_id' => $invoiceID,
			'chkMember' => $chkMember,
			'invoice_bill' => $invoiceBill,
			'pok' => $print,
			'getDiscountType' => $getDiscountType,
			'entertainer' => getEntertainer()
		);
		if($cash > 0 && $cash!=''){
			$json_arr['receive'] = $cash;
			$json_arr['change'] = $change;
		}
	}
	returnJSON($json_arr);
}

function cash($invoiceID, $memberID, $grandTotal, $paymentMethod, $cash, $vat, $discount, $entertainer){
	$preCutECoupon = preCutECoupon($invoiceID);
	
	$preAddECoupon = preAddECoupon($invoiceID);
	$sql = "SELECT * FROM `invoice_bill` WHERE `inv_ref`='$invoiceID'";
	$query = mysql_query($sql);	
	$row = mysql_num_rows($query);
	$user_id = accountDecrypt($_SESSION['user_id']);
	if($row == 0 && $preCutECoupon == true){
		$curDate = date("Y-m-d");
		$curTime = date("H:i:s");
		if($curTime < '07:00:00'){
			$lastDate = date ("Y-m-d", strtotime("-1day", strtotime($curDate)));
			$curTime = '23:59:59';
			$dateTime = $lastDate.' '.$curTime;
			$sql = "INSERT INTO `invoice_bill` (`inv_ref`,`customer_id`,`grand_total`,`payment`,`received`, `vat`, `discount`, `employee_id`, `checkout`, `entertainer`) VALUES ('$invoiceID','$memberID','$grandTotal','$paymentMethod','$cash', '$vat', '$discount', '$user_id', '$dateTime', '$entertainer')";
		}else{
			$sql = "INSERT INTO `invoice_bill` (`inv_ref`,`customer_id`,`grand_total`,`payment`,`received`, `vat`, `discount`, `employee_id`, `entertainer`) VALUES ('$invoiceID','$memberID','$grandTotal','$paymentMethod','$cash', '$vat', '$discount', '$user_id', '$entertainer')";
		}
		preCutStore($invoiceID);
		$query = mysql_query($sql);
		$invoiceBill = mysql_insert_id();

		$sql = "UPDATE `invoice` SET `checkout`=NOW() WHERE `id`='$invoiceID'";
		$query = mysql_query($sql);	

		$change = $cash - $grandTotal;
		
		chkBillDetails($invoiceID, $memberID, true, $cash, $change, $invoiceBill);
	}else{
		//$sql = "UPDATE `invoice_bill` SET `customer_id`='$memberID', `grand_total`='$grandTotal', `payment`='$paymentMethod', `received`='$cash', `vat`='$vat', `discount`='$discount', `employee_id`='$user_id' WHERE `inv_ref`='$invoiceID'";	
		//$query = mysql_query($sql);
	}
}
function preCutStore($invoiceID){
	$sql = "SELECT SUM(`unit`) AS `unit`, `menu_id` FROM `order` WHERE `order_inv`='$invoiceID' AND `order_status`='5' GROUP BY `menu_id`";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){		
		if($result['unit']>0){
			$getStoreID = getStoreID($result['menu_id']);
			if($getStoreID > 0){
				cutStore($getStoreID, $result['unit']);
			}
		}
	}
}
function getStoreID($menu_id){
	$sql = "SELECT `stock_id` FROM `menu` WHERE `id`='$menu_id'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	return $result['stock_id'];
}
function cutStore($storeID, $unit){
	$user_id = accountDecrypt($_SESSION['user_id']);
	$currentStore = currentStore($storeID);
	$newStore = $currentStore-$unit;
	$sql = "INSERT INTO `stock_detail` (`stock_id`, `out`, `employee_position`, `total`) VALUES ('$storeID', '$unit', '$user_id', '$newStore')";
	$query = mysql_query($sql);

	$sql = "UPDATE `stock` SET `amount`='$newStore' WHERE `id`='$storeID'";
	$query = mysql_query($sql);
	return true;
}
function currentStore($storeID){
	$sql = "SELECT `amount` FROM `stock` WHERE `id`='$storeID'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	return $result['amount'];
}
function chkBillDetailsPrint($invoiceID, $memberID, $print, $cash, $change, $invoiceBill){
	$getDiscountType = array();
	if($memberID=='0'){
		$sql = "SELECT * FROM `customer_type` WHERE `id`='".$memberID."' LIMIT 1";
		$query = mysql_query($sql);	
		$results = mysql_fetch_assoc($query);

		$chkMember = $results['discount_percent'];
		$memberID = '0';
	}elseif($memberID=='zeus30'){
		$chkMember = 30;
	}else{
		$chkMember = chkMember($memberID);		
	}
	if($chkMember>0){
		$getDiscountType = getDiscountType($memberID);
	}

	$details = array();

	$cash_total = 0;
	$cash_sql = "SELECT * FROM `order_cash` WHERE `order_inv` = '".$invoiceID."'";
	$cash_query = mysql_query($cash_sql);	
	while($results = mysql_fetch_assoc($cash_query)) {
		$cash_total = $cash_total+$results['price'];
		$cash_total = $cash_total+$results['charge'];
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
		$ecouponDetail = getCouponDetail2($results['coupon_id']);
		//$ecouponDetail = getECouponDetail($results['coupon_id']);
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

	$coupon_total = 0;
	$coupon_sql = "SELECT * FROM `order_coupon` WHERE `order_inv` = '".$invoiceID."'";
	$coupon_query = mysql_query($coupon_sql);	
	while($results = mysql_fetch_assoc($coupon_query)) {
		$coupon_total = $coupon_total+$results['total'];
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
	$drink_total = 0;
	$order_sql = "SELECT `menu_id`, `id`, `start`, `end`, SUM(`unit`) AS `units`, `price`, `order_status`, SUM(`total`) AS `totals` FROM `order` WHERE `order_inv` = '".$invoiceID."' AND `order_status`='5' GROUP BY `menu_id`";
	$order_query = mysql_query($order_sql);	
	while($results = mysql_fetch_assoc($order_query)) {
		$menuDetail = menuName($results['menu_id']);
		
		if(!in_array($menuDetail[1], $getDiscountType)){
			$drink_total = $drink_total+$results['totals'];
		}else{
			$food_total = $food_total+$results['totals'];
		}
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	$menuDetail[0],
				'employee_name'		=>	employeeName($results['employee_id']),
				'order_start'		=>	$results['start'],
				'order_end'			=>	$results['end'],
				'unit'				=>	$results['units'],
				'price'				=>	$results['price'],
				'status'			=>	orderStatusName($results['order_status']),
				'status_id'			=>	$results['order_status'],
				'takehome'			=>	$results['takehome'],
				'total'				=>	$results['totals'],
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

	$locker_total = 0;
	$locker_sql = "SELECT * FROM `order_locker` WHERE `order_inv` = '".$invoiceID."'";
	$locker_query = mysql_query($locker_sql);	
	while($results = mysql_fetch_assoc($locker_query)) {
		$locker_total = $locker_total+$results['total'];
		$customerName = getLockerDetail($results['customer_id']);
		if($results['renew'] == '1'){
			$customerName .= '(ต่ออายุ '.$results['unit'].' เดือน)';
		}
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	$customerName,
				'employee_name'		=>	employeeName($results['employee_id']),
				'order_start'		=>	$results['start'],
				'order_end'			=>	$results['end'],
				'unit'				=>	$results['unit'],
				'price'				=>	'',
				'takehome'			=>	'0',
				'total'				=>	$results['total'],
				'thisis'			=>	'locker'
			)
		);	
	}

	$discount_total = 0;
	$discount_sql = "SELECT * FROM `order_discount` WHERE `order_inv` = '".$invoiceID."'";
	$discount_query = mysql_query($discount_sql);	
	while($results = mysql_fetch_assoc($discount_query)) {
		$discount_total = $discount_total+$results['total'];
		$zoneName = getDiscountDetail($results['discount_id']);
		if($results['ecoupon_id'] > 0){
			$zoneName = getEcouponDetail($results['ecoupon_id']);
		}
		
		array_push($details,array(
				'id' 				=>	$results['id'],
				'order_name'		=>	$zoneName,
				'employee_name'		=>	employeeName($results['order_person']),
				'order_start'		=>	$results['start'],
				'unit'				=>	'1',
				'price'				=>	$results['price'],
				'total'				=>	$results['total'],
				'thisis'			=>	'discount'
			)
		);	
	}
	if(empty($details)) {
		$grand_total = 0;
		$service_detail = service_detail($invoiceID);
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'service_name' => $service_detail[0],
			'date' => $service_detail[1],
			'details' => $details,
			'total' => 0,
			'discount' => 0,
			'service_charge' => 0,
			'vat' => 0,
			'grand_total' => $grand_total,
			'invoice_id' => $invoiceID,
			'chkMember' => '0',
			'invoice_bill' => $invoiceBill,
			'pok' => $print
		);
	} else {

		if($memberID=='0'){
			$sql = "SELECT * FROM `customer_type` WHERE `id`='".$memberID."' LIMIT 1";
			$query = mysql_query($sql);	
			$results = mysql_fetch_assoc($query);

			$chkMember = $results['discount_percent'];
			$memberID = '0';
		}elseif($memberID=='zeus30'){
			$chkMember = 30;
		}else if($memberID > '0'){
			$chkMember = chkMember($memberID);
		}

		if($chkMember != '0'){
			if($memberID=='zeus30'){
				$member_status = true;
			}elseif($memberID >= '0'){
				$member_status = true;
			}else{
				$member_status = false;
			}
			$total = $massage_total+$snooker_total+$sauna_total+$food_total+$member_total+$drink_total+$cash_total+$ecoupon_total+$coupon_total+$locker_total-$discount_total; // true
			$discount = floor($food_total*($chkMember/100)); // true
		}else{
			$member_status = false;
			$total = $massage_total+$snooker_total+$sauna_total+$food_total+$member_total+$drink_total+$cash_total+$ecoupon_total+$coupon_total+$locker_total-$discount_total;	// true
			$discount = 0; // true
	
		}
		if($total<0){
			$total=0;
		}
		$grand_total = $total-$discount;
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
			'massage_total' => $massage_total,
			'snooker_total' => $snooker_total,
			'food_total' => $food_total,
			'drink_total' => $drink_total,
			'cash_total' => $cash_total,
			'member_total' => $member_total,
			'coupon_total' => $coupon_total,
			'discount' => $discount,
			'service_charge' => 0,
			'vat' => 0,
			'grand_total' => $grand_total,
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
function freeSnooker($orderID){
	$sql = "SELECT * FROM `order_snooker` WHERE `id`='$orderID'";
	$query = mysql_query($sql);	
	$result = mysql_fetch_assoc($query);

	$newTotal = $result['total']-$result['price'];
	if($newTotal < 0){
		$newTotal = '0';
	}
	$sql = "UPDATE `order_snooker` SET `coupon`='1', `total`='$newTotal' WHERE `id`='$orderID'";
	$query = mysql_query($sql);	

	chkBillDetails($result['order_inv'], '', false, 0, 0, 0);
}
function freeSauna($orderID){
	$sql = "SELECT * FROM `order_sauna` WHERE `id`='$orderID'";
	$query = mysql_query($sql);	
	$result = mysql_fetch_assoc($query);

	$discount = $result['total']-200;

	$sql = "UPDATE `order_sauna` SET `coupon`='1', `total`='$discount' WHERE `id`='$orderID'";
	$query = mysql_query($sql);	

	/*$sql = "SELECT * FROM `order_sauna` WHERE `id`='$orderID'";
	$query = mysql_query($sql);	
	$result = mysql_fetch_assoc($query);*/	

	chkBillDetails($result['order_inv'], '', false, 0, 0, 0);
}
function getEntertainer(){
	$entertainers = array();

	$sql = "SELECT * FROM `entertainer`";
	$query = mysql_query($sql);	
	while($results = mysql_fetch_assoc($query)){
		array_push($entertainers, array(
				'id' 					=>	$results['id'],
				'entertainer_name'		=>	$results['entertainer_name']
			)
		);
	}
	return $entertainers;
	/*global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'entertainers' => $entertainers
	);
	returnJSON($json_arr);*/
}
	
chkSession();
switch($type){
	case 'chkBillDetails':
		$invoiceID =  mysql_real_escape_string($_POST['invoiceID']);
		$memberID =  mysql_real_escape_string($_POST['memberID']);
		chkBillDetails($invoiceID, $memberID, false, 0, 0, 0);
		break;
	case 'chkBillDetailsPrint':
		$invoiceID =  mysql_real_escape_string($_POST['invoiceID']);
		$memberID =  mysql_real_escape_string($_POST['memberID']);
		chkBillDetailsPrint($invoiceID, $memberID, false, 0, 0, 0);
		break;
	case 'cash':
		$invoiceID		=  mysql_real_escape_string($_POST['invoiceID']);
		$memberID		=  mysql_real_escape_string($_POST['memberID']);
		$grandTotal		=  mysql_real_escape_string($_POST['grandTotal']);
		$paymentMethod	=  mysql_real_escape_string($_POST['paymentMethod']);
		$cash			=  mysql_real_escape_string($_POST['cash']);
		$vat			=  mysql_real_escape_string($_POST['vat']);
		$discount		=  mysql_real_escape_string($_POST['discount']);
		$entertainer	=  mysql_real_escape_string($_POST['entertainerID']);
		
		cash($invoiceID, $memberID, $grandTotal, $paymentMethod, $cash, $vat, $discount, $entertainer);
		break;
	case 'freeSnooker':
		$orderID		=  mysql_real_escape_string($_POST['orderID']);
		freeSnooker($orderID);
		break;
	case 'freeSauna':
		$orderID		=  mysql_real_escape_string($_POST['orderID']);
		freeSauna($orderID);
		break;
	case 'getEntertainer':
		getEntertainer();
		break;
}

function getEcouponDetail($ticket_id){
	$sql	= "SELECT *, `ecoupon_customer_ticket`.`id` AS `ticket_id`, `ecoupon_customer_ticket`.`qty` AS `qty_balance` FROM `ecoupon_customer_ticket` LEFT JOIN `ecoupon` ON `ecoupon_customer_ticket`.`ecoupon_id`=`ecoupon`.`id` WHERE `ecoupon_customer_ticket`.`id`='$ticket_id' ORDER BY `ecoupon_id`";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return '[eCoupon] - '.$results['ecoupon_name'];
}

function preCutECoupon($invoiceID){
	$sql = "SELECT SUM(`unit`) AS `unit`, `ecoupon_id` FROM `order_discount` WHERE `order_inv`='$invoiceID' AND `discount_id`='0' GROUP BY `ecoupon_id`";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){		
		if($result['unit']>0){
			$cutECoupon = cutECoupon($result['ecoupon_id'], $result['unit']);
			if($cutECoupon == false){
				return false;
			}
		}
	}
	return true;
}
function cutECoupon($ecoupon_id, $unit){	
	$currentStore = currentECoupon($ecoupon_id);
	$newStore = $currentStore['qty']-$unit;
	if($newStore >= 0){
		$sql = "UPDATE `ecoupon_customer_ticket` SET `qty`='$newStore' WHERE `id`='$ecoupon_id'";
		$query = mysql_query($sql);

		sendSMS($unit, $currentStore);
		return true;	
	}else{
		return false;	
	}
}
function currentECoupon($ecoupon_id){	
	$sql = "SELECT `qty`, `ecoupon_id`, `customer_id` FROM `ecoupon_customer_ticket` WHERE `id`='$ecoupon_id'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	return array('qty'=>$result['qty'], 'ecoupon_id'=>$result['ecoupon_id'], 'customer_id'=>$result['customer_id']);
}

function sendSMS($unit, $ecoupon_ticket){

	//var_dump($ecoupon_ticket);
	$ecoupon = getEcoupon($ecoupon_ticket['ecoupon_id']);
	$customer = getEcouponCustomer($ecoupon_ticket['customer_id']);
	//var_dump($ecoupon);
	//var_dump($customer);

	if($customer['enable_sms'] == '0'){
		return false;
	}

	///echo $customer['enable_sms'];

	$params['method']   = 'send';
	$params['username'] = 'ra88itch';
	$params['password'] = '75465173';
	
	$from = 'ZeusMessage';
	if($ecoupon['discount_zone_category'] == '10'){
		$from = 'ZeusSauna';
	}

	$message = 'คุณใช้ eCoupon';
	$message .= $ecoupon['ecoupon_name'];
	$message .= 'จำนวน ';
	$message .= $unit;
	$message .= ' สิทธิ์';
	$message .= '/คงเหลือ ';
	$message .= $ecoupon_ticket['qty'] - $unit;
	$message .= ' ขอบคุณที่ใช้บริการค่ะ';

	$params['from']     = $from;
	$params['to']       = $customer['mobile'];
	$params['message']  = $message;

	//var_dump($params);

	if (is_null( $params['to']) || is_null( $params['message']))
	{
		return FALSE;
	}
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://www.thsms.com/api/rest');
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( $params));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$response  = curl_exec($ch);
	$lastError = curl_error($ch);
	$lastReq = curl_getinfo($ch);
	curl_close($ch);

	

	//$result = $this->curl( $params);
	$xml = @simplexml_load_string( $response);
	if (!is_object($xml))
	{
		//var_dump( array( FALSE, 'Respond error'));
	} else {
		if ($xml->send->status == 'success')
		{
			//var_dump( array( TRUE, $xml->send->uuid));
		} else {
			//var_dump( array( FALSE, $xml->send->message));
		}
	}
}

function getEcoupon($ecoupon_id){
	$sql = "SELECT * FROM `ecoupon` WHERE `id`='$ecoupon_id'";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){	
		return $result;
	}
	
}

function getEcouponCustomer($customer_id){
	$sql = "SELECT * FROM `ecoupon_customer` WHERE `id`='$customer_id'";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){	
		return $result;
	}
}







function preAddECoupon($invoiceID){
	$sql = "SELECT * FROM `order_ecoupon` WHERE `order_inv`='$invoiceID'";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){	
		autoAddEcoupon($result['coupon_id'], $result['customer_id']);
	}
	return true;
}
function autoAddEcoupon($ecoupon_id, $customer_id){	
	$qty = getecouponQty($ecoupon_id);

	$sql = "SELECT * FROM `ecoupon_customer_ticket` WHERE `customer_id`='".$customer_id."' AND `ecoupon_id`='".$ecoupon_id."' LIMIT 1";
	$query = mysql_query($sql);
	$rows = mysql_num_rows($query);
	if($rows == 0){
		$sql_update = "INSERT INTO `ecoupon_customer_ticket` (`customer_id`, `ecoupon_id`, `qty`) VALUES ('".$customer_id."', '".$ecoupon_id."', '".$qty."')";	
		//$sql = "UPDATE `invoice` SET `checkout`=NOW() WHERE `id`='$invoiceID'";
	}else{
		$sql_update = "UPDATE `ecoupon_customer_ticket` SET `qty`=`qty`+$qty WHERE `customer_id`='".$customer_id."' AND `ecoupon_id`='".$ecoupon_id."'";
	}
	//echo $sql_update;
	mysql_query($sql_update);

}
function getecouponQty($ecoupon_id){	
	$sql = "SELECT `qty` FROM `ecoupon` WHERE `id`='$ecoupon_id'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	return $result['qty'];
}
?>
