<?php
defined('R88PROJ') or die ($system_error);

if(chkPermission('admin')!=true){
	session_destroy(); 
	header('Location: http://'.$_SERVER['HTTP_HOST']);
}

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
	$sql = "SELECT COUNT(*) FROM $table WHERE `store_stock`='0'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	return $result['COUNT(*)'];
}
function lastPage($rows_count){
	return ceil($rows_count/20);
}

function getUnit($unitID) {
	$sql	=	"SELECT * FROM  `stock_unit` WHERE `id` = '".$unitID."'";
	$query	=	mysql_query($sql);
	$result	=	mysql_fetch_assoc($query);
	return $result['unit_name'];
}

?>

<section id="sec-stock">
	<div class="warp">
		<?php //showNav('stock', $_GET['mod'], $_GET['page']);?>
		<?php 
		if(isset($_GET['id']) && $_GET['id']!=''){
			$sql = "SELECT * FROM `stock` WHERE `id`='".$_GET['id']."' AND `store_stock`='0' ORDER BY `code` ASC, `name` ASC";
			$query = mysql_query($sql);
			$row = mysql_fetch_assoc($query);
		?>
		<div class="onerow">
			<div class="result">
				<div class="col12" style="text-align:left;">
					<p>
						<?php echo '['.$row['code'].'] '.$row['name']; ?>
					</p>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<div class="onerow">
			<div class="result">
				<div class="col12" style="text-align:left;">
					<p>
						<table>
							<thead>
								<tr>
									<td></td>
									<td>เหตุผล</td>
									<td>วันที่</td>
									<td>ยอดคงเหลือ</td>
									<td>เพิ่มรายการ</td>
									<td>เบิกรายการ</td>
									<td>หน่วยนับ</td>
								</tr>
							</thead>
							<tbody>
							<?php	
								$sql = "SELECT * FROM `stock_detail` WHERE `stock_id`='".$_GET['id']."' ORDER BY `date_time` DESC";
								$query = mysql_query($sql);
								while($results = mysql_fetch_assoc($query)){
							?>
								<tr class="row">
									<td></td>
									<td><?php echo $results['comment']; ?></td>
									<td><?php echo $results['date_time']; ?></td>
									<td style="text-align:left;"><?php echo $results['total']; ?></td>
									<td><?php echo $results['in']; ?></td>
									<td><?php echo $results['out']; ?></td>
									<td><?php echo getUnit($row['unit']); ?></td>
								</tr>
							<?php
								}
							?>
							</tbody>
						</table>
					</p>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<?php }else{ ?>
		<div class="onerow">
			<div class="result">
				<div class="col12" style="text-align:left;">
					<p>
						ค้นหารายการ <input type="text" id="filter">
					</p>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<div class="onerow">
			<div class="result">
				<div class="col12">
					<p>
						<table>
							<thead>
								<tr>
									<td></td>
									<td>รายการ</td>
									<td>ยอดคงเหลือ</td>
									<td>หน่วยนับ</td>
									<td>เพิ่มรายการ</td>
									<td>เบิกรายการ</td>
									<td>แก้ไข</td>
								</tr>
							</thead>
							<tbody>
							<?php							
							$start_on = ($_GET['page']-1)*20;
							//$sql = "SELECT * FROM `stock` WHERE `store_stock`='0' ORDER BY `name` LIMIT ".$start_on.", 20";
							$sql = "SELECT * FROM `stock` WHERE `store_stock`='0' ORDER BY `code` ASC, `name` ASC";
							$query = mysql_query($sql);
							while($row = mysql_fetch_assoc($query)){
							?>
								<tr class="row">
									<td></td>
									<td><a href="?mod=<?php echo $_GET['mod']?>&id=<?php echo $row['id'];?>"><?php echo '['.$row['code'].'] '.$row['name']; ?></a></td>
									<td style="text-align:left;"><?php if($row['amount'] <= $row['minimum']){ echo '<span style="color:#f00; text-align:right;">'.$row['amount'].'</span>';}else{ echo '<span style="text-align:right;">'.$row['amount'].'</span>'; }?></td>
									<td><?php echo getUnit($row['unit']); ?></td>
									<td><input id="increase<?php echo $row['id']; ?>" type="button" value="เพิ่มรายการ"></td>
									<td><input id="decrease<?php echo $row['id']; ?>" type="button" value="เบิกรายการ" <?php if($row['amount'] == 0) { echo "disabled"; echo " style = 'cursor:not-allowed'";}?>></td>
									<td><span class="icon24 edit" id="edit<?php echo $row['id']; ?>"></span></td>
								</tr>
							<?php
							}
							?>
							</tbody>
						</table>
					</p>
				</div>
				<div id="add" class="add">ADD NEW STOCK</div>
			</div>
		</div>
		<?php
		}
		?>
	</div>
</section>