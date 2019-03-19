<?php
defined('R88PROJ') or die ('SYSTEM ERROR');

$type	= $_REQUEST['type'];
function invZone($invoice, $zone){
	$zone_cate_sql = "UPDATE `invoice` SET `zone`='".$zone."' WHERE `id`='".$invoice."'";
	$zone_cate_query = mysql_query($zone_cate_sql);
	return $invoice;
}
function createInvoice($zone_key, $zone_value, $customer_value){
	$sql = "INSERT INTO `invoice` (`zone`,`zone_extra`,`customer_value`) VALUES ('".$zone_key."','".$zone_value."','".$customer_value."')";
	$query = mysql_query($sql);
	return mysql_insert_id();
}
function createMemberInvoice($member_id){
	$sql = "INSERT INTO `invoice` (`member_id`,`customer_value`) VALUES ('".$member_id."','1')";
	$query = mysql_query($sql);
	return mysql_insert_id();
}
function chkZonePrice($zone_id){
	$zone_cate_sql = "SELECT `zone_category` FROM `zone` WHERE `id`='".$zone_id."'";
	$zone_cate_query = mysql_query($zone_cate_sql);
	$cate_result = mysql_fetch_assoc($zone_cate_query);
	
	$zone_charge_sql = "SELECT `charge` FROM `zone_category` WHERE `id`='".$cate_result['zone_category']."'";
	$zone_charge_query = mysql_query($zone_charge_sql);
	$charge_result = mysql_fetch_assoc($zone_charge_query);
	return $charge_result['charge'];
}
function bookingRestaurant($zone_value, $customer_value, $invoice_id){
	$zone_value = str_replace('zone', '', $zone_value);
	$zone_value = substr($zone_value, 0, -1);
	$zone_arr = explode(',', $zone_value);
	$zone_key = $zone_arr[0];
	
	$sql = "SELECT COUNT(*) FROM `zone` WHERE `reserved`='0' AND `active`='1' AND `id` IN (".$zone_value.")";
	$query = mysql_query($sql);
	$count_result = mysql_fetch_assoc($query);

	$count = count($zone_arr);
	if($count_result['COUNT(*)'] != $count){
		global $system_status_failed;
		$json_arr = array(
			'process' => $system_status_failed
		);
	} else {
		//	$createInvoice = createInvoice($zone_key, $zone_value, $customer_value);
		if($invoice_id=='0' || $invoice_id==''){
			$createInvoice = createInvoice($zone_key, $zone_value, $customer_value);
		}else{
			$createInvoice = invZone($invoice_id, $zone_key);
		   //$createInvoice = $invoice_id;
		}

		for($i=0; $i<$count; $i++){
			$charge = chkZonePrice($zone_arr[$i]);

			$sql = "UPDATE `zone` SET `reserved`='1' WHERE `id`='".$zone_arr[$i]."'";
			$query = mysql_query($sql);

			$curDate = date("Y-m-d");
			$curTime = date("H:i:s");
			if($curTime < '07:00:00'){
				$lastDate = date ("Y-m-d", strtotime("-1day", strtotime($curDate)));
				$curTime = '23:59:59';
				$dateTime = $lastDate.' '.$curTime;
				$sql = "INSERT INTO `order_restaurant` (`order_inv`,`zone_id`,`price`, `order_person`, `start`) VALUES ('".$createInvoice."', '".$zone_arr[$i]."', '".$charge."', '".accountDecrypt($_SESSION['user_id'])."', '$dateTime')";
			}else{
				$dateTime = $curDate.' '.$curTime;
				$sql = "INSERT INTO `order_restaurant` (`order_inv`,`zone_id`,`price`, `order_person`) VALUES ('".$createInvoice."', '".$zone_arr[$i]."', '".$charge."', '".accountDecrypt($_SESSION['user_id'])."')";
			}

			//$sql = "INSERT INTO `order_restaurant` (`order_inv`,`zone_id`,`price`, `order_person`) VALUES ('".$createInvoice."', '".$zone_arr[$i]."', '".$charge."', '".accountDecrypt($_SESSION['user_id'])."')";
			$query = mysql_query($sql);
		}

		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'invoice_no' => $createInvoice
		);
	}
	returnJSON($json_arr);
}
function bookingSnooker($zone_value, $customer_value, $invoice_id){
	$zone_value = str_replace('zone', '', $zone_value);
	$zone_value = substr($zone_value, 0, -1);
	$zone_arr = explode(',', $zone_value);
	$zone_key = $zone_arr[0];
	
	$sql = "SELECT COUNT(*) FROM `zone` WHERE `reserved`='0' AND `active`='1' AND `id` IN (".$zone_value.")";
	$query = mysql_query($sql);
	$count_result = mysql_fetch_assoc($query);

	$count = count($zone_arr);
	if($count_result['COUNT(*)'] != $count){
		global $system_status_failed;
		$json_arr = array(
			'process' => $system_status_failed
		);
	} else {	
		include('LXTelnet.php');		

		//	$createInvoice = createInvoice($zone_key, $zone_value, $customer_value);
		if($invoice_id=='0' || $invoice_id==''){
			$createInvoice = createInvoice($zone_key, $zone_value, $customer_value);
		}else{
			$createInvoice = invZone($invoice_id, $zone_key);
			//$createInvoice = $invoice_id;
		}

		$lx = new LXTelnet();
		if($lx->init() === false){ return; }
		if($lx->login() === false){ return; }
		
		for($i=0; $i<$count; $i++){
			$cmd = getCmdByZone($zone_arr[$i]);
			if($cmd != false){
				$resp = $lx->control($cmd);
			}else{
				$resp = true;
			}
			//$lx->control($cmd);
			if($resp!=false){
				$charge = chkZonePrice($zone_arr[$i]);

				$sql = "UPDATE `zone` SET `reserved`='1' WHERE `id`='".$zone_arr[$i]."'";
				$query = mysql_query($sql);
				
				/*$curDate = date("Y-m-d");
				$curTime = date("H:i:s");
				if($curTime < '07:00:00'){
					$lastDate = date ("Y-m-d", strtotime("-1day", strtotime($curDate)));
					$curTime = '23:59:59';
					$dateTime = $lastDate.' '.$curTime;
					$sql = "INSERT INTO `order_snooker` (`order_inv`,`zone_id`,`price`, `order_person`, `start`) VALUES ('".$createInvoice."', '".$zone_arr[$i]."', '".$charge."', '".accountDecrypt($_SESSION['user_id'])."', '$dateTime')";
				}else{
					$dateTime = $curDate.' '.$curTime;
					$sql = "INSERT INTO `order_snooker` (`order_inv`,`zone_id`,`price`, `order_person`) VALUES ('".$createInvoice."', '".$zone_arr[$i]."', '".$charge."', '".accountDecrypt($_SESSION['user_id'])."')";
				}*/
				$sql = "INSERT INTO `order_snooker` (`order_inv`,`zone_id`,`price`, `order_person`) VALUES ('".$createInvoice."', '".$zone_arr[$i]."', '".$charge."', '".accountDecrypt($_SESSION['user_id'])."')";
				$query = mysql_query($sql);

				addLog('เปิดใช้บริการโต๊ะสนุ๊ก', $createInvoice);
			}	
		}
		
		$lx->close();

		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success
		);
	}
	returnJSON($json_arr);
}
function getCmdByZone($zone){
	$cmd_opens	= array('$00W01N#1F', '$00W02N#1C', '$00W03N#1D', '$00W04N#1A', '$00W05N#1B', '$00W06N#18', '$00W07N#19', '$00W08N#16', '$00W09N#17', '$00W10N#1F', '$00W11N#1E', '$00W12N#1D', '$00W13N#1C', '$00W14N#1B', '$00W15N#1A');

	$sql = "SELECT `extra` FROM `zone` WHERE `id`='$zone'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	if($result['extra'] == ''){
		return false;
	}
	$extra = $result['extra']-1;
	return $cmd_opens[$extra];
//$cmd_opens	= ['$00W01N#1F', '$00W02N#1C', '$00W03N#1D', '$00W04N#1A', '$00W05N#1B', '$00W06N#18', '$00W07N#19', '$00W08N#16', '$00W09N#17', '$00W10N#1F', '$00W11N#1E', '$00W12N#1D', '$00W13N#1C', '$00W14N#1B', '$00W15N#1A'];

	/*switch($zone){
	case '39':
		$cmd = '$00W01N#1F';
		break;
	case '40':
		$cmd = '$00W02N#1C';
		break;
	case '41':
		$cmd = '$00W03N#1D';
		break;
	case '42':
		$cmd = '$00W04N#1A';
		break;
	case '43':
		$cmd = '$00W05N#1B';
		break;
	case '44':
		$cmd = '$00W06N#18';
		break;
	case '45':
		$cmd = '$00W07N#19';
		break;
	case '46':
		$cmd = '$00W08N#16';
		break;
	case '47':
		$cmd = '$00W11N#1E';
		break;
	case '48':
		$cmd = '$00W12N#1D';
		break;
	case '49':
		$cmd = '$00W13N#1C';
		break;
	case '50':
		$cmd = '$00W14N#1B';
		break;
	case '51':
		$cmd = '$00W15N#1A';
		break;
	}
	return $cmd;*/
}
function bookingMassage($zone_value, $customer_value, $massenger_value, $invoice_id){
	$zone_value = str_replace('zone', '', $zone_value);
	$zone_value = substr($zone_value, 0, -1);
	$zone_arr = explode(',', $zone_value);
	$zone_key = $zone_arr[0];

	$massenger_value = str_replace('mass', '', $massenger_value);
	$massenger_value = substr($massenger_value, 0, -1);
	$massenger_arr = explode(',', $massenger_value);
	$massenger_key = $massenger_arr[0];
	
	$sql = "SELECT COUNT(*) FROM `employee` WHERE `reserved`='0' AND `active`='1' AND `id` IN (".$massenger_value.")";
	$query = mysql_query($sql);
	$count_result = mysql_fetch_assoc($query);

	$count = count($massenger_arr);
	if($count_result['COUNT(*)'] != $count){
		global $system_status_failed;
		$json_arr = array(
			'process' => $system_status_failed
		);
	} else {
		//	$createInvoice = createInvoice($zone_key, $zone_value, $customer_value);
		if($invoice_id=='0' || $invoice_id==''){
			$createInvoice = createInvoice($zone_key, $zone_value, $customer_value);
		}else{
			$createInvoice = invZone($invoice_id, $zone_key);
			//$createInvoice = $invoice_id;
		}

		$last_zone = '';
		for($i=0; $i<$count; $i++){			
			if($zone_arr[$i]==''){
				$zone_arr[$i] = $last_zone; 
			}
			$charge = chkZonePrice($zone_arr[$i]);
			$extraCharge = chkExtraCharge($massenger_arr[$i]);
			$charge = $charge+$extraCharge;

			$sql = "UPDATE `zone` SET `reserved`='1' WHERE `id`='".$zone_arr[$i]."'";
			$query = mysql_query($sql);

			$curDate = date("Y-m-d");
			$curTime = date("H:i:s");
			if($curTime < '07:00:00'){
				$lastDate = date ("Y-m-d", strtotime("-1day", strtotime($curDate)));
				$curTime = '23:59:59';
				$dateTime = $lastDate.' '.$curTime;
				$sql = "INSERT INTO `order_massage` (`order_inv`,`zone_id`,`employee_id`,`price`, `order_person`, `start`) VALUES ('".$createInvoice."', '".$zone_arr[$i]."', '".$massenger_arr[$i]."', '".$charge."', '".accountDecrypt($_SESSION['user_id'])."', '$dateTime')";

			}else{
				$dateTime = $curDate.' '.$curTime;
				$sql = "INSERT INTO `order_massage` (`order_inv`,`zone_id`,`employee_id`,`price`, `order_person`) VALUES ('".$createInvoice."', '".$zone_arr[$i]."', '".$massenger_arr[$i]."', '".$charge."', '".accountDecrypt($_SESSION['user_id'])."')";
			}

			//$sql = "INSERT INTO `order_massage` (`order_inv`,`zone_id`,`employee_id`,`price`, `order_person`) VALUES ('".$createInvoice."', '".$zone_arr[$i]."', '".$massenger_arr[$i]."', '".$charge."', '".accountDecrypt($_SESSION['user_id'])."')";
			$query = mysql_query($sql);

			
			$last_zone = $zone_arr[$i];
		}
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success
		);
	}
	returnJSON($json_arr);
}
function chkExtraCharge($massenger_id){
	$extra_charge_sql = "SELECT `salary` FROM `employee` WHERE `id`='".$massenger_id."'";
	$extra_charge_query = mysql_query($extra_charge_sql);
	$extraCharge = mysql_fetch_assoc($extra_charge_query);
	return $extraCharge['salary'];
}
function bookingSauna($zone_value, $customer_value, $invoice_id){
	$zone_value = str_replace('zone', '', $zone_value);
	$zone_value = substr($zone_value, 0, -1);
	$zone_arr = explode(',', $zone_value);
	$zone_key = $zone_arr[0];
	
	$sql = "SELECT COUNT(*) FROM `zone` WHERE `reserved`='0' AND `active`='1' AND `id` IN (".$zone_value.")";
	$query = mysql_query($sql);
	$count_result = mysql_fetch_assoc($query);

	$count = count($zone_arr);
	if($count_result['COUNT(*)'] != $count){
		global $system_status_failed;
		$json_arr = array(
			'process' => $system_status_failed
		);
	} else {
		//	$createInvoice = createInvoice($zone_key, $zone_value, $customer_value);
		if($invoice_id=='0' || $invoice_id==''){
			$createInvoice = createInvoice($zone_key, $zone_value, $customer_value);
			//$createInvoice = createInvoice($zone_key, $zone_value, $customer_value);
		}else{
			$createInvoice = invZone($invoice_id, $zone_key);
			//$createInvoice = $invoice_id;
		}

		for($i=0; $i<$count; $i++){
			$charge = chkZonePrice($zone_arr[$i]);

			$sql = "UPDATE `zone` SET `reserved`='1' WHERE `id`='".$zone_arr[$i]."'";
			$query = mysql_query($sql);

			$curDate = date("Y-m-d");
			$curTime = date("H:i:s");
			if($curTime < '07:00:00'){
				$lastDate = date ("Y-m-d", strtotime("-1day", strtotime($curDate)));
				$curTime = '23:59:59';
				$dateTime = $lastDate.' '.$curTime;
				$sql = "INSERT INTO `order_sauna` (`order_inv`,`zone_id`,`price`, `total`, `order_person`, `start`) VALUES ('".$createInvoice."', '".$zone_arr[$i]."', '".$charge."', '".$charge."', '".accountDecrypt($_SESSION['user_id'])."', '$dateTime')";
			}else{
				$dateTime = $curDate.' '.$curTime;
				$sql = "INSERT INTO `order_sauna` (`order_inv`,`zone_id`,`price`, `total`, `order_person`) VALUES ('".$createInvoice."', '".$zone_arr[$i]."', '".$charge."', '".$charge."', '".accountDecrypt($_SESSION['user_id'])."')";
			}
			
			//$sql = "INSERT INTO `order_sauna` (`order_inv`,`zone_id`,`price`, `total`, `order_person`) VALUES ('".$createInvoice."', '".$zone_arr[$i]."', '".$charge."', '".$charge."', '".accountDecrypt($_SESSION['user_id'])."')";
			$query = mysql_query($sql);
		}
		

		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success
		);
	}
	returnJSON($json_arr);
}
function bookingMember($member_id){	
	$createInvoice = createMemberInvoice($member_id);

	$charge = 0;

	$dateTime = date("Y-m-d H:i:s");
	$sql = "INSERT INTO `order_sauna` (`order_inv`,`customer_id`,`price`, `total`, `order_person`, `end`) VALUES ('".$createInvoice."', '".$member_id."', '".$charge."', '".$charge."', '".accountDecrypt($_SESSION['user_id'])."', '$dateTime')";
	$query = mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'Success'
	);

	returnJSON($json_arr);
}

chkSession();
switch($type){
	case 'restaurant':
		$zone_value	= mysql_real_escape_string($_REQUEST['zoneValue']);
		$customer_value	= mysql_real_escape_string($_REQUEST['customerValue']);
		$invoice_id			= mysql_real_escape_string($_REQUEST['invoiceID']);
		bookingRestaurant($zone_value, $customer_value, $invoice_id);
		break;
	case 'snooker':
		$zone_value	= mysql_real_escape_string($_REQUEST['zoneValue']);
		$customer_value	= mysql_real_escape_string($_REQUEST['customerValue']);
		$invoice_id			= mysql_real_escape_string($_REQUEST['invoiceID']);
		bookingSnooker($zone_value, $customer_value, $invoice_id);
		break;
	case 'massage':
		$zone_value	= mysql_real_escape_string($_REQUEST['zoneValue']);
		$customer_value	= mysql_real_escape_string($_REQUEST['customerValue']);
		$massenger_value	= mysql_real_escape_string($_REQUEST['massengerValue']);
		$invoice_id			= mysql_real_escape_string($_REQUEST['invoiceID']);
		bookingMassage($zone_value, $customer_value, $massenger_value, $invoice_id);
		break;
	case 'sauna':
		$zone_value	= mysql_real_escape_string($_REQUEST['zoneValue']);
		$customer_value	= mysql_real_escape_string($_REQUEST['customerValue']);
		$invoice_id			= mysql_real_escape_string($_REQUEST['invoiceID']);
		bookingSauna($zone_value, $customer_value, $invoice_id);
		break;
	case 'member':
		$member_id	= mysql_real_escape_string($_REQUEST['member_id']);
		bookingMember($member_id);
		break;
}

?>