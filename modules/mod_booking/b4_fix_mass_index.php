<?php
defined('R88PROJ') or die ('SYSTEM ERROR');

$type	= $_REQUEST['type'];

function createInvoice($zone_key, $zone_value, $customer_value){
	$sql = "INSERT INTO `invoice` (`zone`,`zone_extra`,`customer_value`) VALUES ('".$zone_key."','".$zone_value."','".$customer_value."')";
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
function bookingRestaurant($zone_value, $customer_value){
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
		for($i=0; $i<$count; $i++){
			$sql = "UPDATE `zone` SET `reserved`='1' WHERE `id`='".$zone_arr[$i]."'";
			$query = mysql_query($sql);
		}

		$createInvoice = createInvoice($zone_key, $zone_value, $customer_value);

		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'invoice_no' => $createInvoice
		);
	}
	returnJSON($json_arr);
}
function bookingSnooker($zone_value, $customer_value){
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
		$createInvoice = createInvoice($zone_key, $zone_value, $customer_value);

		for($i=0; $i<$count; $i++){
			$charge = chkZonePrice($zone_arr[$i]);

			$sql = "UPDATE `zone` SET `reserved`='1' WHERE `id`='".$zone_arr[$i]."'";
			$query = mysql_query($sql);

			$sql = "INSERT INTO `order_snooker` (`order_inv`,`zone_id`,`price`) VALUES ('".$createInvoice."', '".$zone_arr[$i]."', '".$charge."')";
			$query = mysql_query($sql);
		}
		

		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success
		);
	}
	returnJSON($json_arr);
}
function bookingMassage($zone_value, $customer_value, $massenger_value){
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
		$createInvoice = createInvoice($zone_key, $zone_value, $customer_value);

		for($i=0; $i<$count; $i++){
			$charge = chkZonePrice($zone_arr[$i]);

			$sql = "UPDATE `employee` SET `reserved`='1' WHERE `id`='".$massenger_arr[$i]."'";
			$query = mysql_query($sql);

			$sql = "INSERT INTO `order_massage` (`order_inv`,`zone_id`,`employee_id`,`price`) VALUES ('".$createInvoice."', '".$zone_arr[$i]."', '".$massenger_arr[$i]."', '".$charge."')";
			$query = mysql_query($sql);
		}
		global $system_status_success;
		$json_arr = array(
			'process' => $sql
		);
	}
	returnJSON($json_arr);
}
function bookingSauna($zone_value, $customer_value){
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
		$createInvoice = createInvoice($zone_key, $zone_value, $customer_value);

		for($i=0; $i<$count; $i++){
			$charge = chkZonePrice($zone_arr[$i]);

			$sql = "UPDATE `zone` SET `reserved`='1' WHERE `id`='".$zone_arr[$i]."'";
			$query = mysql_query($sql);

			$sql = "INSERT INTO `order_sauna` (`order_inv`,`zone_id`,`price`) VALUES ('".$createInvoice."', '".$zone_arr[$i]."', '".$charge."')";
			$query = mysql_query($sql);
		}
		

		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success
		);
	}
	returnJSON($json_arr);
}

chkSession();
switch($type){
	case 'restaurant':
		$zone_value	= mysql_real_escape_string($_REQUEST['zoneValue']);
		$customer_value	= mysql_real_escape_string($_REQUEST['customerValue']);
		bookingRestaurant($zone_value, $customer_value);
		break;
	case 'snooker':
		$zone_value	= mysql_real_escape_string($_REQUEST['zoneValue']);
		$customer_value	= mysql_real_escape_string($_REQUEST['customerValue']);
		bookingSnooker($zone_value, $customer_value);
		break;
	case 'massage':
		$zone_value	= mysql_real_escape_string($_REQUEST['zoneValue']);
		$customer_value	= mysql_real_escape_string($_REQUEST['customerValue']);
		$massenger_value	= mysql_real_escape_string($_REQUEST['massengerValue']);
		//$invoice_no			= mysql_real_escape_string($_REQUEST['invoiceNo']);
		bookingMassage($zone_value, $customer_value, $massenger_value);
		break;
	case 'sauna':
		$zone_value	= mysql_real_escape_string($_REQUEST['zoneValue']);
		$customer_value	= mysql_real_escape_string($_REQUEST['customerValue']);
		bookingSauna($zone_value, $customer_value);
		break;
}

?>