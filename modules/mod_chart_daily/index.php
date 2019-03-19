<?php
defined('R88PROJ') or die ('SYSTEM ERROR');

$type	= $_REQUEST['type'];

function dailyValue($date){
	$valueTime = array();
	for($j=1; $j<=4; $j++){
		for($i=10; $i<=23; $i++){
			$valueTime[$j][] = getInvRef($date, $i, $j);
		}
		for($i=0; $i<=9; $i++){
			$valueTime[$j][] = getInvRef($date, '0'.$i, $j);
		}
	}
	/*$massage = '['.join(',', $valueTime[1]).']';
	$sauna = '['.join(',', $valueTime[2]).']';
	$restaurant = '['.join(',', $valueTime[3]).']';
	$snooker = '['.join(',', $valueTime[4]).']';*/

	/*$massage = join(', ', $valueTime[1]);
	$sauna = join(', ', $valueTime[2]);
	$restaurant = join(', ', $valueTime[3]);
	$snooker = join(', ', $valueTime[4]);*/

	global $system_status_success;
	$json_arr = array(
		'process'		=>	$system_status_success,
		'date'			=>	$date,
		'massage' 		=>	$valueTime[1],
		'sauna' 		=>	$valueTime[2],
		'restaurant' 	=>	$valueTime[3],
		'snooker' 		=>	$valueTime[4]
	);
	returnJSON($json_arr);
}
function getInvRef($date, $time, $type){
	$sql = "SELECT * FROM `invoice_bill` WHERE DATE(`checkout`)='$date' AND DATE_FORMAT(`realtimes`, '%H')='$time'";
	$query = mysql_query($sql);	
	while($result = mysql_fetch_assoc($query)){
		$inv_array[] = $result['inv_ref'];
	}
	$inv_array = join(',', $inv_array);

	switch($type){
		case '1':
			$value = getMassage($inv_array);
			break;
		case '2':
			$value = getSauna($inv_array);
			break;
		case '3':
			$value = getRestaurant($inv_array);
			break;
		case '4':
			$value = getSnooker($inv_array);
			break;
	}
	
	return $value;
}
function getMassage($array){
	$sql = "SELECT SUM(`total`) AS `SUM` FROM `order_massage` WHERE `order_inv` IN ($array)";
	$query = mysql_query($sql);	
	$result = mysql_fetch_assoc($query);
	if($result['SUM']==null){
		$result['SUM'] = '0';
	}
	return $result['SUM'];
}
function getSauna($array){
	$sql = "SELECT SUM(`total`) AS `SUM` FROM `order_sauna` WHERE `order_inv` IN ($array)";
	$query = mysql_query($sql);	
	$result = mysql_fetch_assoc($query);
	if($result['SUM']==null){
		$result['SUM'] = '0';
	}
	return $result['SUM'];
}
function getRestaurant($array){
	$sql = "SELECT SUM(`total`) AS `SUM` FROM `order` WHERE `order_inv` IN ($array) AND `order_status`='5'";
	$query = mysql_query($sql);	
	$result = mysql_fetch_assoc($query);
	if($result['SUM']==null){
		$result['SUM'] = '0';
	}
	return $result['SUM'];
}
function getSnooker($array){
	$sql = "SELECT SUM(`total`) AS `SUM` FROM `order_snooker` WHERE `order_inv` IN ($array)";
	$query = mysql_query($sql);	
	$result = mysql_fetch_assoc($query);
	if($result['SUM']==null){
		$result['SUM'] = '0';
	}
	return $result['SUM'];
}


chkSession();
switch($type){
	case 'get_daily_value':
		$date =  $_POST['date'];
		if($date == '0'){
			$date = date('Y-m-d');
		}
		$time = date('H');
		if($date == date('Y-m-d') && $time <= '09'){
			$date = date('Y-m-d', strtotime("-1 days"));
		}
		dailyValue($date);
		break;
}

?>
