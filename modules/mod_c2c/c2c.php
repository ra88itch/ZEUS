<?php
defined('R88PROJ') or die ($system_error);

function getZoneDetail($zone_id){
	$sql = "SELECT * FROM `zone` WHERE `id`='".$zone_id."'";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['zone'];
}
?>
<script>
$(document).ready(function(){
	$('#submit').click(function(){
		var cash = $('#cash').val();
		if(cash > 0){
			c2c(cash);
		}else{
			alert('กรุณาระบุจำนวนเงินอีกครั้ง');
		}
	});
});
function c2c(cash){

	ajaxCall( 'api.php', c2cResponse, { mod:'c2c', type:'c2c', cash:cash });
}
function c2cResponse(response){
	location.reload();
}
</script>
<section id="sec-c2c">
	<div class="warp">
		<div id="customer-detail" class="result">
			<div style="background-color:#fff; padding:10px 30px; overflow:hidden; border-radius:5px;">
				<h2>บริการเบิกเงินสด</h2>
				<div>กรุณากรอกจำนวนเงิน <input type="text" id="cash" maxlength="5"> บาท</div>
				<div class="open" id="submit">ยืนยันรายการ</div>
			</div>			
		</div>
	</div>
</section>