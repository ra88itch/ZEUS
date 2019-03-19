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
		$massage =  mysql_real_escape_string($_REQUEST['massage']);
		$massageV =  mysql_real_escape_string($_REQUEST['massageV']);
		$massageSpa =  mysql_real_escape_string($_REQUEST['massageSpa']);
		$massageRoom =  mysql_real_escape_string($_REQUEST['massageRoom']);
		$sauna =  mysql_real_escape_string($_REQUEST['sauna']);
		$fitness =  mysql_real_escape_string($_REQUEST['fitness']);
		$memberM =  mysql_real_escape_string($_REQUEST['memberM']);
		$memberY =  mysql_real_escape_string($_REQUEST['memberY']);
		$memberWM =  mysql_real_escape_string($_REQUEST['memberWM']);
		$memberWY =  mysql_real_escape_string($_REQUEST['memberWY']);
		$snooker =  mysql_real_escape_string($_REQUEST['snooker']);
		$snookerV =  mysql_real_escape_string($_REQUEST['snookerV']);
		setPrice($massage, '1');
		setPrice($massageV, '2');
		setPrice($massageSpa, '12');
		setPrice($massageRoom, '13');
		setPrice($sauna, '10');
		setPrice($fitness, '3');
		setPrice($memberM, '4');
		setPrice($memberY, '5');
		setPrice($memberWM, '19');
		setPrice($memberWY, '20');
		setPrice($snooker, '8');
		setPrice($snookerV, '9');

		$extraM =  mysql_real_escape_string($_REQUEST['extraM']);
		$extraY =  mysql_real_escape_string($_REQUEST['extraY']);
		$extraWM =  mysql_real_escape_string($_REQUEST['extraWM']);
		$extraWY =  mysql_real_escape_string($_REQUEST['extraWY']);
		setExtra($extraM, '4');
		setExtra($extraY, '5');
		setExtra($extraWM, '19');
		setExtra($extraWY, '20');


		$saunaSet =  mysql_real_escape_string($_REQUEST['saunaSet']);
		$warranty =  mysql_real_escape_string($_REQUEST['warranty']);
		$paidMsgTha =  mysql_real_escape_string($_REQUEST['paidMsgTha']);
		$paidMsgSpa =  mysql_real_escape_string($_REQUEST['paidMsgSpa']);
		setPrice($saunaSet, '15');
		setPrice($warranty, '16');
		setPrice($paidMsgTha, '17');
		setPrice($paidMsgSpa, '18');
		
		break;
}
?>