<?php
defined('R88PROJ') or die ($system_error);

$type			=	$_POST['type'];

function setPrice($price, $category){
	$sql = "UPDATE `zone_category` SET `charge`='$price' WHERE `id`='".$category."'";
	$query = mysql_query($sql);	
}
function setExtra($extra, $category){
	$sql = "UPDATE `zone_category` SET `extra`='$extra' WHERE `id`='".$category."'";
	$query = mysql_query($sql);	
}
chkSession();
switch($type){
	case 'setPrice':

		$memberBoxingKids1 =  mysql_real_escape_string($_REQUEST['memberBoxingKids1']);
		$memberBoxingKids3 =  mysql_real_escape_string($_REQUEST['memberBoxingKids3']);
		$memberBoxingKids4 =  mysql_real_escape_string($_REQUEST['memberBoxingKids4']);
		$memberBoxingKids12 =  mysql_real_escape_string($_REQUEST['memberBoxingKids12']);
		$memberBoxing1 =  mysql_real_escape_string($_REQUEST['memberBoxing1']);
		$memberBoxing3 =  mysql_real_escape_string($_REQUEST['memberBoxing3']);
		$memberBoxing4 =  mysql_real_escape_string($_REQUEST['memberBoxing4']);
		$memberBoxing12 =  mysql_real_escape_string($_REQUEST['memberBoxing12']);
		$boxingKids =  mysql_real_escape_string($_REQUEST['boxingKids']);
		$boxing =  mysql_real_escape_string($_REQUEST['boxing']);
		setPrice($memberBoxingKids1, '24');
		setPrice($memberBoxingKids3, '25');
		setPrice($memberBoxingKids4, '26');
		setPrice($memberBoxingKids12, '27');
		setPrice($memberBoxing1, '28');
		setPrice($memberBoxing3, '29');
		setPrice($memberBoxing4, '30');
		setPrice($memberBoxing12, '31');
		setPrice($boxingKids, '32');
		setPrice($boxing, '33');
		
		break;
}
?>