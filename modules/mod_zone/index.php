<?php
defined('R88PROJ') or die ($system_error);

$zone_id		=	$_POST['zone_id'];
$type			=	$_POST['type'];

function changeZoneStatus($zone_id){
	if($zone_id > 1){
		$sql = "UPDATE `zone` SET `active`=IF(`active`=1, 0, 1), `reserved`='0' WHERE `id`='$zone_id'";
		$query	= mysql_query($sql);

		$sql = "SELECT `active` FROM `zone` WHERE `id`='$zone_id'";
		$query	= mysql_query($sql);
		$result	= mysql_fetch_assoc($query);
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'zone' => $zone_id,
			'active' => $result['active']
		);
	}else{
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'zone' => $zone_id,
			'active' => 1
		);
	}
	returnJSON($json_arr);
	
}

chkSession();
switch($type){
	case 'changeZoneStatus':
		changeZoneStatus($zone_id);
		break;
}
?>