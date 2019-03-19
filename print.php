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
echo 'var invoiceID = ',$_GET['inv'],';';
if(isset($_GET['member_id']) && $_GET['member_id']!=''){
	echo 'var memberID = ',$_GET['member_id'],';';
}else{
	echo 'var memberID = "";';  
}
if(isset($_GET['receive']) && $_GET['receive']!=''){
	echo 'var receive = ',$_GET['receive'],';';  
}else{
	echo 'var receive = 0;';  
}
if(isset($_GET['change']) && $_GET['change']!=''){
	echo 'var change = ',$_GET['change'],';';  
}else{
	echo 'var change = 0;';  
}
echo '</script>';  

?>
<script>
$(document).ready(function(){
	getBill();
	$('#print').click(function(){
		window.print();
	});
});
function ajaxCall( _url, _successfunction, _data  ){
	$.post(_url, _data, _successfunction , "json" );
}
function getBill(){
	ajaxCall( 'api.php', setBill, { mod:'check_bill', type:'chkBillDetailsPrint', invoiceID:invoiceID, memberID:memberID });
}
function setBill(response){
	if(response.process == 'success'){	
		price_total = parseFloat(0);
		price_food = parseFloat(0);
		var html = '';
		var details = response.details;
		var count = details.length;
		html += '<table><tbody id="order_list">';
		for(var i=0; i < count; i++ ){
			var arr = details[i];

			// CASH ORDER
			if(arr.thisis == 'cash'){
				var price = arr.total;
				html += '<tr id="cash"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+price+'</td></tr>';

			// ECOUPON ORDER	
			} else if(arr.thisis == 'ecoupon'){
				var price = arr.total;
				html += '<tr id="ecoupon'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+price+'</td></tr>';

			// COUPON ORDER	
			} else if(arr.thisis == 'coupon'){
				var price = arr.total;
				html += '<tr id="coupon'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+price+'</td></tr>';

			// RESTAURANT ORDER	
			} else if(arr.thisis == 'restaurant'){
				var price = arr.total;
				html += '<tr id="resturant'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+price+'</td></tr>';

			// MASSAGE ORDER
			}else if(arr.thisis == 'massage'){
				var price = arr.total;
				if(arr.order_name != null){
					html += '<tr id="massage'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+price+'</td></tr>';
				}else{
					html += '<tr id="massage'+arr.id+'"><td>'+arr.unit+'</td><td>'+details[i-1].order_name+'</td><td>'+price+'</td></tr>';
				}
				price_total = parseFloat(price_total)+parseFloat(price);

			// SNOOKER ORDER	
			} else if(arr.thisis == 'snooker'){
				var hours = Math.floor( arr.times_min / 60);          
				var minutes = arr.times_min % 60;
				var price = arr.total;
				html += '<tr id="snooker'+arr.id+'"><td>'+hours+'.'+minutes+' ชั่วโมง</td><td>'+arr.order_name+'</td><td>'+price+'</td></tr>';
				price_total = parseFloat(price_total)+parseFloat(price);

			// SAUNA ORDER
			} else if(arr.thisis == 'sauna'){
				var price = arr.total;
				html += '<tr id="sauna'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+price+'</td></tr>';
				price_total = parseFloat(price_total)+parseFloat(price);

			// FOOD 'n DRINK ORDER
			} else if(arr.thisis == 'order' && arr.total !=0){
				var price = arr.total;
				html += '<tr id="order'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+price+'</td></tr>';
				price_food = parseFloat(price_food)+parseFloat(price);
			// MEMBER
			} else if(arr.thisis == 'member'){
				html += '<tr id="member'+arr.id+'"><td>1</td><td>สมัครสมาชิก '+arr.order_name+'</td><td>'+arr.total+'</td></tr>';
				thisIsMember = true;
			// LOCKER
			} else if(arr.thisis == 'locker'){
				html += '<tr id="locker'+arr.id+'"><td>1</td><td>เช่าตู้ Locker '+arr.order_name+'</td><td>'+arr.total+'</td></tr>';
				thisIsMember = true;
			// DISCOUNT
			} else if(arr.thisis == 'discount'){
				html += '<tr id="discount'+arr.id+'"><td>1</td><td>'+arr.order_name+'</td><td>-'+arr.total+'</td></tr>';
			}
		}
		html += '<tr><td colspan="3">---------------------------------</td></tr>';
		html += '<tr><td colspan="2">รวม</td><td>'+response.total+'</td></tr>';
		if(response.member_status==true){
			html += '<tr><td colspan="2">ส่วนลดค่าอาหาร</td><td>'+response.discount+'</td></tr>';
			html += '<tr><td colspan="2">ยอดหลังหักส่วนลด</td><td>'+response.grand_total+'</td></tr>';
		}else{
			response.discount = 0;
		}
		if(receive){
			html += '<tr><td colspan="2">จำนวนเงินที่รับ</td><td>'+receive+'</td></tr>';
			html += '<tr><td colspan="2">จำนวนเงินทอน</td><td>'+change+'</td></tr>';
		}
		html += '</tbody></table>';
	}else{
		var html = $('#bill').html();
		html += '<table><thead><tr><td>Unit</td><td>Description</td><td>Status</td></tr></thead><tbody id="order_list">';
		html += '</tbody></table><div class="check_bill">CHECK BILL</div><div class="payment_detail"></div><div class="print"></div>';	
	}
	$('#bill').html(html);
	$('#zone').html(response.service_name);
	$('#date').html(response.date);
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
<?php 
if(isset($_GET['pok'])){
?>
<div id="info">
	<h4>STAR THUNDER ENTERTAINMENT CO., LTD.</h4>
	53 ถนนวงแหวนอุตสาหกรรม แขวงช่องนนทรี<br>
	เขตยานนาวา กรุงเทพ 10310<br>
	Tel : 02-294-9581,02-294-9582
	</div>
<?php
}else{
?>
<div id="info">
	<h4>ใบแจ้งค่าใช้บริการ</h4>
</div>
<?php
}
?>
	
<?php if(isset($_GET['bill'])){  ?>
Invoice No. <span id="inv"><?php echo $_GET['bill']; ?></span><br>
<?php }?>
Zone Ref. <span id="zone"></span><br>
Date : <span id="date"></span><br>
---------------------------------
<div id="bill"></div>

<?php 
if(isset($_GET['pok'])){
?>
	<br><br>ลงชื่อลูกค้า ....................................<br>
<?php
}
?>
	

<div id="print" style="text-align:center;"><img src="images/printter.png"></div>

</BODY>
</HTML>
