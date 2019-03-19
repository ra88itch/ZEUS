<?php
session_start();
?>

<!DOCTYPE>
<HTML>
<HEAD>
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
h4 {
	margin:0;
}
table {
	width:100%;
}
#info {
	text-align:center;
	padding:10px;
}
#print {
	cursor:pointer;
}
@media print {
	body {
		font-size:14px;
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
<?php	
echo '<script>'; 
if(isset($_GET['date']) && $_GET['date']!=''){
	echo 'var date = "',$_GET['date'],'";';  
}
echo '</script>';  

?>
<script>
$(document).ready(function(){
	getdayResult();
	$('#print').click(function(){
		window.print();
	});
});
function ajaxCall( _url, _successfunction, _data  ){
	$.post(_url, _data, _successfunction , "json" );
}
function getdayResult(){
	ajaxCall( 'api.php', setdayResult, { mod:'daily', type:'dayResult', date:date });
}
function setdayResult(response){
	if(response.process == 'success'){		
		$('#bill').html(response.html);
		//console.log(response.html);
	}
}
</script>
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
<h4>รายรับแคชเชียร์ประจำวัน</h4>
วันที่ <?php echo $_GET['date']?>
<br>
แคชเชียร์ <?php echo $_SESSION['user_name']; ?>
<br>
Time <?php echo date('Y-m-d H:i:s'); ?>
</div>
---------------------------------
<div id="bill"></div>

<div id="print" style="text-align:center;"><img src="images/printter.png"></div>
</BODY>
</HTML>
