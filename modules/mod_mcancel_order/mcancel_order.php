<?php
defined('R88PROJ') or die($system_error);
function menuName($menu_id){
	$sql = "SELECT * FROM `menu` WHERE `id`='".$menu_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return array($results['menu_name_th'], $results['type_by_cooking']);
}
function accountName($account_id){
	$sql = "SELECT * FROM `account` WHERE `id`='".$account_id."' LIMIT 1";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['firstname'].' '.$results['lastname'];
}
?>
<style>
#order-detail table{
	background-color: rgba(255, 255, 255, 0.8);
    width: 100%;
}
#order-detail table thead {
	background-color: rgba(0, 0, 0, 0.4);
    width: 100%;
}
#order-detail table thead td {
	text-align: center;
}
#order-detail iframe {
	height: 500px;
    overflow-y: scroll;
    width: 100%;
}
</style>
<section id="sec-kitchen">
	<div class="warp">
		<div id="order-detail" class="result">
			<h3>รายการอาหารยกเลิก</h3>
				<table>
					<thead>
						<tr>
							<td>จำนวน</td>
							<td>รายการอาหาร</td>
							<td>โต๊ะลูกค้าผู้สั่งอาหาร</td>
							<td>ผู้ยกเลิก</td>
							<td>เวลา</td> 
							<td>การตรวจสอบ</td>
						</tr>
					</thead>
					<tbody id="order_list">
						<?php
						$sql = "SELECT * FROM `order_cancel` WHERE `cancel_approve`='0' ORDER BY `start` DESC";
						$query = mysql_query($sql);	
						while($results = mysql_fetch_assoc($query)) {
							$menuDetail = menuName($results['menu_id']);
							$accountName = accountName($results['employee_id']);
						?>
						<tr>
							<td><?php echo $results['unit']; ?></td>
							<td><?php echo $menuDetail[0]; ?>
							<?php
								$chk_sql = "SELECT *, COUNT(*) AS `count` FROM `order` WHERE `id`='".$results['order_ref']."'";
								$chk_query = mysql_query($chk_sql);	
								$chk_results = mysql_fetch_assoc($chk_query);
								
								$xxx = '';
								if($chk_results['end'] != '0000-00-00 00:00:00'){
									$xxx = ' <span style="color:red;">[อาหารปรุงเสร็จแล้ว]</span>';
								}
								if($chk_results['count'] > 0){
									echo 'ยกเลิก', $xxx;
								}else{
									echo 'ลบรายการ',$xxx;
								}
							?>
							</td>
							<td>
							<?php
								//echo $chk_results['order_inv'];
								$inv_sql = "SELECT * FROM `invoice` WHERE `id`='".$chk_results['order_inv']."'";
								//echo $inv_sql;
								$inv_query = mysql_query($inv_sql);	
								$inv_results = mysql_fetch_assoc($inv_query);
								if($inv_results['zone'] == 0){
									$zone = 'ลูกค้าเงินสด';
									if($inv_results['zone_extra'] != ''){
										$zone = $inv_results['zone_extra'];
									}
								}else{
									$zone_sql = "SELECT * FROM `zone` WHERE `id`='".$inv_results['zone']."'";
									$zone_query = mysql_query($zone_sql);	
									$zone_results = mysql_fetch_assoc($zone_query);
									$zone = $zone_results['zone'];
								}
								echo $zone;
							?>
							</td>
							<td><?php echo $accountName; ?></td>
							<td><?php echo $results['start']; ?></td>
							<td></td>
						</tr>
						<?php
							/* 
							<td><button class="order" id="confirm<?php echo $results['id']; ?>" value="ยืนยัน">ยืนยัน</button></td> */
						}
						?>
						
					</tbody>
				</table>
		</div>
	</div>
</section>