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
<?php
if(isset($_GET['order_id']) && $_GET['order_id']!=''){ 
?>
	<script type="text/javascript">
	jsPrintSetup.setOption('orientation', jsPrintSetup.kPortraitOrientation);

	// set top margins in millimeters
	jsPrintSetup.setOption('marginTop', 5);
	jsPrintSetup.setOption('marginBottom', 15);
	jsPrintSetup.setOption('marginLeft', 20);
	jsPrintSetup.setOption('marginRight', 10);

	// set page header
	jsPrintSetup.setOption('headerStrLeft', '');
	jsPrintSetup.setOption('headerStrCenter', '');
	jsPrintSetup.setOption('headerStrRight', '');

	// set empty page footer
	jsPrintSetup.setOption('footerStrLeft', '');
	jsPrintSetup.setOption('footerStrCenter', '');
	jsPrintSetup.setOption('footerStrRight', '');

	// Suppress print dialog
	jsPrintSetup.setSilentPrint(true);

	// Do Print
	jsPrintSetup.print();

	// Restore print dialog
	jsPrintSetup.setSilentPrint(true); /** Set silent printing back to false */
	</script>
<?php } ?>
<script type="text/javascript" src="./js/jquery-1.11.2.min.js"></script>

<style>
html, body {
	background-color:#fff;
	height:100%;	
	margin:0;
	padding:0;
}
</style>
</HEAD>

<BODY>
<div id="info">
<?php
	if(isset($_GET['order_id']) && $_GET['order_id']!=''){
		$sql = "SELECT * FROM `order` WHERE `id`='".$_GET['order_id']."' LIMIT 1";
		$query = mysql_query($sql);	
		$results = mysql_fetch_assoc($query);
?>
<h2><?php echo getZoneName($results['order_inv']),'<br><br><span style="font-size:12px;">',$results['start'];?></h2>
---------------------------------
<div><?php echo menuName($results['menu_id']); ?> จำนวน <?php echo $results['unit'];?></div>
<div><?php echo $results['menu_desc']; ?></div>
<div>Ref. <?php echo $_GET['order_id']; ?></div>
---------------------------------
</div>
<div id="print" style="text-align:center;"><img src="images/printter.png"></div>
<script>
$(document).ready(function(){
	window.print();
	$('#print').click(function(){
		window.print();
	});
});
</script>
<?php 
	if($results['printed']=='0'){
		$sql_printed = "UPDATE `order` SET `printed`='1' WHERE `id`='".$_GET['order_id']."'";
		$query_printed = mysql_query($sql_printed);
	}
} ?>
</BODY>
</HTML>
