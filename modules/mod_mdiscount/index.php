<?php
defined('R88PROJ') or die ($system_error);

$type			=	$_POST['type'];

function setPrice($price, $category){
	$sql = "UPDATE `customer_type` SET discount_percent='$price' WHERE `id`='".$category."'";
	$query = mysql_query($sql);	
}

chkSession();
switch($type){
	case 'setDiscount':
		$sql = "SELECT `id` FROM `customer_type`";
		$query = mysql_query($sql);	
		while($results = mysql_fetch_assoc($query)){			
			$discount_percent =  mysql_real_escape_string($_REQUEST['discount_percent'.$results['id']]);
			setPrice($discount_percent, $results['id']);
		}		
		break;
}
?>