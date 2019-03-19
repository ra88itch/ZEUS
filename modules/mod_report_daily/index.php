<?php
defined('R88PROJ') or die ('SYSTEM ERROR');

$type	= $_REQUEST['type'];
function accountName($account_id){
	$sql = "SELECT * FROM `account` WHERE `id`='".$account_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['firstname'].' '.$results['lastname'];
}

function dayResult($date){

	$total = 0;
	$html = '<table>';

	$sql = "SELECT SUM(`grand_total`) AS `gt`, `employee_id` AS `employee_id` FROM `invoice_bill` WHERE DATE(`checkout`)='$date' GROUP BY `employee_id`";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		$total = $total+$result['gt'];
		$html .= '<tr><td style="width:300px;">'.accountName($result['employee_id']). '</td><td>'. $result['gt'].'</td></tr>';
	
	}
	$html .= '<tr><td style="width:300px;">รวม</td><td>'. $total.'</td></tr>';
	$html .= '</table>';

	global $system_status_success;
	$json_arr = array(
		'process'	=>	$system_status_success,
		'html' 	=>	$html
	);
	returnJSON($json_arr);
}

chkSession();
switch($type){
	case 'dayResult':
		$date =  $_POST['date'];
		dayResult($date);
		break;
}

?>
