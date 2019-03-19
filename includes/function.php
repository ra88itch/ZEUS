<?php
defined('R88PROJ') or die($system_error);

function returnJSON($json_arr){
	global $conn;
	echo json_encode( $json_arr );
	mysql_close( $conn );
	exit;
}

function chkAccessDevice($ipAddress){
	$ipAddress = mysql_real_escape_string($ipAddress);
	$sql = "SELECT COUNT(*) AS `count` FROM `allow_device` WHERE `ip_address`='$ipAddress' AND `active`='1'";
	$query = mysql_query($sql);
	$results = mysql_fetch_assoc($query);
	if($results['count']==0){
		global $system_error_block_device;
		die($system_error_block_device);
	}
	return true;
}

function accountEncrypt( $id ){	
	$non_str = base_convert( $id, 10, 9);
	$non_str = strrev( (string)$non_str );
	$ret = array();
	$ch = "";	
	for( $i = 0; $i < strlen( $non_str ); $i++ ){
		$ch = substr( $non_str, $i, 1);
		if( $ch== '0' )$ch='9';
		$ret[ $i ] = $ch;
	}
	$enc_id = join( '', $ret );
	if( strlen( $enc_id ) < 6 ) $enc_id = (int)$enc_id * 130;
	return (int)$enc_id;
}

function accountDecrypt( $enc_id ){
	$enc_str = (string)$enc_id;
	$len = strlen( $enc_str );
	if( substr( $enc_str, ($len-1), 1 ) == '0') 	{
		$enc_str = (string)( $enc_id / 130 ); 
		$len = strlen( $enc_str ); 
	}

	$ret = '';
	$ch = '';
	for( $i=0;$i<$len;$i++){
		$ch = substr( $enc_str, $i, 1);
		if( $ch == '9' ) $ch='0';
		$ret .= $ch;
	}
	$ret = strrev( $ret );
	$unenc_id = base_convert( $ret, 9, 10); 
	return (int)$unenc_id;
}
function chkSession(){
	global $system_status_success;
	if($_SESSION['login'] != $system_status_success){
		global $system_error_login;
		die($system_error_login);
	}
	return true;
}
function chkPermission($type){
	switch($type){
		case 'root':
			$chk = '0';
			break;

		case 'admin':
			$chk = '1';
			break;
	}
	if($_SESSION['type'] > $chk){
		return false;
	}
	return true;
}
function cutText($text, $length){
	$new_text = substr($text, 0, $length);
	if(strlen($new_text) < strlen($text)){
		$new_text .= '...';
	}
	return $new_text;
}
function getMenuName($menu_id){
	$sql = "SELECT `menu_name_th` FROM `menu` WHERE `id`='$menu_id'";
	$query = mysql_query($sql);
	$results = mysql_fetch_assoc($query);
	return $results['menu_name_th'];
}
function addLog($text, $invoice){
	$sql = "INSERT `log` (`invoice`, `log`, `account_id`) VALUES ('".$invoice."', '".$text."', '".accountDecrypt($_SESSION['user_id'])."')";
	$query = mysql_query($sql);
}
function updateMenuPrice(){
	$html = '';
	$sql = "SELECT * FROM `menu` WHERE `future_date`!='0000-00-00' AND DATE(`future_date`)<=CURDATE() AND `future_price`!='0'";
	$html .= '1<br>';
	$query = mysql_query($sql);
	$html .= '2<br>';
	while($results = mysql_fetch_assoc($query)){
		$html .= '4<br>';
		$curDate = date("Y-m-d");
		$curTime = date("Y-m-d H:i:s");
		if($curTime > ($curDate.' 07:00:00')){
			setNewPrice($results['id'], $results['future_price']);
		}
		$html .= $results['id'].' - '. $results['future_price'].'<br>';
	}	
	$html .= '3<br>';
	//return $html;
}
function setNewPrice($id, $future_price){
	$order_sql = "UPDATE `menu` SET `price`='".$future_price."', `future_date`='0000-00-00', `future_price`='0' WHERE `id`='".$id."'";
	$order_query = mysql_query($order_sql);	
}
?>