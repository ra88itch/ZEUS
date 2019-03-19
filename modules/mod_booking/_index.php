<?php
defined('R88PROJ') or die ('SYSTEM ERROR');

$zone_value	= mysql_real_escape_string($_REQUEST['zoneValue']);
$customer_value	= mysql_real_escape_string($_REQUEST['customerValue']);

function booking($zone_value, $customer_value){
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
function createInvoice($zone_key, $zone_value, $customer_value){
	$sql = "INSERT INTO `invoice` (`zone`,`zone_extra`,`customer_value`) VALUES ('".$zone_key."','".$zone_value."','".$customer_value."')";
	$query = mysql_query($sql);
	return mysql_insert_id();
}
chkSession();
booking($zone_value, $customer_value);
?>