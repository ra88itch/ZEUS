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
			<div style="width:70%; float:left;">
				<table>
					<thead>
						<tr>
							<td>จำนวน</td>
							<td>รายการอาหาร</td>
							<td>ผู้ยกเลิก</td>
							<td>เวลา</td> 
							<?php //<td>สถานะ</td> ?>
						</tr>
					</thead>
					<tbody id="order_list">
						<?php
						$sql = "SELECT * FROM `order_cancel` WHERE `cancel_approve`='0' ORDER BY `start`DESC";
						$query = mysql_query($sql);	
						while($results = mysql_fetch_assoc($query)) {
							$menuDetail = menuName($results['menu_id']);
							$accountName = accountName($results['employee_id']);
						?>
						<tr class="order" id="order<?php echo $results['id']; ?>">
							<td><?php echo $results['unit']; ?></td>
							<td><?php echo $menuDetail[0]; ?>
							<?php
								$chk_sql = "SELECT COUNT(*) AS `count` FROM `order` WHERE `id`='".$results['order_ref']."'";
								$chk_query = mysql_query($chk_sql);	
								$chk_results = mysql_fetch_assoc($chk_query);
								if($chk_results['count'] > 0){
									echo 'ยกเลิก';
								}else{
									echo 'ลบรายการ';
								}
							?>
							</td>
							<td><?php echo $accountName; ?></td>
							<td><?php echo $results['start']; ?></td>
							<?PHP //<td>รอการตรวจสอบ</td> ?>
						</tr>
						<?php
						}
						?>
						
					</tbody>
				</table>
			</div>
			<section style="width:27%; float:right;">
				<iframe frameborder="0" src="print-cancel.php" id="print-frame"></iframe>
			</section>
		</div>
	</div>
</section>