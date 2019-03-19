<?php
defined('R88PROJ') or die ('SYSTEM ERROR');

$type	= $_POST['type'];
function listDiscount(){
	$discount = array();	
	$sql = "SELECT * FROM `discount` WHERE `active` = '1'";
	$query = mysql_query($sql);	
	while($results = mysql_fetch_assoc($query)) {		
		array_push($discount,array(
				'id' 				=>	$results['id'],
				'discount_name'		=>	$results['name'],
				'discount_price'	=>	$results['price']				
			)
		);	
	}
	
	if(empty($discount)) {
		global $system_status_failed;
		$json_arr = array(
			'process' => $system_status_failed
		);
	} else {
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'discount' => $discount

		);
	}
	returnJSON($json_arr);
}
function addDiscount($invoiceID, $discountID){
	$sql = "SELECT * FROM `discount` WHERE `id`='$discountID'";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);	

	$sql	= "INSERT INTO `order_discount` (order_inv, `discount_id`,`price`,`total`, `order_person`) VALUES ('".$invoiceID."', '".$discountID."', '".$results['price']."', '".$results['price']."', '".accountDecrypt($_SESSION['user_id'])."')";
	$query	= mysql_query($sql);
}
function deleteDiscount($invoiceID, $orderDiscount){
	$sql = "DELETE FROM `order_discount` WHERE `id`='$orderDiscount'";
	$query = mysql_query($sql);		
}
chkSession();

switch($type){
	case 'addDiscount':
		$invoiceID		= $_POST['invoiceID'];
		$discountID		= $_POST['discountID'];
		addDiscount($invoiceID, $discountID);
		break;
	case 'deleteDiscount':
		$invoiceID		= $_POST['invoiceID'];
		$orderDiscount	= $_POST['orderDiscount'];
		deleteDiscount($invoiceID, $orderDiscount);
		break;
	default:
		listDiscount();
		break;
}

?>