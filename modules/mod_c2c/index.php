<?php
defined('R88PROJ') or die ($system_error);

$type			=	$_POST['type'];
function createInvoice(){
	$name	=	'ค่าบริการเบิกเงินสด';
	$sql	=	"INSERT INTO `invoice` (`zone`, `customer_value`, `zone_extra`) VALUES ('0','0','".$name."')";
	$query	=	mysql_query($sql);
	$invID	=	mysql_insert_id();
	return $invID;
}
function charge(){
	$sql = "SELECT `charge` FROM `zone_category` WHERE `id`='14'";
	$query = mysql_query($sql);
	$charge_result = mysql_fetch_assoc($query);
	return $charge_result['charge'];
}
function c2c($cash){
	$createInvoice = createInvoice();
	$charge = $cash * (charge()/100);
	$total = $charge+$cash;
	
	$sql	=	"INSERT INTO `order_cash` (`order_inv`, `unit`,  `price`,  `charge`, `total`, `employee_id`) VALUES ('".$createInvoice."', '1', '".$cash."', '".$charge."', '".$total."', '".accountDecrypt($_SESSION['user_id'])."')";
	$query = mysql_query($sql);


	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success
	);

	returnJSON($json_arr);
}
chkSession();
switch($type){
	case 'c2c':
		$cash =  mysql_real_escape_string($_REQUEST['cash']);
		c2c($cash);
		break;
}
?>