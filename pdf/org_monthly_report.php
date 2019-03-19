<?php
function chkMemberBio($member_id){
	$sql = "SELECT `customer_type` FROM `customer` WHERE `id`='$member_id'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	if($result['customer_type']<3){
		return 'male';
	}else{
		return 'female';
	}
}
function chkMassage($massage_type){
	switch($massage_type){
		case 3:
			return 'นวดสปา/น้ำมัน';
			break;
		default:
			return 'นวดแผนโบราณ';
			break;
	}
}
function chkEntertain($array){
	$html = '';
	$sql = "SELECT `entertainer`.`entertainer_name`, SUM(`invoice_bill`.`grand_total`) AS `grand_total` FROM `invoice_bill` LEFT JOIN `entertainer`
ON `entertainer`.`id` = `invoice_bill`.`entertainer` WHERE `invoice_bill`.`inv_ref` IN ($array) GROUP BY `invoice_bill`.`entertainer` ORDER BY `invoice_bill`.`entertainer`";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		if($result['grand_total'] > 0){
			$html .= '<div><b>'.$result['entertainer_name'].'</b></div>';
			$html .= '<div> - รวมยอด '.$result['grand_total'].' บาท</div>';
		}
	}	
	return $html;
}
function countOrder($inv_ref, $table){
	$sql = "SELECT SUM(`total`) AS `totals` FROM `$table` WHERE `order_inv`='$inv_ref'";
	
	//return $sql;
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	return $result['totals'];
}
function chkDiscount($array){
	$details = '';
	$totals = 0;
	$sql = "SELECT `discount`.`name`, SUM(`order_discount`.`unit`) AS `units`, SUM(`order_discount`.`total`) AS `totals` FROM `order_discount` LEFT JOIN `discount`
ON `discount`.`id` = `order_discount`.`discount_id` WHERE `order_inv` IN ($array) GROUP BY `discount_id`";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		$totals = $totals+$result['totals'];
		$details .= '<div>'.$result['name'].' -  จำนวน '.$result['units'].' / รวม '.$result['totals'].' บาท</div>';
	}
	//$result = mysql_fetch_assoc($query);
	if($totals==0){
		$html = '<div>ไม่มีรายการคูปองส่วนลด</div>';
	}else{
		$html = '<div><b>ส่วนลดรวม '.$totals.' บาท</b></div>';
	}
	return $html.$details;
}
function accountName($account_id){
	$sql = "SELECT * FROM `account` WHERE `id`='".$account_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['firstname'].' '.$results['lastname'];
}
function chkOrder($array){
	$i = 1;
	$html = '<tr>';
	$sql = "SELECT SUM(`unit`) AS `unit`, `menu_id` FROM `order` WHERE `order_inv` IN ($array) AND `order_status`='5' GROUP BY `menu_id`";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		$chkMenuType = chkMenuType($result['menu_id']);		
		if($chkMenuType[0] == true){
			$html .= '<td style="width:50%;">'.$chkMenuType[1]. ' จำนวน ' .$result['unit'].'</td>';
			if($i % 2 == 0){
				$html .= '</tr><tr>';
			}
			$i++;
		}		
	}
	if($i % 2 == 1){
		$html .= '<td style="width:50%;"></td>';
	}
	$html .= '</tr>';
	return $html;
}
function chkOrderSum($array, $chkRestaurant){
	$sum = 0;
	$foodtotal = 0;
	$drinktotal = 0;
	$sql = "SELECT SUM(`unit`) AS `unit`, `menu_id`, SUM(`total`) AS `total` FROM `order` WHERE `order_inv` IN ($array) AND `order_status`='5' GROUP BY `menu_id`";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		$is_food = isFood($result['menu_id']);
		if($is_food==true){
			$foodtotal = $foodtotal+$result['total'];
		}else{
			$drinktotal = $drinktotal+$result['total'];
		}
	}
	$total = $foodtotal+$drinktotal;
	$html = '<div>';
	$html .= '<b>รายรับรวม '.$total.' บาท';
	$html .= '</b></div><div>';
	$html .= 'รายรับจากเครื่องดื่ม, แอลกอฮฮล์, และอื่นๆ '.$drinktotal.' บาท';
	$html .= '</div><div>';
	$html .= 'รายรับจากรายการอาหาร '.$foodtotal.' บาท';
	$html .= '</div><div>';
	$html .= 'มีผู้ใช้บริการจำนวน '.$chkRestaurant.' โต๊ะ';
	$html .= '</div>';
	return $html;
}
function isFood($menu_id){
	$sql = "SELECT * FROM `menu` WHERE `id`='$menu_id' LIMIT 1";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	if($result['type_by_cooking']==8 || $result['type_by_cooking']==9|| $result['type_by_cooking']==16){
		return false;
	}else{
		return true;
	}
}
function chkPayCash($array){
	$cash = 0;
	$sql = "SELECT * FROM `order_cash` WHERE `order_inv` IN ($array)";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		$cash = $cash+$result['price'];		
	}
	return $cash;
}
function chkCash($array){
	$html = '';
	$cash = 0;
	$charge = 0;
	$sql = "SELECT * FROM `order_cash` WHERE `order_inv` IN ($array)";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		$html .= '<div>ลูกค้าเบิก '.$result['price'].' / ค่าบริการ  '.$result['charge'].'<div>';
		$cash = $cash+$result['price'];
		$charge = $charge+$result['charge'];		
	}
	$html .= '<div><b>รวมลูกค้าเบิก '.$cash.' / ค่าบริการ  '.$charge.'</b></div>';
	return $html;
}
function isEntertain($invoice_id){
	$sql = "SELECT `payment` FROM `invoice_bill` WHERE `inv_ref`='$invoice_id'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	if($result['payment']=='3'){
		return true;
	}
	return false;
}
function chkMember($array){
	$total = 0;
	$paid = 0;
	
	$entertain = 0;
	$entertain_value =0;
	$male_y = 0;
	$male_value_y = 0;
	$female_y = 0;	
	$female_value_y = 0;
	$male_m = 0;
	$male_value_m = 0;
	$female_m = 0;	
	$female_value_m = 0;

	$sql = "SELECT * FROM `order_member` WHERE `order_inv` IN ($array)";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		$isEntertain = isEntertain($result['order_inv']);
		if($isEntertain==true){
			$entertain++;
			$entertain_value = $entertain_value+$result['total'];
		}else{
			switch($result['zone_id']){
				case 2001:
					$male_m ++;
					$male_value_m = $male_value_m+$result['total'];
					break;
				case 2002:
					$male_y ++;
					$male_value_y = $male_value_y+$result['total'];
					break;
				case 2027:
					$female_y ++;
					$female_value_y = $female_value_y+$result['total'];
					break;
				case 2028:
					$female_m ++;
					$female_value_m = $female_value_m+$result['total'];
					break;
			}
			
			//$paid++;
			//$paid_value = $paid_value+$result['total'];
		}
		$total = $total+$result['total'];
	}
	$paid_value = $male_value_m + $female_value_y + $male_value_y + $female_value_m;
	if($total==0){
		$html = 'ไม่มีลูกค้าสมัครสมาชิก';
	}else{
		$html = '<div><b>';
		$html .= 'รายรับรวม '.$paid_value;
		$html .= ' บาท</b> </div>';
		$html .= '<div><b>รายปี</b></div>';
		$html .= '<div>ลูกค้าชายสมัครสมาชิกจำนวน '.$male_y.' / '.$male_value_y.' บาท</div>';
		$html .= '<div>ลูกค้าหญิงสมัครสมาชิกจำนวน '.$female_y.' / '.$female_value_y.' บาท</div>';
		$html .= '<div><b>รายเดือน</b></div>';
		$html .= '<div>ลูกค้าชายสมัครสมาชิกจำนวน '.$male_m.' / '.$male_value_m.' บาท</div>';
		$html .= '<div>ลูกค้าหญิงสมัครสมาชิกจำนวน '.$female_m.' / '.$female_value_m.' บาท</div>';
		$html .= '<div><b>ลูกค้าเอ็นเตอร์เทน</b>จำนวน '.$entertain.'</div>';
	}
	return $html;
}
/*function chkMember($array){
	$total = 0;
	$paid = 0;
	$paid_value = 0;
	$entertain = 0;
	$entertain_value =0;
	$sql = "SELECT * FROM `order_member` WHERE `order_inv` IN ($array)";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		$isEntertain = isEntertain($result['order_inv']);
		if($isEntertain==true){
			$entertain++;
			$entertain_value = $entertain_value+$result['total'];
		}else{
			$paid++;
			$paid_value = $paid_value+$result['total'];
		}
		$total = $total+$result['total'];
	}
	if($total==0){
		$html = 'ไม่มีลูกค้าสมัครสมาชิก';
	}else{
		$html = '<div><b>';
		$html .= 'รายรับรวม '.$paid_value;
		$html .= ' บาท</b> </div><div>';
		$html .= 'ลูกค้าสมัครสมาชิกจำนวน '.$paid;
		$html .= '</div><div>ลูกค้าเอ็นเตอร์เทนจำนวน '.$entertain;		
		$html .= '</div>';
	}
	return $html;
}*/
function chkCoupon($array){
	$details = '';
	$totals = 0;
	$sql = "SELECT `coupon`.`coupon_name`, SUM(`order_coupon`.`unit`) AS `units`, SUM(`order_coupon`.`total`) AS `totals` FROM `order_coupon` LEFT JOIN `coupon`
ON `coupon`.`id` = `order_coupon`.`coupon_id` WHERE `order_inv` IN ($array) GROUP BY `coupon_id`";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		$totals = $totals+$result['totals'];
		$details .= '<div>'.$result['coupon_name'].' -  จำนวน '.$result['units'].' / รวม '.$result['totals'].' บาท</div>';
	}
	//$result = mysql_fetch_assoc($query);
	if($totals==0){
		$html = '<div>ไม่มีรายการขายคูปอง</div>';
	}else{
		$html = '<div><b>รายรับรวม '.$totals.' บาท</b></div>';
	}
	return $html.$details;
}
function chkSauna($array){
	$sum = 0;
	$count_daily = 0;
	$count_member_male = 0;
	$count_member_female = 0;
	$count_fitt = 0;
	$count_sauna = 0;
	$count_sauna_fitt = 0;
	$count_coupon = 0;
	$sql = "SELECT * FROM `order_sauna` WHERE `order_inv` IN ($array)";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		if($result['customer_id'] == 0){
			$count_daily++;
			if($result['price'] <= 150){
				$count_fitt++;
			}else if($result['price'] <= 220){
				$count_sauna++;
			}else{
				$count_sauna_fitt++;
			}
		}else{
			$chkMemberBio = chkMemberBio($result['customer_id']);
			if($chkMemberBio == 'male'){
				$count_member_male++;
			}else{
				$count_member_female++;
			}
		}
		
		if($result['coupon']==1){
			$count_coupon++;
		}else{
			$sum = $sum+$result['total'];
		}
	}
	$sum = 'รายรับรวม '.$sum.' บาท';
	$daily = 'ผู้ใช้บริการรายวันจำนวน '.$count_daily.'คน';
	$member_male = 'สมาชิกชายใช้บริการจำนวน '.$count_member_male.'คน';
	$member_female = 'สมาชิกหญิงใช้บริการจำนวน '.$count_member_female.'คน';
	$fitt = 'พนักงานฟิตเนส จำนวน'.$count_fitt;
	$sauna = 'ซาวน่า จำนวน '.$count_sauna;
	$sauna_fitt = 'ซาวน่าและฟิตเนส จำนวน '.$count_sauna_fitt;
	if($count_coupon > 0){
		$coupon = 'มีผู้ใช้คูปอง จำนวน '.$count_coupon;
	}else{
		$coupon = '';
	}
	return '<div><b>'.$sum.'</b></div><div>'.$member_male.'</div><div>'.$member_female.'</div><div>'.$daily.'</div><div>'.$sauna.'</div><div>'.$sauna_fitt.'</div><div>'.$fitt.'</div><div>'.$coupon.'</div>';
}
/*function chkSauna($array){
	$sum = 0;
	$count_daily = 0;
	$count_member = 0;
	$count_sauna = 0;
	$count_sauna_fitt = 0;
	$count_coupon = 0;
	$sql = "SELECT * FROM `order_sauna` WHERE `order_inv` IN ($array)";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		if($result['customer_id'] == 0){
			$count_daily++;
			if($result['price'] <= 220){
				$count_sauna++;
			}else{
				$count_sauna_fitt++;
			}
		}else{
			$count_member++;
		}
		
		if($result['coupon']==1){
			$count_coupon++;
		}else{
			$sum = $sum+$result['total'];
		}
	}
	$sum = 'รายรับรวม '.$sum.' บาท';
	$daily = 'ผู้ใช้บริการรายวันจำนวน '.$count_daily.'คน';
	$member = 'สมาชิกใช้บริการจำนวน '.$count_member.'คน';
	$sauna = 'ซาวน่า จำนวน '.$count_sauna;
	$sauna_fitt = 'ซาวน่าและฟิตเนส จำนวน '.$count_sauna_fitt;
	if($count_coupon > 0){
		$coupon = 'มีผู้ใช้คูปอง จำนวน '.$count_coupon;
	}else{
		$coupon = '';
	}
	return '<div><b>'.$sum.'</b></div><div>'.$member.'</div><div>'.$daily.'</div><div>'.$sauna.'</div><div>'.$sauna_fitt.'</div><div>'.$coupon.'</div>';
}*/
function isVip($zone_id, $vip_category){
	$sql = "SELECT COUNT(*) FROM `zone` WHERE `id`='$zone_id' AND `zone_category`='$vip_category' LIMIT 1";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	if($result['COUNT(*)'] == 1){
		return true;
	}else{
		return false;
	}
}
function chkMassageSum($array){
	$count_1 = 0;
	$count_2 = 0;
	$count_3 = 0;
	$count_4 = 0;
	$count_5 = 0;
	$sum_1 = 0;
	$sum_2 = 0;
	$sum_3 = 0;
	$sum_4 = 0;
	$sum_5 = 0;
	$sql = "SELECT * FROM `order_massage` WHERE `order_inv` IN ($array)";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		if($result['massage_type'] == 0){
			$sum_1 = $sum_1+$result['total'];
			$count_1++;
		}else if($result['massage_type'] == 1){
			$sum_2 = $sum_2+$result['total'];
			$count_2++;
		}else if($result['massage_type'] == 2){
			$sum_3 = $sum_3+$result['total'];
			$count_3++;
		}else if($result['massage_type'] == 3){
			$sum_4 = $sum_4+$result['total'];
			$count_4++;
		}else if($result['massage_type'] == 4){
			$sum_5 = $sum_5+$result['total'];
			$count_5++;
		}
	}
	
	$sum = $sum_1+$sum_2+$sum_3+$sum_4+$sum_5;
	$sum = 'รายรับรวม '.$sum.' บาท';
	$msg_type1 = 'ค่าชั่วโมงพนักงานนวดจำนวน '.$count_1.'คน / ค่าใช้บริการ '.$sum_1.' บาท';
	$msg_type2 = 'นวดแผนไทยห้องรวมจำนวน '.$count_2.'คน / ค่าใช้บริการ '.$sum_2.' บาท';
	$msg_type3 = 'นวดแผนไทยห้อง VIPจำนวน  '.$count_3.'คน / ค่าใช้บริการ '.$sum_3.' บาท';
	$msg_type4 = 'นวดน้ำมัน / สปา จำนวน  '.$count_4.'คน / ค่าใช้บริการ '.$sum_4.' บาท';
	$msg_type5 = 'ค่าบริการห้อง VIP จำนวน  '.$count_5.'คน / ค่าใช้บริการ '.$sum_5.' บาท';
	return '<div><b>'.$sum.'</b></div><div>'.$msg_type1.'</div><div>'.$msg_type2.'</div><div>'.$msg_type3.'</div><div>'.$msg_type4.'</div><div>'.$msg_type5.'</div>';
}
function chkRestaurant($array){
	$sql = "SELECT COUNT(*) FROM `order_restaurant` WHERE `order_inv` IN ($array)";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	return $result['COUNT(*)'];
}
function chkSnookerSum($array){
	$count_normal = 0;
	$count_vip = 0;
	$count_challenge = 0;
	$sum_normal = 0;
	$sum_vip = 0;
	$sum_challenge = 0;
	$sql = "SELECT `times_in_min` AS `minutes`, `zone_id`, `total` FROM `order_snooker` WHERE `order_inv` IN ($array)";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		$is_vip = isVip($result['zone_id'], 9);
		if($is_vip == false){
			$count_normal = $count_normal+$result['minutes'];
			$sum_normal = $sum_normal+$result['total'];
		}else{
			if($result['zone_id']=='47' || $result['zone_id']=='48'){
				$count_challenge = $count_challenge+$result['minutes'];
				$sum_challenge = $sum_challenge+$result['total'];
			}else{
				$count_vip = $count_vip+$result['minutes'];
				$sum_vip = $sum_vip+$result['total'];
			}
		}
	}
	$hour = floor($count_normal/60);
	$min = $count_normal%60;
	$vipHour = floor($count_vip/60);
	$vipMin = $count_vip%60;
	$challengeHour = floor($count_challenge/60);
	$challengeMin = $count_challenge%60;
	$count_normal = $hour.'.'.$min;
	$count_vip = $vipHour.'.'.$vipMin;
	$count_challenge = $challengeHour.'.'.$challengeMin;
	$sum = $sum_normal+$sum_challenge+$sum_vip;
	$sum = 'รายรับรวม '.$sum.' บาท';
	$normal = 'ห้องรวมจำนวน '.$count_normal.' ชั่วโมง / ค่าใช้บริการ '.$sum_normal.' บาท';
	$vip = 'ห้อง VIP จำนวน '.$count_vip.' ชั่วโมง / ค่าใช้บริการ '.$sum_vip.' บาท';
	$challenge = 'ห้องแข่งขัน จำนวน '.$count_challenge.' ชั่วโมง / ค่าใช้บริการ '.$sum_challenge.' บาท';
	return '<div><b>'.$sum.'</b></div><div>'.$normal.'</div><div>'.$vip.'</div><div>'.$challenge.'</div>';
}
function chkMenuType($menu_id){
	$sql = "SELECT * FROM `menu` WHERE `id`='$menu_id'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	$type_by_cooking = $result['type_by_cooking'];
	return array(true, $result['menu_name_th']);
}

$date = $_GET['date'];
require_once("setPDF.php");

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false); 
$pdf->SetTitle('MONTHLY REPORT of '.$date);
$pdf->SetSubject('MONTHLY REPORT of '.$date);
$pdf->SetHeaderData('img-logo.png', '80', '', '');
$pdf->SetHeaderMargin('5');
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$pdf->SetMargins(PDF_MARGIN_LEFT, '30', PDF_MARGIN_RIGHT);

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
$pdf->setLanguageArray($l); 
$pdf->SetFont('freeserif', '', 10);

$sql = "SELECT SUM(`grand_total`) AS `gt`, `employee_id` AS `employee_id`, `inv_ref` FROM `invoice_bill` WHERE DATE_FORMAT(`checkout`, '%Y-%m')='$date'";
$query = mysql_query($sql);
while($result = mysql_fetch_assoc($query)){
	if($result['gt']>=0){
		$pdf->AddPage();
		$grand_total = 'รายรับรวม '.$result['gt'].'  บาท';

		$cash = 0;
		$credit = 0;
		$entertain = 0;
		$discount = 0;
		$array = '';
		$entertain_array = '';
		$table_details = '';
		$i = 0;
		$sql_2 = "SELECT `inv_ref`, `grand_total`, `payment`, `discount` FROM `invoice_bill` WHERE DATE_FORMAT(`checkout`, '%Y-%m')='$date'";
		$query_2 = mysql_query($sql_2);
		while($result_2 = mysql_fetch_assoc($query_2)){
			if($result_2['grand_total']>=0){
				if($result_2['payment'] == 1){
					$cash = $cash+$result_2['grand_total'];
				}elseif($result_2['payment'] == 2){
					$credit = $credit+$result_2['grand_total'];	
				}else{
					$entertain = $entertain+$result_2['grand_total'];
					if($entertain_array != ''){
						$entertain_array .= ',';
					}
					$entertain_array .= $result_2['inv_ref'];
				}
				if($i != 0){
					$array .= ',';
				}
				$array .= $result_2['inv_ref'];
				$discount = $discount+$result_2['discount'];
				$i++;
			}			
		}
		$grand_cash = 'เงินสด '.$cash.' บาท';
		$grand_credit = 'บัตรเครดิต '.$credit.' บาท';
		$grand_entertain = 'เอ็นเตอร์เทน '.$entertain.' บาท';
		$chkPayCash = chkPayCash($array);
		$after_pay_cash = $cash-$chkPayCash;
		$grand_pay_cash = 'ลูกค้าเบิกเงินสด '.$chkPayCash.' บาท';
		$grand_after_pay_cash = 'เงินสดคงเหลือ '. $after_pay_cash.' บาท';
		$grand_discount = 'ส่วนลดค่าอาหาร '.$discount.' บาท';


		$html = '<h1  style="font-size:60px">สรุปรายรับเดือน '.$date.' </h1>';
		$html .= '<table>';
		$html .= '<tr>';
		$html .= '<td width="50%">';
		$html .= '<div><b>'.$grand_total.'</b></div><div>'.$grand_cash.'</div><div>'.$grand_credit.'</div><div>'.$grand_entertain.'</div>';
		$html .= '</td>';
		$html .= '<td width="50%">';
		$html .= '<div></div><div>'.$grand_pay_cash.'</div><div><b>'.$grand_after_pay_cash.'</b></div><div>'.$grand_discount.'</div>';
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '</table>';

		$html .= '<br>';
		$html .= '<table border="1">';
		$html .= '<tr>';
		$html .= '<td width="50%" align="center">';
		$html .= '<b>ซาวน่า</b>';
		$html .= '</td>';
		$html .= '<td width="50%" align="center">';
		$html .= '<b>นวด</b>';
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td width="50%">';
		$html .= chkSauna($array);
		$html .= '</td>';
		$html .= '<td width="50%">';
		$html .= chkMassageSum($array);
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '</table>';

		$html .= '<br>';
		$html .= '<table border="1">';
		$html .= '<tr>';
		$html .= '<td width="50%" align="center">';
		$html .= '<b>สนุ๊กเกอร์</b>';
		$html .= '</td>';
		$html .= '<td width="50%" align="center">';
		$html .= '<b>อาหารและเครื่องดื่ม</b>';
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td width="50%">';
		$html .= chkSnookerSum($array);
		$html .= '</td>';
		$html .= '<td width="50%">';
		$chkRestaurant = chkRestaurant($array);
		$html .= chkOrderSum($array, $chkRestaurant);
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '</table>';

		$html .= '<br>';
		$html .= '<table border="1">';
		$html .= '<tr>';
		$html .= '<td width="50%" align="center">';
		$html .= '<b>สมาชิก</b>';
		$html .= '</td>';
		$html .= '<td width="50%" align="center">';
		$html .= '<b>คูปอง</b>';
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td width="50%">';
		$html .= chkMember($array);
		$html .= '</td>';
		$html .= '<td width="50%">';
		$html .= chkCoupon($array);
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '</table>';
/*
		$html .= '<br>';
		$html .= '<table border="1">';
		$html .= '<tr>';
		$html .= '<td width="30%" align="center">';
		$html .= '<b>เบิกเงินสด</b>';
		$html .= '</td>';
		$html .= '<td width="70%">';
		$html .= chkCash($array);
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '</table>';
*/
		$html .= '<br>';
		$html .= '<table border="1">';
		$html .= '<tr>';
		$html .= '<td width="30%" align="center">';
		$html .= '<b>คูปองส่วนลด</b>';
		$html .= '</td>';
		$html .= '<td width="70%">';
		$html .= chkDiscount($array);
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '</table>';

		if($entertain_array != ''){
			$html .= '<br>';
			$html .= '<table border="1">';
			$html .= '<tr>';
			$html .= '<td width="30%" align="center">';
			$html .= '<b>เอ็นเตอร์เทน</b>';
			$html .= '</td>';
			$html .= '<td width="70%">';
			$html .= chkEntertain($entertain_array);
			$html .= '</td>';
			$html .= '</tr>';
			$html .= '</table>';
		}

		$htmlcontent=stripslashes($html);
		$htmlcontent=AdjustHTML($htmlcontent);
		$pdf->writeHTML($htmlcontent, true, 0, true, 0);
	}
}

function getSum($date, $payment){
	$sql = "SELECT SUM(`grand_total`) AS `grand_total` FROM `invoice_bill` WHERE `payment`='$payment' AND DATE(`checkout`)='$date'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	if($result['grand_total'] > 0){
		return $result['grand_total'];
	}else{
		return '-';
	}
	
}

$pdf->AddPage();
$cash = 0;
$credit = 0;
$entertain = 0;
$html = '<table border="1"><tr><td width="20%" align="center">DATE</td><td width="20%" align="center">CASH (THB)</td><td width="20%" align="center">CREDIT (THB)</td><td width="20%" align="center">ENTERTAIN (THB)</td><td width="20%" align="center">TOTAL (THB)</td></tr>';

$sql = "SELECT SUM(`discount`) AS `discount`, DATE(`checkout`) AS `txt_date`, `grand_total` FROM `invoice_bill` WHERE DATE_FORMAT(`checkout`, '%Y-%m')='$date' GROUP BY DATE(`checkout`)";
$query = mysql_query($sql);
while($result = mysql_fetch_assoc($query)){
	if($result['grand_total']>=0){
		$cash = getSum($result['txt_date'],1);
		$credit = getSum($result['txt_date'],2);
		$entertain = getSum($result['txt_date'],3);
		$total = $cash+$credit+$entertain;
		$html .= '<tr><td width="20%">'.$result['txt_date'].'</td><td width="20%" align="right">'.$cash.'</td><td width="20%" align="right">'.$credit.'</td><td width="20%" align="right">'.$entertain.'</td><td width="20%" align="right">'.$total.'</td></tr>';
	}
}
$html .= '</table>';
$htmlcontent=stripslashes($html);
$htmlcontent=AdjustHTML($htmlcontent);
$pdf->writeHTML($htmlcontent, true, 0, true, 0);

$sql = "SELECT * FROM `zone_category` WHERE `id` BETWEEN 16 AND 18";
$query = mysql_query($sql);
while($result = mysql_fetch_assoc($query)){
	if($result['id']=='16'){
		$warranty = $result['charge'];
	}else if($result['id']=='17'){
		$msg = 	$result['charge'];
	}else if($result['id']=='18'){
		$spa = 	$result['charge'];
	}
}

$pdf->AddPage();
$massager = 0;
$massager_total = 0;
$total_html = 0;
$html = '<h1  style="font-size:60px">สรุปการเบิกเงินจ่ายพนักงานนวดก่อนวันที่ 15 </h1>';
$html .= '<table border="1"><tr><td width="50%">หมายเลข</td><td width="50%">เบิกจ่ายพนักงาน</td></tr>';
//$sql = "SELECT (`times_in_min`/60) AS `hours`, `massage_type`, `employee_id`, `start` FROM `order_massage` WHERE DATE_FORMAT(`start`, '%Y-%m')='$date' AND DATE_FORMAT(`start`, '%d')<16 AND `times_in_min`!='0' AND `massage_type`<'4' ORDER BY `employee_id`";
$sql = "SELECT * FROM `order_massage` WHERE DATE_FORMAT(`start`, '%Y-%m')='$date' AND DATE_FORMAT(`start`, '%d')<16 AND `times_in_min`!='0' AND `massage_type`<'4' GROUP BY `employee_id` ORDER BY `employee_id`";
$query = mysql_query($sql);
while($result = mysql_fetch_assoc($query)){
	$sql2 = "SELECT SUM(`times_in_min`/60) AS `hours`, IF(`massage_type`<'3','1','3') AS `type`, `employee_id`, `start` FROM `order_massage` WHERE DATE_FORMAT(`start`, '%Y-%m')='$date' AND DATE_FORMAT(`start`, '%d')<16 AND `times_in_min`!='0' AND `employee_id`='".$result['employee_id']."' GROUP BY `type`";
	$query2 = mysql_query($sql2);
	$num = mysql_num_rows($query2);
	$massager_total = 0;
	$k = 0;
	while($result2 = mysql_fetch_assoc($query2)){
		$chkMassage = chkMassage($result2['type']);
		if($result2['type']==3){
			$charge_per_hour = $spa;
		}else{
			$charge_per_hour = $msg;
		}
		$total_html = $total_html+($result2['hours']*$charge_per_hour);
		$massager_total = $massager_total+($result2['hours']*$charge_per_hour);
		if($num > 1 && $k > 0){
			$html .= '<tr><td width="50%">'.$result2['employee_id'].'</td><td width="50%">'.$massager_total.'</td></tr>';
		}else if($num == 1){
			$html .= '<tr><td width="50%">'.$result2['employee_id'].'</td><td width="50%">'.$massager_total.'</td></tr>';
		}
		$k++;
	}
}
$html .= '<tr><td width="50%" align="right">รวม</td><td width="50%">'.$total_html.'</td></tr>';

$html .= '</table>';
$htmlcontent=stripslashes($html);
$htmlcontent=AdjustHTML($htmlcontent);
$pdf->writeHTML($htmlcontent, true, 0, true, 0);


$pdf->AddPage();
$massager = 0;
$massager_total = 0;
$total_html2 = $total_html;
$total_html = 0;
$html = '<h1  style="font-size:60px">สรุปการเบิกเงินจ่ายพนักงานนวดหลังวันที่ 15 </h1>';
$html .= '<table border="1"><tr><td width="50%">หมายเลข</td><td width="50%">เบิกจ่ายพนักงาน</td></tr>';
$sql = "SELECT * FROM `order_massage` WHERE DATE_FORMAT(`start`, '%Y-%m')='$date' AND DATE_FORMAT(`start`, '%d')>15 AND `times_in_min`!='0' AND `massage_type`<'4' GROUP BY `employee_id` ORDER BY `employee_id`";
$query = mysql_query($sql);
while($result = mysql_fetch_assoc($query)){
	$sql2 = "SELECT SUM(`times_in_min`/60) AS `hours`, IF(`massage_type`<'3','1','3') AS `type`, `employee_id`, `start` FROM `order_massage` WHERE DATE_FORMAT(`start`, '%Y-%m')='$date' AND DATE_FORMAT(`start`, '%d')>15 AND `times_in_min`!='0' AND `employee_id`='".$result['employee_id']."' GROUP BY `type`";
	$query2 = mysql_query($sql2);
	$num = mysql_num_rows($query2);
	$massager_total = 0;
	$k = 0;
	while($result2 = mysql_fetch_assoc($query2)){
		$chkMassage = chkMassage($result2['type']);
		if($result2['type']==3){
			$charge_per_hour = $spa;
		}else{
			$charge_per_hour = $msg;
		}
		$total_html = $total_html+($result2['hours']*$charge_per_hour);
		$massager_total = $massager_total+($result2['hours']*$charge_per_hour);
		if($num > 1 && $k > 0){
			$html .= '<tr><td width="50%">'.$result2['employee_id'].'</td><td width="50%">'.$massager_total.'</td></tr>';
		}else if($num == 1){
			$html .= '<tr><td width="50%">'.$result2['employee_id'].'</td><td width="50%">'.$massager_total.'</td></tr>';
		}
		$k++;
	}
}
$total_html2 = $total_html2+$total_html;
$html .= '<tr><td width="50%" align="right">รวม</td><td width="50%">'.$total_html.'</td></tr>';
$html .= '<tr><td width="50%" align="right">รวมเดือนนี้</td><td width="50%">'.$total_html2.'</td></tr>';

$html .= '</table>';
$htmlcontent=stripslashes($html);
$htmlcontent=AdjustHTML($htmlcontent);
$pdf->writeHTML($htmlcontent, true, 0, true, 0);

ob_end_clean();

$pdf->Output('monthlyreport-'.$date.'.pdf', 'I');
?>