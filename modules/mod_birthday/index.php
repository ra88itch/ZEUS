<?php 
defined('R88PROJ') or die($system_error);

function birthToday(){
	$customer = array();
	$customer_sql = "SELECT `id`, `firstname`, `lastname` FROM `customer` WHERE MONTH(CURDATE()) = MONTH(`dob`) and DAY(CURDATE()) = DAY(`dob`) AND `active`='1'";
	$customer_query = mysql_query($customer_sql);
	while($customer_result = mysql_fetch_assoc($customer_query)) {
		array_push($customer,array(
			'id'		=>	$customer_result['id'],
			'firstname'	=>	$customer_result['firstname'],
			'lastname'	=>	$customer_result['lastname']
		));		
	}
	
	if(empty($customer)) {
		global $system_status_failed;
			$json_arr = array(
				'process' => $system_status_failed
			);
	} else {
		global $system_status_success;
			$json_arr = array(
				'process' => $system_status_success,
				'customer' => $customer
			);
	}
returnJSON($json_arr);
		
}

chkSession();
birthToday();
?>