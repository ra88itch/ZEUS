<?php
defined('R88PROJ') or die ($system_error);

if(isset($_GET['page']) && $_GET['page']>1){
	$_GET['page'] = $_GET['page'];
}else{
	$_GET['page'] = '1';
}
function memberEntertain($memberID){
	$sql	= "SELECT `order_inv` FROM `order_member` WHERE `customer_id`='$memberID'";
	$query	= mysql_query($sql);
	$row	= mysql_num_rows($query);

	if($row==0){
		$return = ' (Entertain)';
	}else{
		$result = mysql_fetch_assoc($query);

		$sql2	= "SELECT * FROM `invoice_bill` WHERE `inv_ref`='".$result['order_inv']."' AND `payment`='3'";
		$query2	= mysql_query($sql2);
		$row2	= mysql_num_rows($query2);
		if($row2==0){
			$return = '';
		}else{
			$return = ' (Entertain)';
		}
	}

	
	return $return;
}
function showNav($table, $mod, $current){
	$rows_count = rowsCount($table);
	if($rows_count > 20){

		$next = $current+1;
		$previus = $current-1;
		$last_page = lastPage($rows_count);
		
		$html = '<div class="onerow"><div class="result">';
		$html .= '<div class="col3"><a href="?mod='.$mod.'"><p> |< </p></a></div>';
		
		if($previus < $current && $previus >= 1){
			$html .= '<div class="col3"><a href="?mod='.$mod.'&page='.$previus.'"><p> < </p></a></div>';
		}else{
			$html .= '<div class="col3"><p> < </p></div>';
		}
		
		if($next > $current && $next <= $last_page){
			$html .= '<div class="col3"><a href="?mod='.$mod.'&page='.$next.'"><p> > </p></a></div>';
		}else{
			$html .= '<div class="col3"><p> > </p></div>';
		}
				
		if($last_page > $current){
			$html .= '<div class="col3"><a href="?mod='.$mod.'&page='.$last_page.'"><p> >| </p></a></div>';
		}else{
			$html .= '<div class="col3"><p> >| </p></div>';
		}
		
		$html .= '<div class="clear"></div></div></div>';
		echo $html;
	}
}
function rowsCount($table){
	//$sql = "SELECT COUNT(*) FROM $table WHERE `customer_type`> '0' AND (DATE(`expire`) - DATE(NOW())) > -30";
	$sql = "SELECT COUNT(*) FROM $table WHERE `customer_type`> '0'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	return $result['COUNT(*)'];
}
function lastPage($rows_count){
	return ceil($rows_count/20);
}
function memberClass($type){
	if($type == 1 || $type == 3){
		return 'รายเดือน';
	}else{
		return 'รายปี';
	}
}
function dateDiff($strDate1,$strDate2){
	return (strtotime($strDate2) - strtotime($strDate1))/  ( 60 * 60 * 24 );  // 1 day = 60*60*24
}
?>
<section id="sec-member">
	<div class="warp">
		<?php showNav('customer', $_GET['mod'], $_GET['page']);?>		
		<div class="onerow">
			<div class="result">
				<div class="col12">
					<p>
						<table>
							<thead>
								<tr>
									<td>เลขบัตร</td>
									<td>ชื่อ</td>
									<td>นามสกุล</td>
									<td>ประเภทสมาชิก</td>
									<td>วันที่ลงทะเบียน</td>
									<td>เข้าใช้งาน</td>
									<td>สถานะ</td>
									<td>แก้ไข</td>
								</tr>
							</thead>
							<tbody>
							<?php							
							$start_on = ($_GET['page']-1)*20;
							//$sql = "SELECT * FROM `customer` WHERE `customer_type`> '0' AND (DATE(`expire`) - DATE(NOW())) > -30 ORDER BY `id` ASC LIMIT ".$start_on.", 20";
							$sql = "SELECT * FROM `customer` WHERE `customer_type`> '0' ORDER BY `id` DESC LIMIT ".$start_on.", 20";
							$query = mysql_query($sql);
							while($row = mysql_fetch_assoc($query)){
								$register = explode(' ', $row['register']);
								$expire = explode(' ', $row['expire']);
								$today = date ("Y-m-d");

								$dateDiff = dateDiff($expire[0], $today);
								$status = '';
								if($dateDiff > 0){
									$status = ' style="color:red;"';
								}
							?>
								<tr<?php echo $status; ?>>
									<td><?php echo $row['id']; ?></td>
									<td><?php echo $row['firstname']; ?></td>								
									<td><?php echo $row['lastname'], ' (', $row['CARDID'], ')'; ?></td>
									<td><?php echo memberClass($row['customer_type']); ?><?php echo memberEntertain($row['id']); ?></td>
									<td><?php echo $register[0], ' - ', $expire[0]; ?></td>
									<td>
									<?php
									if($status == ''){
									?>
										<span class="member" id="member<?php echo $row['id']; ?>" style="cursor:pointer;">คลิ๊กที่นี่</span>
									<?php
									}
									?>
									</td>
									<td>
									<?php
									if($status == ''){
									?>
										<span class="icon24 status<?php echo ($row['active']==0 ? ' lock' : '' ); ?>" id="status<?php echo $row['id']; ?>"></span></td>
									<?php
									}
									?>
									<td>
									<?php
									if($status == ''){
									?>
										<span class="icon24 edit" id="edit<?php echo $row['id']; ?>"></span></td>
									<?php
									}
									?>
								</tr>
							<?php
							}
							?>
							</tbody>
						</table>
					</p>
				</div>
				<div id="add" class="add">สมัครสมาชิกใหม่</div>
			</div>
		</div>
	</div>
</section>