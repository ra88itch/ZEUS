<?php
defined('R88PROJ') or die ($system_error);

$locker_id		= $_POST['locker_id'];
$type			= $_POST['type'];

function getAddForm(){
	$html = '<h3 class="title">สมัครบริการตู้ล๊อกเกอร์</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">ชื่อ</span><input type="text" id="firstname" maxlength="20"></li>';
	$html .= '<li><span class="label">นามสกุล</span><input type="text" id="lastname" maxlength="30"></li>';
	$html .= '<li><span class="label">หมายเลขโทรศัพท์</span><input type="text" id="phone" maxlength="10"></li>';
	$html .= '<li><span class="label">ที่อยู่</span><input type="text" id="address"></li>';
	$html .= '<li><span class="label">ประเภทสมาชิก</span><select id="locker_type"><option value="1" selected>รายเดือน</option><option value="2">รายปี</option></select></li>';
	$html .= '<li><span class="label">เบอร์ตู้ล๊อกเกอร์</span><input type="text" id="locker_no"></li>';
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="hidden" id="status" value="1"><input type="button" id="submit" value="สมัคร"> / <span id="cancel">ยกเลิก</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
function getEditForm($locker_id){
	$sql	= "SELECT * FROM `locker` WHERE `id`='$locker_id'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);

	$html = '<h3 class="title">แก้ไขข้อมูลสมาชิก</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">ชื่อ</span><input type="text" value="'.$result['firstname'].'" id = "firstname" maxlength = "20"></li>';
	$html .= '<li><span class="label">นามสกุล</span><input type="text" value="'.$result['lastname'].'" id = "lastname" maxlength = "30"></li>';
	$html .= '<li><span class="label">หมายเลขโทรศัพท์</span><input type="text" value = "'.$result['phone'].'" id="phone" maxlength="10"></li>';
	$html .= '<li><span class="label">ที่อยู่</span><input type="text" value = "'.$result['address'].'" id="address"></li>';	
	$html .= '<li><span class="label">เบอร์ตู้ล๊อกเกอร์</span><input type="text" value = "'.$result['locker_no'].'" id="locker_no"></li>';
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="hidden" id="locker_id" value="'.$locker_id.'"><input type="button" id="submit" value="บันทึก"> / <span id="cancel">ยกเลิก</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
function getExtra($zone_category){
	$sql = "SELECT `extra` FROM `zone_category` WHERE `id`='".$zone_category."'";
	$query = mysql_query($sql);	
	$extra = mysql_fetch_assoc($query);
	return $extra['extra'];
}
function addNewMember($firstname, $lastname, $phone, $address, $locker_type, $locker_no){
	if($locker_type=='1'){
		$exp_date = date ("Y-m-d", strtotime("+".getExtra('21')." month -1day"));
	}elseif($locker_type=='2'){
		$exp_date = date ("Y-m-d", strtotime("+".getExtra('22')." month -1day"));
	}
	$sql	= "INSERT INTO `locker` (`firstname`, `lastname`, `phone`, `address`, `locker_type`, `locker_no`, `active`, `expire`) VALUES ('$firstname', '$lastname', '$phone', '$address', '$locker_type', '$locker_no', '1', '$exp_date')";
	$query	= mysql_query($sql);
	$memberID = mysql_insert_id();
	createInvoice($memberID,$locker_type);
	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'ดำเนินการเรียบร้อย'	
	);
	returnJSON($json_arr);	
}
function createInvoice($memberID,$locker_type){
	if($locker_type=='1'){
		$zone_key = 2029;
	}elseif($locker_type=='2'){
		$zone_key = 2030;
	}
	$sql = "INSERT INTO `invoice` (`zone`,`customer_value`) VALUES ('".$zone_key."','1')";
	$query = mysql_query($sql);
	$invID = mysql_insert_id();
	//ORDER LOCKER
	$zone_price = chkZonePrice($zone_key);
	$order_sql = "INSERT INTO `order_locker` (`order_inv`, `customer_id`, `price`, `total`, `order_person`, `zone_id`) VALUES ('".$invID."', '".$memberID."', '".$zone_price."', '".$zone_price."', '".accountDecrypt($_SESSION['user_id'])."', '".$zone_key."')";
	$order_query = mysql_query($order_sql);
}

function chkZonePrice($zone_id){
	$zone_cate_sql = "SELECT `zone_category` FROM `zone` WHERE `id`='".$zone_id."'";
	$zone_cate_query = mysql_query($zone_cate_sql);
	$cate_result = mysql_fetch_assoc($zone_cate_query);
	
	$zone_charge_sql = "SELECT `charge` FROM `zone_category` WHERE `id`='".$cate_result['zone_category']."'";
	$zone_charge_query = mysql_query($zone_charge_sql);
	$charge_result = mysql_fetch_assoc($zone_charge_query);
	return $charge_result['charge'];
}

function updateMember($firstname, $lastname, $phone, $address, $locker_no, $locker_id){
	$sql	= "UPDATE `locker` SET `firstname`='$firstname', `lastname`='$lastname', `phone`='$phone', `address`='$address', `locker_no`='$locker_no' WHERE `id`='$locker_id'";	
	$query	= mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'แก้ไขข้อมูลลูกค้าแล้ว'
	);
	returnJSON($json_arr);
}
function changeMemberStatus($locker_id){
	$sql = "UPDATE `locker` SET `active`=IF(`active`=1, 0, 1) WHERE `id`='$locker_id'";
	$query	= mysql_query($sql);

	$sql = "SELECT `active` FROM `locker` WHERE `id`='$locker_id'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);
	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'locker' => $locker_id,
		'active' => $result['active']
	);
	returnJSON($json_arr);
}
function renewLocker($locker_id, $start_date){	
	$sql = "SELECT * FROM `locker` WHERE `id`='$locker_id'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);

	createInvoiceRenew($result['id'],$result['locker_type'],$start_date);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'ต่ออายุแล้ว'
	);
	returnJSON($json_arr);
}
function createInvoiceRenew($memberID,$locker_type,$expire){
	if($locker_type=='1'){
		$zone_key = 2029;
	}elseif($locker_type=='2'){
		$zone_key = 2030;
	}
	$sql = "INSERT INTO `invoice` (`zone`,`customer_value`) VALUES ('".$zone_key."','1')";
	$query = mysql_query($sql);
	$invID = mysql_insert_id();
	$zone_price = chkZonePrice($zone_key);

	$now = time(); // or your date as well
	$your_date = strtotime($expire);
	//$datediff = $now - $your_date;
	//$back_date = floor($datediff / (60 * 60 * 24));
	//$unit = ceil($back_date/30);
	//$total = $zone_price*$unit;
	
	$order_sql = "INSERT INTO `order_locker` (`order_inv`, `customer_id`, `price`, `total`, `order_person`, `zone_id`, `unit`, `renew`) VALUES ('".$invID."', '".$memberID."', '".$zone_price."', '".$zone_price."', '".accountDecrypt($_SESSION['user_id'])."', '".$zone_key."', '1', '1')";
	$order_query = mysql_query($order_sql);

	$date = date_format(date_create($expire), 'Y-m-d');
	/*if($locker_type=='1'){		
		$days = 30 * $unit;
		$exp_date = date ( 'Y-m-d', strtotime("+".$days." days",strtotime($expire)));
	}elseif($locker_type=='2'){	
		$days = 365 * $unit;
		$exp_date = date ( 'Y-m-d', strtotime("+".$days." days", strtotime($expire)));
	}*/
	if($locker_type=='1'){
		$exp_date = date ("Y-m-d", strtotime($date . "+".getExtra('21')." month -1day"));
	}elseif($locker_type=='2'){
		$exp_date = date ("Y-m-d", strtotime($date . "+".getExtra('22')." month -1day"));
	}
	
	$sql = "UPDATE `locker` SET `expire`='$exp_date' WHERE `id`='$memberID'";
	$query	= mysql_query($sql);
}

function renewLockerForm($locker_id){
	$sql	= "SELECT * FROM `locker` WHERE `id`='$locker_id'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);

	$html = '<h3 class="title">ต่ออายุสมาชิก</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">ชื่อ</span>'.$result['firstname'].'</li>';
	$html .= '<li><span class="label">นามสกุล</span>'.$result['lastname'].'</li>';
	$html .= '<li><span class="label">เบอร์ตู้ล๊อกเกอร์</span><input type="text" value = "'.$result['locker_no'].'" id="locker_no" disabled></li>';
	$html .= '<li><span class="label">วันที่เริ่มคิดค่าบริการย้อนหลัง<input type="text" value = "'.date('Y-m-d',strtotime($result['expire'])).'" id="expire"></span>';
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="hidden" id="locker_id" value="'.$locker_id.'"><input type="button" id="submit" value="บันทึก"> / <span id="cancel">ยกเลิก</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
chkSession();
switch($type){
	case 'add':
		getAddForm();
		break;
	case 'changeMemberStatus':
		changeMemberStatus($locker_id);
		break;
	case 'edit':
		getEditForm($locker_id);
		break;
	case 'addNewMember':
		$firstname				= mysql_real_escape_string($_POST['firstname']);
		$lastname				= mysql_real_escape_string($_POST['lastname']);
		$phone					= mysql_real_escape_string($_POST['phone']);
		$address				= mysql_real_escape_string($_POST['address']);
		$locker_type			= mysql_real_escape_string($_POST['locker_type']);		
		$locker_no				= mysql_real_escape_string($_POST['locker_no']);
		addNewMember($firstname, $lastname, $phone, $address, $locker_type, $locker_no);
		break;
	case 'updateMember':
		$firstname				= mysql_real_escape_string($_POST['firstname']);
		$lastname				= mysql_real_escape_string($_POST['lastname']);
		$phone					= mysql_real_escape_string($_POST['phone']);
		$address				= mysql_real_escape_string($_POST['address']);	
		$locker_no				= mysql_real_escape_string($_POST['locker_no']);
		updateMember($firstname, $lastname, $phone, $address, $locker_no, $locker_id);
		break;
	case 'renewLocker':
		renewLockerForm($locker_id);
		break;
	case 'renewLockerSubmit':
		$start_date				= mysql_real_escape_string($_POST['start_date']);
		renewLocker($locker_id, $start_date);
		break;
}
?>