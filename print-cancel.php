<?php
define('R88PROJ',true);
session_start(); 

require('includes/responsed.php');
require('connect.php');
require('includes/function.php');

function getZoneName($invoice){
	$sql = "SELECT * FROM `invoice` WHERE `id`='".$invoice."'";
	$query = mysql_query($sql);
	$results = mysql_fetch_assoc($query);
	$zone_id = $results['zone'];

	if($zone_id>'0'){
		$sql = "SELECT * FROM `zone` WHERE `id`='".$zone_id."'";
		$query = mysql_query($sql);	
		$results = mysql_fetch_assoc($query);		
		return $results['zone'];
	}else{
		return $results['zone_extra'];
	}
}
function menuName($menu_id){
	$sql = "SELECT * FROM `menu` WHERE `id`='".$menu_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['menu_name_th'];
}
function accountName($account_id){
	$sql = "SELECT * FROM `account` WHERE `id`='".$account_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['firstname'].' '.$results['lastname'];
}
?>
<!DOCTYPE>
<HTML>
<HEAD>
<meta charset="UTF-8">
<TITLE> New Document </TITLE>
<META NAME="Generator" CONTENT="EditPlus">
<META NAME="Author" CONTENT="">
<META NAME="Keywords" CONTENT="">
<META NAME="Description" CONTENT="">
<style>
html, body {
	background-color:#fff;
	height:100%;	
	margin:0;
	padding:0;
}
body {
	font-size:14px;
	line-height:16px;
}
table {
	width:100%;
}
#info {
	text-align:center;
}
#print {
	cursor:pointer;
}
@media print {
	body {
		font-size:14px;
		padding-top:50px;
	}
	h4 {
		margin:0;
	}
	table {
		width:100%;
	}
	#print {
		display:none;
	}
}
</style>
<script type="text/javascript" src="./js/jquery-1.11.2.min.js"></script>
</HEAD>

<BODY>
<div id="info">
<?php
	if(isset($_GET['order_id']) && $_GET['order_id']!=''){
		$sql = "SELECT * FROM `order_cancel` WHERE `id`='".$_GET['order_id']."'";
		$query = mysql_query($sql);	
		$results = mysql_fetch_assoc($query);
?>
<h2><?php echo accountName($results['employee_id']),'<br><br><span style="font-size:12px;">',$results['start'];?></h2>
---------------------------------
<br>
<div><?php echo 'Ref.', $results['order_ref'], ' - ', menuName($results['menu_id']); ?> จำนวน <?php echo $results['unit'];?></div>
<br><br><br>
ผู้จัดการ _________________________
</div>
<div id="print" style="text-align:center;"><img src="images/printter.png"></div>
<script>
$(document).ready(function(){
	$('#print').click(function(){
		window.print();
	});
});
</script>
<?php } ?>
</BODY>
</HTML>