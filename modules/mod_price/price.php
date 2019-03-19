<?php
defined('R88PROJ') or die ($system_error);

function getZonePrice($zone_category){
	$sql = "SELECT * FROM `zone_category` WHERE `id`='".$zone_category."'";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	if($zone_category=='4' || $zone_category=='5' || $zone_category=='19' || $zone_category=='20'){
		$response = '<tr>
					<td>'.$results['zonename'].'</td>
					<td><input type"text" id="category'.$zone_category.'" value="'.$results['charge'].'"> ระยะเวลา <input type"text" id="extra'.$zone_category.'" value="'.$results['extra'].'"> เดือน</td>
				</tr>';
	}else{
		$response = '<tr>
					<td>'.$results['zonename'].'</td>
					<td><input type"text" id="category'.$zone_category.'" value="'.$results['charge'].'"> / '.$results['extra'].'</td>
				</tr>';
	}
	
	return $response;
}

if(chkPermission('admin')!=true){
	session_destroy(); 
	header('Location: http://'.$_SERVER['HTTP_HOST']);
}
?>
<script>
$(document).ready(function(){
	$('#submit').click(function(){
		setPrice();
	});
});
function setPrice(){
	var massage		= $('#category1').val();
	var massageV	= $('#category2').val();
	var massageSpa	= $('#category12').val();
	var massageRoom = $('#category13').val();
	var sauna		= $('#category10').val();
	var fitness		= $('#category3').val();
	var memberM		= $('#category4').val();
	var memberY		= $('#category5').val();
	var memberWM	= $('#category19').val();
	var memberWY	= $('#category20').val();
	var snooker		= $('#category8').val();
	var snookerV	= $('#category9').val();

	var extraM = $('#extra4').val();
	var extraY = $('#extra5').val();
	var extraWM = $('#extra19').val();
	var extraWY = $('#extra20').val();

	var saunaSet	= $('#category15').val(); // คูปอง 15 มบ
	var warranty	= $('#category16').val(); // เงินประกันรายได้
	var paidMsgTha	= $('#category17').val();
	var paidMsgSpa	= $('#category18').val();

	if(massage!='' && massageV!='' && massageSpa!='' && massageRoom!='' && sauna!='' && fitness!='' && memberM!='' && memberY!='' && snooker!='' && snookerV!='' && extraM!='' && extraY!=''){
		ajaxCall( 'api.php', finishResponse, { mod:'price', type:'setPrice', massage:massage, massageV:massageV, massageSpa:massageSpa, massageRoom:massageRoom, sauna:sauna, fitness:fitness, memberM:memberM, memberY:memberY, snooker:snooker, snookerV:snookerV, extraM:extraM, extraY:extraY, warranty:warranty, paidMsgTha:paidMsgTha, paidMsgSpa:paidMsgSpa, saunaSet:saunaSet, memberWM:memberWM, memberWY:memberWY, extraWM:extraWM, extraWY:extraWY });
	}else{
		alert('ห้ามใส่ช่องว่าง');
	}
}
function finishResponse(response){
	location.reload();
}
</script>
<section id="sec-stock">
	<div class="warp">
		<div class="onerow">
			<div class="result">
				<div class="col12">
					<p style="cursor:default;">
						<table>
							<thead>
								<tr>
									<td width="400px">แผนกที่ใช้บริการ</td>
									<td width="600px">ราคา</td>
								</tr>
							</thead>
							<tbody>
								<?php echo getZonePrice('1');?>
								<?php echo getZonePrice('2');?>
								<?php echo getZonePrice('12');?>
								<?php echo getZonePrice('13');?>
								<?php echo getZonePrice('10');?>
								<?php echo getZonePrice('3');?>
								<?php echo getZonePrice('4');?>
								<?php echo getZonePrice('5');?>								
								<?php echo getZonePrice('19');?>
								<?php echo getZonePrice('20');?>
								<?php echo getZonePrice('8');?>
								<?php echo getZonePrice('9');?>
								<?php echo getZonePrice('14');?>
								<?php echo getZonePrice('15');?>
								<?php echo getZonePrice('16');?>
								<?php echo getZonePrice('17');?>
								<?php echo getZonePrice('18');?>
								<tr class="submit" style="text-align:left;"><td></td><td><input type="button" id="submit" value="SAVE"></td></tr>
							</tbody>
						</table>
						
					</p>
				</div>
			</div>
		</div>
	</div>
</section>