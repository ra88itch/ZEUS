<?php
defined('R88PROJ') or die ($system_error);

if(isset($_GET['page']) && $_GET['page']>1){
	$_GET['page'] = $_GET['page'];
}else{
	$_GET['page'] = '1';
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
	$sql = "SELECT COUNT(*) FROM $table WHERE DATE(`expire`) >= DATE(NOW() - INTERVAL 6 MONTH) AND `locker_type`> '0' ";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	return $result['COUNT(*)'];
}
function lastPage($rows_count){
	return ceil($rows_count/20);
}
function lockerClass($type){
	if($type == 1){
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
		<?php showNav('locker', $_GET['mod'], $_GET['page']);?>		
		<div class="onerow">
			<div class="result">
				<div class="col12">
					<p>
						<table>
							<thead>
								<tr>
									<td></td>
									<td>ชื่อ - นามสกุล</td>
									<td>ต่ออายุ</td>
									<td>ประเภทสมาชิก</td>
									<td>วันที่ลงทะเบียน</td>
									<td>สถานะ</td>
									<td>แก้ไข</td>
								</tr>
							</thead>
							<tbody>
							<?php							
							$start_on = ($_GET['page']-1)*20;
							$sql = "SELECT * FROM `locker` WHERE DATE(`expire`) >= DATE(NOW() - INTERVAL 6 MONTH) AND `locker_type`> '0' ORDER BY `id` ASC LIMIT ".$start_on.", 20";
							//$sql = "SELECT * FROM `locker` WHERE `locker_type`> '0' ORDER BY `id` ASC LIMIT ".$start_on.", 20";
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
									<td></td>
									<td><?php echo $row['firstname'], ' ', $row['lastname'], ' (', $row['locker_no'], ')'; ?></td>
									<td><?php if($dateDiff > 0){?><span class="icon24 renew" id="renew<?php echo $row['id']; ?>"></span><?php } ?></td>
									<td><?php echo lockerClass($row['locker_type']); ?></td>
									<td><?php echo $register[0], ' - ', $expire[0]; ?></td>
									<td><span class="icon24 status<?php echo ($row['active']==0 ? ' lock' : '' ); ?>" id="status<?php echo $row['id']; ?>"></span></td>
									<td><span class="icon24 edit" id="edit<?php echo $row['id']; ?>"></span></td>
								</tr>
							<?php
							}
							?>
							</tbody>
						</table>
					</p>
				</div>
				<div id="add" class="add">สมัครบริการล๊อกเกอร์ใหม่</div>
			</div>
		</div>
	</div>
</section>