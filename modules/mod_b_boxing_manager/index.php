<?php
defined('R88PROJ') or die ($system_error);

$member_id		= $_POST['member_id'];
$type			= $_POST['type'];

function getAddForm($mobile = ''){
	$html = '<h3 class="title">สมัครสมาชิกใหม่</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">ชื่อ</span><input type="text" id="name" maxlength="50"></li>';
	$html .= '<li><span class="label">เบอร์มือถือ</span><input type="text" id="mobile" maxlength="10" value="'.$mobile.'"></li>';
	$html .= '<li><span class="label">ต้องการส่ง SMS</span><select id="enable_sms"><option value="0">ไม่ต้องการ</option><option value="1">ต้องการ</option></select></li>';
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="button" id="submit" value="สมัคร"> / <span id="cancel">ยกเลิก</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'view' => 'getAddForm',
		'html' => $html
	);
	returnJSON($json_arr);
}
function addNewMember($name, $mobile, $enable_sms){
	$checkMobile = checkMobile($mobile);
	if($checkMobile == false){
		$sql	= "INSERT INTO `ecoupon_customer` (`name`, `mobile`, `enable_sms`) VALUES ('$name', '$mobile', '$enable_sms')";
		$query	= mysql_query($sql);
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'msg' => 'สมัครสมาชิกเรียบร้อย'	
		);	
	}else{
		global $system_status_fail;
		$json_arr = array(
			'process' => $system_status_fail,
			'msg' => 'เบอร์มือถือนี้ถูกลงทะเบียนแล้ว'	
		);	
	}
	returnJSON($json_arr);	
}

function getEditForm($c_ecoupon){
	$sql	= "SELECT * FROM `ecoupon_customer` WHERE `id`='$c_ecoupon'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);

	$html = '<h3 class="title">แก้ไขข้อมูล</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">ชื่อ</span><input type="text" id="name" maxlength="50" value="'.$result['name'].'"></li>';
	$html .= '<li><span class="label">เบอร์มือถือ</span><input type="text" id="mobile" maxlength="10" value="'.$result['mobile'].'"></li>';
	$html .= '<li><span class="label">ต้องการส่ง SMS</span><select id="enable_sms">';
	if($result['enable_sms'] == 0){
		$html .= '<option value="0" selected>ไม่ต้องการ</option><option value="1">ต้องการ</option></select></li>';
	}else{
		$html .= '<option value="0">ไม่ต้องการ</option><option value="1" selected>ต้องการ</option></select></li>';
	}
	
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="hidden" id="member" value="'.$c_ecoupon.'"><input type="button" id="submit" value="บันทึก"> / <span id="cancel">ยกเลิก</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
function updateMember($member_id, $name, $mobile, $enable_sms){
	$sql	= "UPDATE `ecoupon_customer` SET `name`='$name', `mobile`='$mobile', `enable_sms`='$enable_sms' WHERE `id`='$member_id'";	
	$query	= mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'แก้ไขข้อมูลลูกค้าแล้ว'
	);
	returnJSON($json_arr);
}

//function checkMobile($mobile='', $search = ''){
function checkMobile($mobile){
	//$sql	= "SELECT * FROM `ecoupon_customer` WHERE `mobile`='$mobile' LIMIT 1";
	//if($search != ''){	
		$sql	= "SELECT * FROM `ecoupon_customer` WHERE `mobile`='$mobile' OR `name`='$mobile' LIMIT 1";
	//}
	
	$query	= mysql_query($sql);	
	$rows	= mysql_num_rows($query);
	if($rows == 0){		
		return false;	
	}else{
		return mysql_fetch_assoc($query);
	}
}
function myTicket($customer_id){
	$return = array();
	$sql	= "SELECT * FROM `ecoupon_customer_ticket` WHERE `customer_id`='$customer_id' ORDER BY `ecoupon_id`";
	$query	= mysql_query($sql);	
	while($rows = mysql_fetch_assoc($query)){
		$rows['ecouponDesc'] = ecouponDesc($rows['ecoupon_id']);
		$return[] = $rows;
	}
	return $return;
}
function myTicketDiscount($ecoupon_id){
	$sql	= "SELECT * FROM `ecoupon` WHERE `id`='$ecoupon_id'";
	$query	= mysql_query($sql);
	$rows = mysql_fetch_assoc($query);
	return $rows['discount'];
	/*while($rows = mysql_fetch_assoc($query)){
		$rows['ecouponDesc'] = ecouponDesc($rows['ecoupon_id']);
		$return[] = $rows;
	}
	return $return;*/
}
function ecouponDesc($id = 0){
	if($id > 0){
		$sql	= "SELECT * FROM `ecoupon` WHERE `id`='$id'";
		$query	= mysql_query($sql);	
		return mysql_fetch_assoc($query);
	}else{	
		$return = array();
		$sql	= "SELECT * FROM `ecoupon`";
		$query	= mysql_query($sql);	
		while($result = mysql_fetch_assoc($query)){
			$return[] = $result;
		}	
		return $return;
	}
}


function getZoneCate($id){
	$sql = "SELECT * FROM `zone_category` WHERE `id`='$id'";
	$query	=	mysql_query($sql);
	$return = mysql_fetch_assoc($query);
	return $return[0];
}
function getZoneList(){
	$sql = "SELECT * FROM `zone_category` WHERE `type` > 0";
	$query	=	mysql_query($sql);
	return mysql_fetch_assoc($query);
}

chkSession();
switch($type){
	case 'search':
		$mobile				= mysql_real_escape_string($_POST['mobile']);
		$checkMobile = checkMobile($mobile);
		if($checkMobile == false){
			getAddForm($mobile);
		}else{
			$myTicket = myTicket($checkMobile['id']);
	//var_dump($myTicket);
			global $system_status_success;
			$json_arr = array(
				'process' => $system_status_success,
				'customer' => $checkMobile,
				'myTicket' => $myTicket	
			);
			returnJSON($json_arr);
		}
		break;
	case 'edit':
		getEditForm($member_id);
		break;
	case 'addNewMember':
		$name				= mysql_real_escape_string($_POST['name']);
		$mobile				= mysql_real_escape_string($_POST['mobile']);
		$enable_sms			= mysql_real_escape_string($_POST['enable_sms']);
		if(trim($name)!='' && trim($mobile)!='') {
			addNewMember($name, $mobile, $enable_sms);
		} else {
			global $system_status_failed;
			$json_arr = array(
				'process' => $system_status_failed,
				'msg' => 'Firstname is empty'	
			);
		}
		
		break;
	case 'updateMember':
		$member_id			= mysql_real_escape_string($_POST['memberID']);
		$name				= mysql_real_escape_string($_POST['name']);
		$mobile				= mysql_real_escape_string($_POST['mobile']);
		$enable_sms			= mysql_real_escape_string($_POST['enable_sms']);
		updateMember($member_id, $name, $mobile, $enable_sms);
		break;
	case 'chkbill' :
		$customer_id		=	$_POST['customer_id'];
		$ecoupon_id			=	$_POST['ecoupon_id'];
		$name				=	$_POST['name'];
		$name				.=	' ซื้อ eCoupon';
		if($customer_id != '') {
			chkbill($ecoupon_id, $name,$customer_id);
		} else {
			global $system_status_failed;
			$json_arr = array(
				'process' => $system_status_failed,
				'msg' => 'Missing Variable'
			);
			returnJSON($json_arr);
		}
		break;
	case 'mycoupon':
		$mobile				= mysql_real_escape_string($_POST['mobile']);
		$checkMobile		= checkMobile($mobile);
		if($checkMobile != false){
			$myTicket = myTicket($checkMobile['id']);
			global $system_status_success;
			$json_arr = array(
				'process' => $system_status_success,
				'customer' => $checkMobile,
				'myTicket' => $myTicket	
			);
			returnJSON($json_arr);
		}
		break;
	case 'addTicket':
		$invoiceID		= $_POST['invoiceID'];
		$discountID		= $_POST['discountID'];
		addTicket($invoiceID, $discountID);
		break;
	case 'deleteTicket':
		$invoiceID		= $_POST['invoiceID'];
		$orderDiscount	= $_POST['orderDiscount'];
		deleteTicket($invoiceID, $orderDiscount);
		break;
	case 'ecouponList':
		$ecouponDesc = ecouponDesc(0);
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'ecoupon' => $ecouponDesc
		);
		returnJSON($json_arr);
		break;		
	default:
		$mobile			= mysql_real_escape_string($_POST['mobile']);
		$checkMobile	= checkMobile($mobile);
		listTicket($checkMobile['id']);
		break;
}

function addTicket($invoiceID, $discountID){
	$sql = "SELECT * FROM `ecoupon_customer_ticket` WHERE `id`='$discountID'";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);	

	$discount = myTicketDiscount($results['ecoupon_id']);

	$sql	= "INSERT INTO `order_discount` (order_inv, `ecoupon_id`,`price`,`total`, `order_person`) VALUES ('".$invoiceID."', '".$discountID."', '".$discount."', '".$discount."', '".accountDecrypt($_SESSION['user_id'])."')";
	$query	= mysql_query($sql);
}
function deleteTicket($invoiceID, $orderDiscount){
	$sql = "DELETE FROM `order_discount` WHERE `id`='$orderDiscount'";
	$query = mysql_query($sql);		
}
function listTicket($customer_id){
	$discount = array();	
//	$sql = "SELECT * FROM `ecoupon` WHERE `status` = '1'";

	$sql	= "SELECT *, `ecoupon_customer_ticket`.`id` AS `ticket_id`, `ecoupon_customer_ticket`.`qty` AS `qty_balance` FROM `ecoupon_customer_ticket` LEFT JOIN `ecoupon` ON `ecoupon_customer_ticket`.`ecoupon_id`=`ecoupon`.`id` WHERE `customer_id`='$customer_id' ORDER BY `ecoupon_id`";
	$query = mysql_query($sql);	
	while($results = mysql_fetch_assoc($query)) {		
		array_push($discount,array(
				'id' 				=>	$results['ticket_id'],
				'discount_name'		=>	$results['ecoupon_name'],
				'discount_price'	=>	$results['discount'],
				'discount_balance'	=>	$results['qty_balance']				
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

function chkbill($ecoupon_id, $name,$customer_id){
	$invID		=	createInv($name);
	$createOrder	=	createOrder($invID,$ecoupon_id,$customer_id);

	if($createOrder == true) {
		global $system_status_success;
			$json_arr = array(
				'process'	=>	$system_status_success,
				'change' 	=>	$change,
			);
		returnJSON($json_arr);
	} else {
		global $system_status_failed;
			$json_arr = array(
				'process' => $system_status_failed,
				'msg' => 'Some Process Wrong'
			);
		returnJSON($json_arr);
	}
}
function createOrder($invID,$ecoupon_id,$customer_id){
	//$orderLength	=	count($order);
	$sumPrice		=	0;
	$employee_id = accountDecrypt($_SESSION['user_id']);

	$ecoupon = ecouponDesc($ecoupon_id);

	$curDate = date("Y-m-d");
	$curTime = date("H:i:s");
	if($curTime < '07:00:00'){
		$lastDate = date ("Y-m-d", strtotime("-1day", strtotime($curDate)));
		$curTime = '23:59:59';
		$dateTime = $lastDate.' '.$curTime;
		//$sql = "INSERT INTO `order` (`order_inv`, `menu_id`, `unit`, `menu_desc`, `order_status`, `take_home`, `employee_id`, `start`) VALUES ('".$invoice_id."', '".$menu_id."', '".$order_unit."', '".$menu_desc."', '1', '".$take_home."', '".$user_id."', '$dateTime')";
		$sql	= "INSERT INTO `order_ecoupon` (`order_inv`, `coupon_id`, `unit`, `price`, `total`, `employee_id`, `start`, `customer_id`) VALUES ('".$invID."', '".$ecoupon_id."', '1', '".$ecoupon['price']."', '".$ecoupon['price']."', '".$employee_id."', '".$dateTime."', '".$customer_id."');";


	}else{
		$sql	= "INSERT INTO `order_ecoupon` (`order_inv`, `coupon_id`, `unit`, `price`, `total`, `employee_id`, `customer_id`) VALUES ('".$invID."', '".$ecoupon_id."', '1', '".$ecoupon['price']."', '".$ecoupon['price']."', '".$employee_id."', '".$customer_id."');";
	}
	//$sql	=	"INSERT INTO `order` (`order_inv`, `menu_id`, `unit`,  `menu_desc`, `order_status`, `take_home`, `price`, `employee_id`) VALUES ('".$invID."', '".$order[$i]['id']."', '".$order[$i]['number']."', '".$order[$i]['note']."', '1', '0', '".$order[$i]['price']."', '".$employee_id."')";
	$query	=	mysql_query($sql);
	return true;
}
function createInv($name){
	$sql	=	"INSERT INTO `invoice` (`zone`, `customer_value`, `zone_extra`) VALUES ('0','0','".$name."')";
	$query	=	mysql_query($sql);
	$invID	=	mysql_insert_id();
	return $invID;
}
?>