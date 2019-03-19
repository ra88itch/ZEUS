<?php
defined('R88PROJ') or die ('SYSTEM ERROR');

$customer_volumn	= mysql_real_escape_string($_REQUEST['volumn']);

function chkZone($customer_volumn, $zone_category){
	$zone = array();
	$sql = "SELECT * FROM `zone` WHERE `reserved` = '0' AND `zone_category` IN (1,2)";
	$query = mysql_query($sql);	
	while($results = mysql_fetch_assoc($query)) {
		array_push($zone,array(
				'id' 			=>	$results['id'],
				'zone_category'	=>	$results['zone_category'],
				'zone'			=>	$results['zone'],
				'zone_volumn'	=>	$results['zone_volumn']
			)
		);	
	}

	$massager= array();
	$sql = "SELECT * FROM `employee` WHERE `reserved`='0' AND `active`='1' AND `position`='0'";
	$query = mysql_query($sql);	
	while($results = mysql_fetch_assoc($query)) {
		array_push($massager,array(
				'id' 		=>	$results['id'],
				'code'		=>	$results['code'],
				'nickname'	=>	$results['nickname'],
				'img'	=>	$results['images']
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
			'zone' => $zone,
			'massager' => $massager
		);
	}
	returnJSON($json_arr);
}
chkSession();
chkZone($customer_volumn, $zone_category);
?>