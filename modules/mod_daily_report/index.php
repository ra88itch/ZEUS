<?php
defined('R88PROJ') or die ('SYSTEM ERROR');

$type	= $_REQUEST['type'];

function dayResult($date){
	$user_id = accountDecrypt($_SESSION['user_id']);

	$cash_count = 0;
	$credit_count = 0;
	$cash = 0;
	$credit = 0;
	$html = '';
	$detail = '';
	$array = '';
	$i = 0;

	$sql = "SELECT * FROM `invoice_bill` WHERE `employee_id`='$user_id' AND DATE(`checkout`)='$date'";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){

		$time = date("H:i:s", strtotime($result['checkout']));
		if($time > '07:00:00'){
			if($result['payment']=='1'){
				$cash = $cash+$result['grand_total'];
				$cash_count++;
			}else{
				$credit = $credit+$result['grand_total'];
				$credit_count++;
			}

			if($i != 0){
				$array .= ',';
			}
			$array .= $result['inv_ref'];

			$i++;
		}
	}
	$chkdetail = chkdetail($array);
	$html .= 'รายการเงินสด '.$cash_count .' รายการ';
	$html .= '<br>';
	$html .= 'รายรับ '.$cash .' บาท';
	$html .= '<br><br>';

	$html .= 'รายการบัตรเครดิต '.$credit_count .' รายการ';
	$html .= '<br>';
	$html .= 'รายรับ '.$credit .' บาท';
	$html .= '<br>------------<br>';
	$html .= $chkdetail;

	global $system_status_success;
	$json_arr = array(
		'process'	=>	$system_status_success,
		'html' 	=>	$html
	);
	returnJSON($json_arr);
}
function chkdetail($array){
	//return $array;
	$html = '';
	$sql = "SELECT SUM(`unit`) AS `unit`, `menu_id` FROM `order` WHERE `order_inv` IN ($array) AND `order_status`='5' GROUP BY `menu_id`";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		$chkMenuType = chkMenuType($result['menu_id']);
		if($chkMenuType[0] == true){
			$html .= $chkMenuType[1]. ' จำนวน ' .$result['unit'].'<br>';
		}
	}
	return $html;
}
function chkMenuType($menu_id){
	$sql = "SELECT * FROM `menu` WHERE `id`='$menu_id'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	$type_by_cooking = $result['type_by_cooking'];
	if($type_by_cooking =='8' || $type_by_cooking == '9'){
		return array(true, $result['menu_name_th']);
	}else{
		return array(false,'');
	}
}

chkSession();
switch($type){
	case 'dayResult':
		$date =  $_POST['date'];
		dayResult($date);
		break;
}

?>
