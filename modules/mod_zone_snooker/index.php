<?php
defined('R88PROJ') or die ($system_error);

$type			=	$_POST['type'];

function setExtra($extra, $zone){
	$sql = "UPDATE `zone` SET `extra`='$extra' WHERE `id`='".$zone."'";
	$query = mysql_query($sql);	
	return true;
}
function getControlForm($zone){
	$html = '<h3 class="title">แก้ไขการสั่งงานตู้ Control</h3>';
	$html .= '<ul class="form">';
	$html .= '<li><span class="label">Control Switch</span><select id="extra">';
	for($i=1; $i < 16; $i++){
		$html .= '<option value="'.$i.'">สวิทซ์หมายเลข '.$i.'</option>';
	}
	$html .= '</select></li>';
	$html .= '</ul>';
	$html .= '<div class="submit"><input type="text" id="zone" value="'.$zone.'"><input type="button" id="submit" value="SAVE"> or <span id="cancel">Cancel</span></div>';

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success,
		'html' => $html
	);
	returnJSON($json_arr);

}
function changeStatus($zone){
	if($account > 1){
		$sql = "UPDATE `zone` SET `active`=IF(`active`=1, 0, 1) WHERE `id`='$zone'";
		$query	= mysql_query($sql);

		$sql = "SELECT `active` FROM `zone` WHERE `id`='$zone'";
		$query	= mysql_query($sql);
		$result	= mysql_fetch_assoc($query);
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'zone' => $zone,
			'active' => $result['active']
		);
	}else{
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'zone' => $zone,
			'active' => 1
		);
	}
	returnJSON($json_arr);	
}
chkSession();
switch($type){
	case 'getControlForm':
		$zone =  mysql_real_escape_string($_REQUEST['zone']);
		getControlForm($zone);
		break;
	case 'setControl':
		$extra =  mysql_real_escape_string($_REQUEST['extra']);
		$zone =  mysql_real_escape_string($_REQUEST['zone']);
		setExtra($extra, $zone);
		break;
	case 'changeStatus':
		$zone =  mysql_real_escape_string($_REQUEST['zone']);
		changeStatus($account_id);
		break;
}
?>