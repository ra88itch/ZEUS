<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> New Document </TITLE>
<META NAME="Generator" CONTENT="EditPlus">
<META NAME="Author" CONTENT="">
<META NAME="Keywords" CONTENT="">
<META NAME="Description" CONTENT="">
</HEAD>

<BODY>
 <?php
error_reporting(0);
define('R88PROJ',true);

session_start(); 


require('includes/responsed.php');
require('connect.php');
require('includes/function.php');

$sql = "SELECT * FROM `menu` WHERE `future_date`!='0000-00-00' AND DATE(`future_date`)<=CURDATE() AND `future_price`!='0'";
	$html .= '1<br>';
	$query = mysql_query($sql);
	$html .= '2<br>';
	while($results = mysql_fetch_assoc($query)){
		/*$html .= '4<br>';
		$curDate = date("Y-m-d");
		$curTime = date("Y-m-d H:i:s");
		if($curTime > ($curDate.' 07:00:00')){
			setNewPrice($results['id'], $results['future_price']);
		}*/
		$html .= $results['id'].' - '. $results['future_price'].'<br>';
	}
	echo $html;
?>
 </BODY>
</HTML>
