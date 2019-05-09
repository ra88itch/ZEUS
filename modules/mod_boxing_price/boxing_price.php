<?php
defined('R88PROJ') or die ($system_error);

function getZonePrice($zone_category){
	$array = array('4','5','19','20','24','25','26','27','28','29','30','31');
	$sql = "SELECT * FROM `zone_category` WHERE `id`='".$zone_category."'";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	if(in_array($zone_category, $array)){
		/*$response = '<tr>
					<td>'.$results['zonename'].'</td>
					<td><input type"text" id="category'.$zone_category.'" value="'.$results['charge'].'"> ระยะเวลา <input type"text" id="extra'.$zone_category.'" value="'.$results['extra'].'" disabled> เดือน</td>
				</tr>';*/
		$response = '<tr>
					<td>'.$results['zonename'].'</td>
					<td><input type"text" id="category'.$zone_category.'" value="'.$results['charge'].'"> ระยะเวลา '.$results['extra'].' เดือน</td>
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

	var memberBoxingKids1	= $('#category24').val();
	var memberBoxingKids3	= $('#category25').val();
	var memberBoxingKids4	= $('#category26').val();
	var memberBoxingKids12	= $('#category27').val();
	var memberBoxing1		= $('#category28').val();
	var memberBoxing3		= $('#category29').val();
	var memberBoxing4		= $('#category30').val();
	var memberBoxing12		= $('#category31').val();
	var boxingKids			= $('#category32').val();
	var boxing				= $('#category33').val();

	$('body').css('cursor','loading');
	ajaxCall( 'api.php', finishResponse, { mod:'boxing_price', type:'setPrice', memberBoxingKids1:memberBoxingKids1, memberBoxingKids3:memberBoxingKids3, memberBoxingKids4:memberBoxingKids4, memberBoxingKids12:memberBoxingKids12, memberBoxing1:memberBoxing1, memberBoxing3:memberBoxing3, memberBoxing4:memberBoxing4, memberBoxing12:memberBoxing12, boxingKids:boxingKids, boxing:boxing });
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

								<?php echo getZonePrice('24');?>
								<?php echo getZonePrice('25');?>
								<?php echo getZonePrice('26');?>
								<?php echo getZonePrice('27');?>
								<?php echo getZonePrice('28');?>
								<?php echo getZonePrice('29');?>
								<?php echo getZonePrice('30');?>
								<?php echo getZonePrice('31');?>
								<?php echo getZonePrice('32');?>
								<?php echo getZonePrice('33');?>
								<tr class="submit" style="text-align:left;"><td></td><td><input type="button" id="submit" value="SAVE"></td></tr>
							</tbody>
						</table>
						
					</p>
					<br><br><br>
				</div>
			</div>
		</div>
	</div>
</section>
