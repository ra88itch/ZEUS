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
	$('.finishSnook').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		finishSnook(thisID);
	});
});
function finishSnook(thisID){
	var thisID = thisID.replace('finishSnook','');
	ajaxCall( 'api.php', finishResponse, { mod:'set_snook', type:'finishSnook', zoneID:thisID });
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
					<p>
						<table>
							<thead>
								<tr>
									<td width="400px">หมายเลขโต๊ะ</td>
									<td width="300px">ปิดโต๊ะ</td>
								</tr>
							</thead>
							<tbody>
							<?php							
							$sql = "SELECT * FROM `zone` WHERE `reserved`='1' AND (`id` BETWEEN 39 AND 51) ";
							$query = mysql_query($sql);
							while($row = mysql_fetch_assoc($query)){
							?>
								<tr>
									<td><?php echo getZoneDetail($row['id']);?></td>
									<td><input type="button" class="finishSnook" id="finishSnook<?php echo $row[id]; ?>" value="ปิดโต๊ะ"></td>
								</tr>
							<?php
							}
							?>
							</tbody>
						</table>
					</p>
				</div>
			</div>
		</div>
	</div>
</section>