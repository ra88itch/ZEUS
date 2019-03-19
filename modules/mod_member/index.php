<?php
defined('R88PROJ') or die ($system_error);

$member_id		= $_POST['member_id'];
$type			= $_POST['type'];

function getAddForm(){
	$html = '<h3 class="title">สมัครสมาชิกใหม่</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">ชื่อ</span><input type="text" id="firstname" maxlength="20"></li>';
	$html .= '<li><span class="label">นามสกุล</span><input type="text" id="lastname" maxlength="30"></li>';
	$html .= '<li><span class="label">วันเกิด</span>วันที่ <input type="text" id="date" maxlength="2" value="" style="width:70px; margin-right:10px;" &nbsp;>เดือน <input type="text" id="month" maxlength="2" value="" style="width:70px; margin-right:10px;" &nbsp;>ปี <input type="text" id="year" maxlength="4" value="" style="width:70px; margin-right:10px;" &nbsp;></li>';
	$html .= '<li><span class="label">อีเมล์</span><input type="text" id="email" maxlength="50"></li>';
	$html .= '<li><span class="label">หมายเลขโทรศัพท์</span><input type="text" id="phone" maxlength="10"></li>';
	$html .= '<li><span class="label">ที่อยู่</span><input type="text" id="address"></li>';
	$html .= '<li><span class="label">ประเภทสมาชิก</span><select id="customer_type"><option value="1" selected>รายเดือน (ผู้ชาย)</option><option value="2">รายปี (ผู้ชาย)</option><option value="3">รายเดือน (ผู้หญิง)</option><option value="4">รายปี (ผู้หญิง)</option></select> / จำนวน <select id="unit"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option></select> ปีหรือเดือน</li>';
	$html .= '<li><span class="label">รหัสบัตร</span><input type="text" id="cardID"></li>';
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="hidden" id="status" value="1"><input type="button" id="submit" value="สมัคร"> / <span id="cancel">ยกเลิก</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
function getEditForm($member_id){
	$sql	= "SELECT * FROM `customer` WHERE `id`='$member_id'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);

	$html = '<h3 class="title">แก้ไขข้อมูลสมาชิก</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">ชื่อ</span><input type="text" value="'.$result['firstname'].'" id = "firstname" maxlength = "20"></li>';
	$html .= '<li><span class="label">นามสกุล</span><input type="text" value="'.$result['lastname'].'" id = "lastname" maxlength = "30"></li>';

	$dob = explode('-', $result['dob']);
	//<input type="text" id="year" maxlength="4">
	$html .= '<li><span class="label">วันเกิด</span>วันที่ <input type="text" id="date" maxlength="2" value="'.$dob[2].'" style="width:70px; margin-right:10px;" &nbsp;>เดือน <input type="text" id="month" maxlength="2" value="'.$dob[1].'" style="width:70px; margin-right:10px;" &nbsp;>ปี <input type="text" id="year" maxlength="4" value="'.($dob[0]+543).'" style="width:70px; margin-right:10px;" &nbsp;></li>';
	$html .= '<li><span class="label">อีเมลล์</span><input type="text" value = "'.$result['email'].'" id="email" maxlength = "50"></li>';
	$html .= '<li><span class="label">หมายเลขโทรศัพท์</span><input type="text" value = "'.$result['phone'].'" id="phone" maxlength="10"></li>';
	$html .= '<li><span class="label">ที่อยู่</span><input type="text" value = "'.$result['address'].'" id="address"></li>';	
	$html .= '<li><span class="label">รหัสบัตร</span><input type="text" value = "'.$result['CARDID'].'" id="cardID"></li>';
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="hidden" id="member" value="'.$member_id.'"><input type="button" id="submit" value="บันทึก"> / <span id="cancel">ยกเลิก</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);
}
function getExtra($zone_category, $unit){
	$sql = "SELECT `extra` FROM `zone_category` WHERE `id`='".$zone_category."'";
	$query = mysql_query($sql);	
	$extra = mysql_fetch_assoc($query);
	return $extra['extra']*$unit;
}
function addNewMember($firstname, $lastname, $dob, $email, $phone, $address, $active, $type, $cardID, $unit){
	if($type=='1'){
		$exp_date = date ("Y-m-d", strtotime("+".getExtra('4', $unit)." month -1day"));
	}elseif($type=='2'){
		$exp_date = date ("Y-m-d", strtotime("+".getExtra('5', $unit)." month -1day"));
	}elseif($type=='3'){
		$exp_date = date ("Y-m-d", strtotime("+".getExtra('19', $unit)." month -1day"));
	}elseif($type=='4'){
		$exp_date = date ("Y-m-d", strtotime("+".getExtra('20', $unit)." month -1day"));
	}
	$sql	= "INSERT INTO `customer` (`firstname`, `lastname`, `dob`, `email`, `phone`, `address`, `customer_type`, `active`, `CARDID`, `expire`) VALUES ('$firstname', '$lastname', '$dob', '$email', '$phone', '$address', '$type', '$active' ,'$cardID', '$exp_date')";
	$query	= mysql_query($sql);
	$memberID = mysql_insert_id();
	createInvoice($memberID,$type);
	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'สมัครสมาชิกเรียบร้อย'	
	);
	returnJSON($json_arr);	
}
function createInvoice($memberID,$type){
	if($type=='1'){
		$zone_key = 2001;
	}elseif($type=='2'){
		$zone_key = 2002;
	}elseif($type=='3'){
		$zone_key = 2027;
	}elseif($type=='4'){
		$zone_key = 2028;
	}
	$sql = "INSERT INTO `invoice` (`zone`,`customer_value`) VALUES ('".$zone_key."','1')";
	$query = mysql_query($sql);
	$invID = mysql_insert_id();
	//ORDER MEMBER
	$zone_price = chkZonePrice($zone_key);
	$order_sql = "INSERT INTO `order_member` (`order_inv`, `customer_id`, `price`, `total`, `order_person`, `zone_id`) VALUES ('".$invID."', '".$memberID."', '".$zone_price."', '".$zone_price."', '".$_SESSION['user_id']."', '".$zone_key."')";
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

function updateMember($member_id, $firstname, $lastname, $dob, $email, $phone, $address, $cardID){
	$sql	= "UPDATE `customer` SET `firstname`='$firstname', `lastname`='$lastname', `dob`='$dob', `email`='$email', `phone`='$phone', `address`='$address', `CARDID`='$cardID' WHERE `id`='$member_id'";	
	$query	= mysql_query($sql);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'msg' => 'แก้ไขข้อมูลลูกค้าแล้ว'
	);
	returnJSON($json_arr);
}
function changeMemberStatus($member_id){
	$sql = "UPDATE `customer` SET `active`=IF(`active`=1, 0, 1) WHERE `id`='$member_id'";
	$query	= mysql_query($sql);

	$sql = "SELECT `active` FROM `customer` WHERE `id`='$member_id'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);
	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'member' => $member_id,
		'active' => $result['active']
	);
	returnJSON($json_arr);
	
}
function uniqueUsername($username){
	$sql = "SELECT COUNT(*) FROM `account` WHERE `username`='$username'";
	$query	= mysql_query($sql);
	$result	= mysql_fetch_assoc($query);
	if($result['COUNT(*)'] > 0){
		return false;
	}else{
		return true;
	}
}

chkSession();
switch($type){
	case 'add':
		getAddForm();
		break;
	case 'changeMemberStatus':
		changeMemberStatus($member_id);
		break;
	case 'edit':
		getEditForm($member_id);
		break;
	case 'addNewMember':
		$firstname				= mysql_real_escape_string($_POST['firstname']);
		$lastname				= mysql_real_escape_string($_POST['lastname']);
		$dob					= mysql_real_escape_string($_POST['dob']);
		$email					= mysql_real_escape_string($_POST['email']);
		$phone					= mysql_real_escape_string($_POST['phone']);
		$address				= mysql_real_escape_string($_POST['address']);
		$active					= mysql_real_escape_string($_POST['status']);
		$type					= mysql_real_escape_string($_POST['cus_type']);		
		$unit					= mysql_real_escape_string($_POST['unit']);
		$cardID					= mysql_real_escape_string($_POST['cardID']);
		if(trim($firstname)!='') {
			addNewMember($firstname, $lastname, $dob, $email, $phone, $address, $active, $type, $cardID, $unit);
		} else {
			global $system_status_failed;
			$json_arr = array(
				'process' => $system_status_failed,
				'msg' => 'Firstname is empty'	
			);
		}
		
		break;
	case 'updateMember':
		$member_id				= mysql_real_escape_string($_POST['memberID']);
		$firstname				= mysql_real_escape_string($_POST['firstname']);
		$lastname				= mysql_real_escape_string($_POST['lastname']);
		$dob					= mysql_real_escape_string($_POST['dob']);
		$email					= mysql_real_escape_string($_POST['email']);
		$phone					= mysql_real_escape_string($_POST['phone']);
		$address				= mysql_real_escape_string($_POST['address']);
		$cardID					= mysql_real_escape_string($_POST['cardID']);
		updateMember($member_id, $firstname, $lastname, $dob, $email, $phone, $address, $cardID);
		break;
}
?>