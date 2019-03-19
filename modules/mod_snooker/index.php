<?php
defined('R88PROJ') or die ('SYSTEM ERROR');

$customer_volumn	= mysql_real_escape_string($_REQUEST['volumn']);

function chkZone($customer_volumn, $zone_category){
	$zone = array();
	$sql = "SELECT * FROM `zone` WHERE `reserved` = '0' AND `zone_category` IN (8,9,23) AND `active`='1'";
	$query = mysql_query($sql);	
	while($results = mysql_fetch_assoc($query)) {
		array_push($zone,array(
				'id' 				=>	$results['id'],
				'zone_category'		=>	$results['zone_category'],
				'zone'				=>	$results['zone'],
				'zone_volumn'		=>	$results['zone_volumn'],
				'reserved'			=>	$results['reserved']
			)
		);	
	}
	if(empty($zone)) {
		global $system_status_failed;
		$json_arr = array(
			'process' => $sql
		);
	} else {
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'zone' => $zone
		);
	}
	returnJSON($json_arr);
}
chkSession();
chkZone($customer_volumn, $zone_category);
?>