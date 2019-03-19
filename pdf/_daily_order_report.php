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
	$sql = "SELECT `entertainer`.`entertainer_name`, `invoice_bill`.`inv_ref`, `invoice_bill`.`grand_total`, `invoice_bill`.`employee_id`, `invoice_bill`.`checkout`, `invoice_bill`.`realtimes` FROM `invoice_bill` LEFT JOIN `entertainer`
ON `entertainer`.`id` = `invoice_bill`.`entertainer` WHERE `invoice_bill`.`inv_ref` IN ($array) ORDER BY `invoice_bill`.`entertainer`";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		if($result['grand_total'] > 0){
			if($result['realtimes']!='0000-00-00 00:00:00'){
				$time = $result['realtimes'];
			}else{
				$time = $result['checkout'];
			}
			$html .= '<div><b>'.$result['entertainer_name'].'</b> - '.accountName($result['employee_id']).'('.$time.')</div>';

			$order = countOrder($result['inv_ref'], 'order');
			if($order > 0){
				$html .= '<div> - อาหารและเครื่องดื่ม '.$order.' บาท</div>';
			}
			$order = countOrder($result['inv_ref'], 'order_coupon');
			if($order > 0){
				$html .= '<div> - คูปอง '.$order.' บาท</div>';
			}
			$order = countOrder($result['inv_ref'], 'order_massage');
			if($order > 0){
				$html .= '<div> - นวด '.$order.' บาท</div>';
			}
			$order = countOrder($result['inv_ref'], 'order_member');
			if($order > 0){
				$html .= '<div> - สมาชิก '.$order.' บาท</div>';
			}
			$order = countOrder($result['inv_ref'], 'order_sauna');
			if($order > 0){
				$html .= '<div> - ซาวน่า '.$order.' บาท</div>';
			}
			$order = countOrder($result['inv_ref'], 'order_snooker');
			if($order > 0){
				$html .= '<div> - สนุ๊กเกอร์ '.$order.' บาท</div>';
			}
			$order = countOrder($result['inv_ref'], 'order_ecoupon');
			if($order > 0){
				$html .= '<div> - eCoupon '.$order.' บาท</div>';
			}
		}
	}	
	return $html;
}
function countOrder($inv_ref, $table){
	if($table=='order'){
		$sql = "SELECT SUM(`total`) AS `totals` FROM `$table` WHERE `order_inv`='$inv_ref' AND `order_status`='5'";
	}else{
		$sql = "SELECT SUM(`total`) AS `totals` FROM `$table` WHERE `order_inv`='$inv_ref'";
	}
	//return $sql;
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	return $result['totals'];
}
function chkLocker($array){
	$Mtotals = 0;
	$Ytotals = 0;
	$MCount = 0;
	$YCount = 0;
	$sql = "SELECT `zone_id`, SUM(`unit`) AS `units`, SUM(`total`) AS `totals` FROM `order_locker` WHERE `order_inv` IN ($array) GROUP BY `zone_id`";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		if($result['zone_id']=='2029'){
			$Mtotals = $Mtotals+$result['totals'];
			$MCount++;
		}else{
			$Ytotals = $Ytotals+$result['totals'];
			$YCount++;
		}
	}
	if($MCount==0 && $YCount==0){
		$html = '<div>ไม่มีรายการเช่าล๊อกเกอร์</div>';
	}else{
		//$html = 'รวม '.$Mtotals+$Ytotals.' บาท';
		$html = '<div>เช่าล๊อกเกอร์ รายเดือน จำนวน '.$MCount. ' / '.$Mtotals.' บาท</div>';
		$html .= '<div>เช่าล๊อกเกอร์ รายปี จำนวน '.$YCount. ' / '.$Ytotals.' บาท</div>';
	}
	return $html;
}
function chkDiscount($array){
	$details = '';
	$totals = 0;
	$sql = "SELECT `discount`.`name`, SUM(`order_discount`.`unit`) AS `units`, SUM(`order_discount`.`total`) AS `totals` FROM `order_discount` LEFT JOIN `discount`
ON `discount`.`id` = `order_discount`.`discount_id` WHERE `order_inv` IN ($array) AND `ecoupon_id`='0' GROUP BY `discount_id`";
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

// E COUPON
function chkeDiscount($array){
	$details = '';
	$totals = 0;
	$sql = "SELECT `ecoupon_customer_ticket`.`ecoupon_id` AS `ecid`, SUM(`order_discount`.`unit`) AS `units`, SUM(`order_discount`.`total`) AS `totals` FROM `order_discount` LEFT JOIN `ecoupon_customer_ticket`
ON `ecoupon_customer_ticket`.`id` = `order_discount`.`ecoupon_id` WHERE `order_inv` IN ($array) AND `order_discount`.`ecoupon_id`>'0' GROUP BY `ecid`";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		$totals = $totals+$result['totals'];
		$name = getEcouponFromTicket($result['ecid']);
		$details .= '<div>'.$name.' -  จำนวน '.$result['units'].' / รวม '.$result['totals'].' บาท</div>';
	}
	//$result = mysql_fetch_assoc($query);
	if($totals==0){
		$html = '<div>ไม่มีรายการส่วนลด eCoupon</div>';
	}else{
		$html = '<div><b>ส่วนลด eCoupon รวม '.$totals.' บาท</b></div>';
	}
	return '<div><b>eCoupon</b></div>'.$html.$details;
}
function getEcouponFromTicket($id){
	$sql = "SELECT `ecoupon_name` FROM `ecoupon` WHERE `id`='$id'";
	$query = mysql_query($sql);
	$return = mysql_fetch_assoc($query);
	return $return['ecoupon_name'];
}
function accountName($account_id){
	$sql = "SELECT * FROM `account` WHERE `id`='".$account_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['firstname'].' '.$results['lastname'];
}
function chkOrder($array){
	if($array!=''){
		$total = 0;
		$i = 1;
		$html = '<tr>';
		$sql = "SELECT SUM(`order`.`unit`) AS `unit`, `order`.`menu_id` AS `menu_id` , `total` FROM `order` JOIN `menu` ON `menu`.`id`=`order`.`menu_id` WHERE `order`.`order_inv` IN ($array) AND `order`.`order_status`='5' GROUP BY `order`.`menu_id` ORDER BY `menu`.`type_by_cooking` ASC";
		$query = mysql_query($sql);
		if(mysql_num_rows($query)>0){
			while($result = mysql_fetch_assoc($query)){
				$total = $total + $result['total'];
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
		}else{
			$html .= '<td style="width:50%;"></td><td style="width:50%;"></td></tr>';
		}
		$html .= '<tr><td style="width:50%;">รายรับรวม</td><td style="width:50%;">'.$total.'</td></tr>';
		
		return $html;
	}else{
		return '<tr><td style="width:50%;"></td><td style="width:50%;"></td></tr>';
	}
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
	//$html .= '</div><div>';
	//$html .= 'มีผู้ใช้บริการจำนวน '.$chkRestaurant.' โต๊ะ';
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
	$sql = "SELECT * FROM `order_cash` WHERE `order_inv` IN (".$array.")";
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
		}
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
				$female_m ++;
				$female_value_m = $female_value_m+$result['total'];
				break;
			case 2028:
				$female_y ++;
				$female_value_y = $female_value_y+$result['total'];
				break;
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
function chkeCoupon($array){
	$details = '';
	$totals = 0;
	$sql = "SELECT `ecoupon`.`ecoupon_name`, SUM(`order_ecoupon`.`unit`) AS `units`, SUM(`order_ecoupon`.`total`) AS `totals` FROM `order_ecoupon` LEFT JOIN `ecoupon`
ON `ecoupon`.`id` = `order_ecoupon`.`coupon_id` WHERE `order_inv` IN ($array) GROUP BY `coupon_id`";
	$query = mysql_query($sql);
	while($result = mysql_fetch_assoc($query)){
		$totals = $totals+$result['totals'];
		$details .= '<div>'.$result['ecoupon_name'].' -  จำนวน '.$result['units'].' / รวม '.$result['totals'].' บาท</div>';
	}
	//$result = mysql_fetch_assoc($query);
	if($totals==0){
		$html = '<div>ไม่มีรายการขาย eCoupon</div>';
	}else{
		$html = '<div><b>รายรับรวม '.$totals.' บาท</b></div>';
	}
	return '<div><b>eCoupon</b></div>'.$html.$details;
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
			if($result['zone_id'] >= 1031 && $result['zone_id'] <= 1050){
				$count_fitt++;
			}else if($result['zone_id'] >= 1051 && $result['zone_id'] <= 1550){
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
	$fitt = ' - พนักงานฟิตเนส จำนวน'.$count_fitt;
	$sauna = ' - ซาวน่า จำนวน '.$count_sauna;
	$sauna_fitt = ' - ซาวน่าและฟิตเนส จำนวน '.$count_sauna_fitt;
	if($count_coupon > 0){
		$coupon = 'มีผู้ใช้คูปอง จำนวน '.$count_coupon;
	}else{
		$coupon = '';
	}
	return '<div><b>'.$sum.'</b></div><div>'.$member_male.'</div><div>'.$member_female.'</div><div>'.$daily.'</div><div>'.$sauna.'</div><div>'.$sauna_fitt.'</div><div>'.$fitt.'</div><div>'.$coupon.'</div>';
}
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

//$date = '2015-08-01';
$date = $_GET['date'];
require_once("setPDF.php");

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false); 
$pdf->SetTitle('DAILY REPORT of '.$date);
$pdf->SetSubject('DAILY REPORT of '.$date);
$pdf->SetHeaderData('img-logo.png', '80', '', '');
$pdf->SetHeaderMargin('5');
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$pdf->SetMargins(PDF_MARGIN_LEFT, '30', PDF_MARGIN_RIGHT);

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
$pdf->setLanguageArray($l); 
$pdf->SetFont('freeserif', '', 10);

$sql = "SELECT SUM(`grand_total`) AS `gt`, `employee_id` AS `employee_id`, `inv_ref` FROM `invoice_bill` WHERE DATE(`checkout`)='$date'";
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
		$i = 0;
		$sql_2 = "SELECT `inv_ref`, `grand_total`, `payment`, `discount` FROM `invoice_bill` WHERE DATE(`checkout`)='$date'";
		$query_2 = mysql_query($sql_2);
		while($result_2 = mysql_fetch_assoc($query_2)){
			if($result_2['grand_total']>=0){
				if($result_2['payment'] == 1){
					$cash = $cash+$result_2['grand_total'];		
				}elseif($result_2['payment'] == 2){
					$credit = $credit+$result_2['grand_total'];	
				}elseif($result_2['payment'] == 3){
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

		$html = '<table border="1">';
		$html .= '<tr>';
		$html .= '<td width="100%" align="center">';
		$html .= '<b>อาหารและเครื่องดื่ม</b>';
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td width="100%">';
		$chkRestaurant = chkRestaurant($array);
		$html .= chkOrderSum($array, $chkRestaurant);
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '</table>';

		$htmlcontent=stripslashes($html);
		$htmlcontent=AdjustHTML($htmlcontent);
		$pdf->writeHTML($htmlcontent, true, 0, true, 0);
	}
}

// DEPT CASH
$pdf->AddPage();
$array = '';
$i = 0;
$html = '<h1  style="font-size:60px">รายการยอดขายลูกค้าเงินสด วันที่  '.$date.' </h1>';
$sql = "SELECT `grand_total`, `inv_ref` FROM `invoice_bill` 
INNER JOIN `invoice` ON `invoice_bill`.`inv_ref`=`invoice`.`id`
WHERE DATE(`invoice_bill`.`checkout`)='$date' AND `invoice`.`zone`='0'";
$query = mysql_query($sql);
while($result_2 = mysql_fetch_assoc($query)){
	if($result_2['grand_total']>=0){
		if($i != 0){
			$array .= ',';
		}
		$array .= $result_2['inv_ref'];
		$i++;
	}			
}
$html .= '<table border="1"><tr><td width="50%">รายการ</td><td width="50%">จำนวน</td></tr>';
$html .= chkOrder($array);
$html .= '</table>';
$htmlcontent=stripslashes($html);
$htmlcontent=AdjustHTML($htmlcontent);
$pdf->writeHTML($htmlcontent, true, 0, true, 0);

// DEPT MASSAGE
$pdf->AddPage();
$array = '';
$i = 0;
$html = '<h1  style="font-size:60px">รายการยอดขายแผนกนวด วันที่  '.$date.' </h1>';
$sql = "SELECT `grand_total`, `inv_ref` FROM `invoice_bill` 
INNER JOIN `invoice` ON `invoice_bill`.`inv_ref`=`invoice`.`id` 
INNER JOIN `zone` ON `invoice`.`zone`=`zone`.`id` 
INNER JOIN `zone_category` ON `zone`.`zone_category`=`zone_category`.`id` 
WHERE DATE(`invoice_bill`.`checkout`)='$date' AND `zone_category`.`type`='1'";
$query = mysql_query($sql);
while($result_2 = mysql_fetch_assoc($query)){
	if($result_2['grand_total']>=0){
		if($i != 0){
			$array .= ',';
		}
		$array .= $result_2['inv_ref'];
		$i++;
	}			
}
$html .= '<table border="1"><tr><td width="50%">รายการ</td><td width="50%">จำนวน</td></tr>';
$html .= chkOrder($array);
$html .= '</table>';
$htmlcontent=stripslashes($html);
$htmlcontent=AdjustHTML($htmlcontent);
$pdf->writeHTML($htmlcontent, true, 0, true, 0);

// DEPT FITNESS
$pdf->AddPage();
$array = '';
$i = 0;
$html = '<h1  style="font-size:60px">รายการยอดขายแผนกซาวน์น่า+ฟิตเนส วันที่  '.$date.' </h1>';
$sql = "SELECT `grand_total`, `inv_ref` FROM `invoice_bill` 
INNER JOIN `invoice` ON `invoice_bill`.`inv_ref`=`invoice`.`id` 
INNER JOIN `zone` ON `invoice`.`zone`=`zone`.`id` 
INNER JOIN `zone_category` ON `zone`.`zone_category`=`zone_category`.`id` 
WHERE DATE(`invoice_bill`.`checkout`)='$date' AND `zone_category`.`type`='2'";
$query = mysql_query($sql);
while($result_2 = mysql_fetch_assoc($query)){
	if($result_2['grand_total']>=0){
		if($i != 0){
			$array .= ',';
		}
		$array .= $result_2['inv_ref'];
		$i++;
	}			
}
$html .= '<table border="1"><tr><td width="50%">รายการ</td><td width="50%">จำนวน</td></tr>';
$html .= chkOrder($array);
$html .= '</table>';
$htmlcontent=stripslashes($html);
$htmlcontent=AdjustHTML($htmlcontent);
$pdf->writeHTML($htmlcontent, true, 0, true, 0);

// DEPT RESTAURANT
$pdf->AddPage();
$array = '';
$i = 0;
$html = '<h1  style="font-size:60px">รายการยอดขายแผนกห้องอาหาร วันที่  '.$date.' </h1>';
$sql = "SELECT `grand_total`, `inv_ref` FROM `invoice_bill` 
INNER JOIN `invoice` ON `invoice_bill`.`inv_ref`=`invoice`.`id` 
INNER JOIN `zone` ON `invoice`.`zone`=`zone`.`id` 
INNER JOIN `zone_category` ON `zone`.`zone_category`=`zone_category`.`id` 
WHERE DATE(`invoice_bill`.`checkout`)='$date' AND `zone_category`.`type`='3'";
$query = mysql_query($sql);
while($result_2 = mysql_fetch_assoc($query)){
	if($result_2['grand_total']>=0){
		if($i != 0){
			$array .= ',';
		}
		$array .= $result_2['inv_ref'];
		$i++;
	}			
}
$html .= '<table border="1"><tr><td width="50%">รายการ</td><td width="50%">จำนวน</td></tr>';
$html .= chkOrder($array);
$html .= '</table>';
$htmlcontent=stripslashes($html);
$htmlcontent=AdjustHTML($htmlcontent);
$pdf->writeHTML($htmlcontent, true, 0, true, 0);

// DEPT SNOOKER
$pdf->AddPage();
$array = '';
$i = 0;
$html = '<h1  style="font-size:60px">รายการยอดขายแผนกสนุ๊กเกอร์ วันที่  '.$date.' </h1>';
$sql = "SELECT `grand_total`, `inv_ref` FROM `invoice_bill` 
INNER JOIN `invoice` ON `invoice_bill`.`inv_ref`=`invoice`.`id` 
INNER JOIN `zone` ON `invoice`.`zone`=`zone`.`id` 
INNER JOIN `zone_category` ON `zone`.`zone_category`=`zone_category`.`id` 
WHERE DATE(`invoice_bill`.`checkout`)='$date' AND `zone_category`.`type`='4'";
$query = mysql_query($sql);
while($result_2 = mysql_fetch_assoc($query)){
	if($result_2['grand_total']>=0){
		if($i != 0){
			$array .= ',';
		}
		$array .= $result_2['inv_ref'];
		$i++;
	}			
}
$html .= '<table border="1"><tr><td width="50%">รายการ</td><td width="50%">จำนวน</td></tr>';
$html .= chkOrder($array);
$html .= '</table>';
$htmlcontent=stripslashes($html);
$htmlcontent=AdjustHTML($htmlcontent);
$pdf->writeHTML($htmlcontent, true, 0, true, 0);



ob_end_clean();

$pdf->Output('dailyOrderReport-'.$date.'.pdf', 'I');
?>