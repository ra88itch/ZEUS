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
	$sql = "SELECT COUNT(*) FROM $table";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	return $result['COUNT(*)'];
}
function lastPage($rows_count){
	return ceil($rows_count/20);
}
function accountClass($type){
	if($type == 1){
		return 'administrator';
	}else{
		return 'user';
	}
}
function getTypeByCook($cookID){
	$sql	=	"SELECT * FROM `menu_type_cooking` WHERE `id` = '".$cookID."'";
	$query	=	mysql_query($sql);
	$result	=	mysql_fetch_assoc($query);
	return $result['type_cooking'];
}
function getTypeByMeat($meatID){
	$sql	=	"SELECT * FROM `menu_type_meat` WHERE `id` = '".$meatID."'";
	$query	=	mysql_query($sql);
	$result	=	mysql_fetch_assoc($query);
	return $result['type_meat'];
}
?>
<section id="sec-menu">
	<div class="warp">
		<?php showNav('menu', $_GET['mod'], $_GET['page']);?>		
		<div class="onerow">
			<div class="result">
				<div class="col12">
					<p>
						<table>
							<thead>
								<tr>
									<td></td>
									<td>รายการอาหาร</td>
									<td>ราคา</td>
									<td>ประเภทอาหาร</td>
									<td>ประเภทเนื้อสัตว์</td>
									<td>สถานะ</td>
									<td>แก้ไข</td>
								</tr>
							</thead>
							<tbody>
							<?php							
							$start_on = ($_GET['page']-1)*20;
							$sql = "SELECT * FROM `menu` ORDER BY `id` ASC LIMIT ".$start_on.", 20";
							$query = mysql_query($sql);
							while($row = mysql_fetch_assoc($query)){
							?>
								<tr>
									<td></td>
									<td><?php echo $row['menu_name_th']; ?></td>
									<td><?php echo $row['price'];?></td>
									<td><?php echo getTypeByCook($row['type_by_cooking']); ?></td>
									<td><?php echo getTypeByMeat($row['type_by_meat']); ?></td>
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
				<div id="add" class="add">เพิ่มรายการอาหาร</div>
			</div>
		</div>
	</div>
</section>