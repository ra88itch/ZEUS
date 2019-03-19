<?php
$hostname = "localhost";
$database = "thairesc_proj";
$username = "thairesc_proj";
$password = 'futera';

$conn = mysql_connect($hostname,$username,$password) or die($system_db_conn);
mysql_select_db($database, $conn) or die($system_db_name);
mysql_query('SET NAMES UTF8');

function calTime($orderID, $tableName){
	$get_times_sql = "SELECT `order_inv`, `zone_id`, (TIME_TO_SEC(`end`)-TIME_TO_SEC(`start`)) /60 AS `minutes` FROM `".$tableName."` WHERE `id`='".$orderID."'";
	$get_times_query = mysql_query($get_times_sql);
	$results = mysql_fetch_assoc($get_times_query);
	
	//echo $get_times_sql,'<br>';

	$get_times_sql = "UPDATE `".$tableName."` SET `times_in_min`='".$results['minutes']."' WHERE `id`='".$orderID."'";
	//echo $get_times_sql,'<br>';
	$get_times_query = mysql_query($get_times_sql);

	$sql = "UPDATE `zone` SET `reserved`='0' WHERE `id`='".$results['zone_id']."'";
	$query = mysql_query($sql);	

	echo $results['order_inv'];
}

calTime('1', 'order_snooker');
?>
