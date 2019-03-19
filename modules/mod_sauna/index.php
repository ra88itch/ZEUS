<?php
defined('R88PROJ') or die ('SYSTEM ERROR');

$customer_volumn	= mysql_real_escape_string($_REQUEST['volumn']);

function chkZone($customer_volumn, $zone_category){
	$zone = array();
	$sql = "SELECT * FROM `zone` WHERE `reserved` = '0' AND `zone_category`='3'  AND `active`='1' LIMIT 16";
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
	$sql = "SELECT * FROM `zone` WHERE `reserved` = '0' AND `zone_category`='10'  AND `active`='1' LIMIT 16";
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
	$sql = "SELECT * FROM `zone` WHERE `reserved` = '0' AND `zone_category`='15'  AND `active`='1' LIMIT 8";
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
/*for($i=0; $i < 200; $i++ ){
	$sql = "INSERT INTO `thairesc_proj`.`locker_key` (`id` ,`inv_id`) VALUES (NULL , '0')";
	$query = mysql_query($sql);
	$html = $sql.'<br>';
}*/

?>
