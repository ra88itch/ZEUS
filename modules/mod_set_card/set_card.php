<?php
defined('R88PROJ') or die ($system_error);

function getZoneDetail($zone_id){
	$sql = "SELECT * FROM `zone` WHERE `id`='".$zone_id."'";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['zone'];
	//return $sql;
}
?>
<script>
$(document).ready(function(){
	$('.finishSauna').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		finishSauna(thisID);
	});
});
function finishSauna(thisID){
	var thisID = thisID.replace('finishSauna','');
	ajaxCall( 'api.php', finishResponse, { mod:'customer_detail', type:'finishSauna', orderID:thisID });
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
									<td width="400px">เลขบัตร</td>
									<td width="300px">คืนบัตร</td>
								</tr>
							</thead>
							<tbody>
							<?php							
							$sql = "SELECT * FROM `order_sauna` WHERE `end`='0000-00-00 00:00:00' ORDER BY `start` ASC";
							$query = mysql_query($sql);
							while($row = mysql_fetch_assoc($query)){
							?>
								<tr>
									<td><?php echo getZoneDetail($row['zone_id']);?> [<?php echo $row['start']?>]</td>
									<td><input type="button" class="finishSauna" id="finishSauna<?php echo $row[id]; ?>" value="คืนบัตร"></td>
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