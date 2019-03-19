<?php
defined('R88PROJ') or die ($system_error);

$type			=	$_POST['type'];

function finishSnook($zone_id){
	include('LXTelnet.php');		
	$lx = new LXTelnet();
	if($lx->init() === false){ return; }
	if($lx->login() === false){ return; }
	$cmd = getCmdByZone($zone_id);
	$lx->control($cmd);
	$lx->close();

	resetZoneReserved($zone_id);

	global $system_status_success;
	$json_arr = array(
		'process' => $system_status_success
	);
	returnJSON($json_arr);
}
function resetZoneReserved($zone_id){
	$sql = "UPDATE `zone` SET `reserved`='0' WHERE `id`='".$zone_id."'";
	$query = mysql_query($sql);	
}
function getCmdByZone($zone){
	$cmd_closes	= array('$00W01F#17', '$00W02F#14', '$00W03F#15', '$00W04F#12', '$00W05F#13', '$00W06F#10', '$00W07F#11', '$00W08F#1E', '$00W09F#1F', '$00W10F#17', '$00W11F#16', '$00W12F#15', '$00W13F#14', '$00W14F#13', '$00W15F#12');

	$sql = "SELECT `extra` FROM `zone` WHERE `id`='$zone'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	$extra = $result['extra']-1;
	return $cmd_closes[$extra];
//$cmd_closes	= ['$00W01F#17', '$00W02F#14', '$00W03F#15', '$00W04F#12', '$00W05F#13', '$00W06F#10', '$00W07F#11', '$00W08F#1E', '$00W09F#1F', '$00W10F#17', '$00W11F#16', '$00W12F#15', '$00W13F#14', '$00W14F#13', '$00W15F#12'];

	/*switch($zone_id){
	case '39':
		$cmd = '$00W01F#17';
		break;
	case '40':
		$cmd = '$00W02F#14';
		break;
	case '41':
		$cmd = '$00W03F#15';
		break;
	case '42':
		$cmd = '$00W04F#12';
		break;
	case '43':
		$cmd = '$00W05F#13';
		break;
	case '44':
		$cmd = '$00W06F#10';
		break;
	case '45':
		$cmd = '$00W07F#11';
		break;
	case '46':
		$cmd = '$00W08F#1E';
		break;
	case '47':
		$cmd = '$00W11F#16';
		break;
	case '48':
		$cmd = '$00W12F#15';
		break;
	case '49':
		$cmd = '$00W13F#14';
		break;
	case '50':
		$cmd = '$00W14F#13';
		break;
	case '51':
		$cmd = '$00W15F#12';
		break;
	}
	return $cmd;*/
}
chkSession();
switch($type){
	case 'finishSnook':
		$zoneID =  mysql_real_escape_string($_REQUEST['zoneID']);
		finishSnook($zoneID);
		break;
}
?>