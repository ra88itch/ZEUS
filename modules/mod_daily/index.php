<?php
defined('R88PROJ') or die ('SYSTEM ERROR');

$type	= $_REQUEST['type'];

function dayResult($date){
	$user_id = accountDecrypt($_SESSION['user_id']);

	$cash_count = 0;
	$credit_count = 0;
	$entertain_count = 0;
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
			}else if($result['payment']=='2'){
				$credit = $credit+$result['grand_total'];
				$credit_count++;
			}else{
				$entertain = $entertain+$result['grand_total'];
				$entertain_count++;
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
	$html .= '<br><br>';

	$html .= 'รายการเอ็นเตอร์เทน '.$entertain_count .' รายการ';
	$html .= '<br>';
	$html .= 'รายรับ '.$entertain .' บาท';
	$html .= '<br><br>';

	$chkCash = chkCash($array);
	if($chkCash[0]>0){
		$html .= 'ลูกค้าเบิกเงินสด '.$chkCash[0] .' รายการ';
		$html .= '<br>';
		$html .= 'จ่ายลูกค้า '.$chkCash[1] .' บาท';
		$html .= '<br>';
		$html .= 'ค่าบริการ '.$chkCash[2] .' บาท';		
	}
	$html .= '<br>------------<br>';
	$html .= $chkdetail;

	$chkSauna = chkSauna($array);
	if($chkSauna>0){
		$html .= '<br><br>';
		$html .= 'สรุปยอดใช้บริการซาวน่า';		
		$html .= '<br>';
		$html .= 'มีผู้ใช้บริการจำนวน '.$chkSauna.' คน';
	}

	$html .= '<br><br>';
	$html .= 'สรุปยอดใช้บริการนวด';
	$chkMassage = chkMassage($array, '0');
	if($chkMassage>0){
		$html .= '<br>';
		$html .= 'มีผู้ใช้บริการค่าชั่วโมงพนักงานนวดจำนวน '.$chkMassage.' คน';
	}
	$chkMassage = chkMassage($array, '1');
	if($chkMassage>0){
		$html .= '<br>';
		$html .= 'มีผู้ใช้บริการนวดแผนไทยห้องรวมจำนวน '.$chkMassage.' คน';
	}
	$chkMassage = chkMassage($array, '2');
	if($chkMassage>0){
		$html .= '<br>';
		$html .= 'มีผู้ใช้บริการนวดแผนไทยห้อง VIPจำนวน '.$chkMassage.' คน';
	}
	$chkMassage = chkMassage($array, '3');
	if($chkMassage>0){
		$html .= '<br>';
		$html .= 'มีผู้ใช้บริการนวดน้ำมัน / สปา จำนวน '.$chkMassage.' คน';
	}
	$chkMassage = chkMassage($array, '4');
	if($chkMassage>0){
		$html .= '<br>';
		$html .= 'มีผู้ใช้บริการค่าบริการห้อง VIP จำนวน '.$chkMassage.' คน';
	}
	$html .= '<br><br>';
	$html .= 'สรุปยอดใช้บริการร้านอาหาร';
	$chkRestaurant = chkRestaurant($array);
	$html .= '<br>';
	$html .= 'มีผู้ใช้บริการจำนวน '.$chkRestaurant.' โต๊ะ';

	$html .= '<br><br>';
	$html .= 'สรุปยอดใช้บริการสนุ๊กเกอร์';
	$chkSnooker = chkSnooker($array);
	$html .= '<br>';
	$html .= 'มีผู้ใช้บริการจำนวน '.$chkSnooker.'ชั่วโมง';


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
	/*if($type_by_cooking =='8' || $type_by_cooking == '9'){
		return array(true, $result['menu_name_th']);
	}else{
		return array(false,'');
	}*/

	return array(true, $result['menu_name_th']);
}
function chkCash($array){
	$sql = "SELECT COUNT(*) AS `count`, SUM(`price`) AS `price`, SUM(`charge`) AS `charge` FROM `order_cash` WHERE `order_inv` IN ($array)";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	return array($result['count'], $result['price'], $result['charge']);
}
function chkSauna($array){
	$sql = "SELECT COUNT(*) FROM `order_sauna` WHERE `order_inv` IN ($array)";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	return $result['COUNT(*)'];
}
function chkMassage($array, $type){
	$sql = "SELECT COUNT(*) FROM `order_massage` WHERE `order_inv` IN ($array) AND `massage_type`='$type'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	return $result['COUNT(*)'];
}
function chkRestaurant($array){
	$sql = "SELECT COUNT(*) FROM `order_restaurant` WHERE `order_inv` IN ($array)";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	return $result['COUNT(*)'];
}
function chkSnooker($array){
	$sql = "SELECT SUM(`times_in_min`) AS `minutes` FROM `order_snooker` WHERE `order_inv` IN ($array)";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	$hour = floor($result['minutes']/60);
	$min = $result['minutes']%60;
	return $hour.'.'.$min;
}

chkSession();
switch($type){
	case 'dayResult':
		$date =  $_POST['date'];
		dayResult($date);
		break;
}
?>