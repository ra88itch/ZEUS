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
	//$sql = "SELECT COUNT(*) FROM `".$table."` JOIN (`menu`) ON (`menu`.`id`=`order`.`menu_id`) WHERE `order_status`>= '5'AND (`menu`.`type_by_cooking`!='8' AND `menu`.`type_by_cooking`!='9' AND `menu`.`type_by_cooking`!='16')";
	$sql = "SELECT COUNT(*) FROM `".$table."` JOIN (`menu`) ON (`menu`.`id`=`order`.`menu_id`) WHERE `order_status`>= '5'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	return $result['COUNT(*)'];
}
function lastPage($rows_count){
	return ceil($rows_count/20);
}
function getZoneName($invoice){
	$sql = "SELECT * FROM `invoice` WHERE `id`='".$invoice."'";
	$query = mysql_query($sql);
	$results = mysql_fetch_assoc($query);
	$zone_id = $results['zone'];

	if($zone_id>'0'){
		$sql = "SELECT * FROM `zone` WHERE `id`='".$zone_id."'";
		$query = mysql_query($sql);	
		$results = mysql_fetch_assoc($query);		
		return $results['zone'];
	}else{
		return $results['zone_extra'];
	}
}
?>
<section id="sec-account">
	<div class="warp">
		<?php showNav('order', $_GET['mod'], $_GET['page']);?>		
		<div class="onerow">
			<div class="result">
				<div class="col12">
					<p>
						<table>
							<thead>
								<tr>
									<td>จำนวน</td>
									<td>รายการอาหาร</td>
									<td>รายละเอียด</td>
									<td>เวลา</td>
								</tr>
							</thead>
							<tbody>
							<?php							
							$start_on = ($_GET['page']-1)*20;
							//$sql = "SELECT `order`.*, `menu`.`menu_name_th` FROM `order` JOIN (`menu`) ON (`menu`.`id`=`order`.`menu_id`) WHERE `order_status`>= '5'AND (`menu`.`type_by_cooking`!='8' AND `menu`.`type_by_cooking`!='9' AND `menu`.`type_by_cooking`!='16') ORDER BY `order`.`id` DESC LIMIT ".$start_on.", 20";
							$sql = "SELECT `order`.*, `menu`.`menu_name_th` FROM `order` JOIN (`menu`) ON (`menu`.`id`=`order`.`menu_id`) WHERE `order_status`>= '5' ORDER BY `order`.`id` DESC LIMIT ".$start_on.", 20";
							$query = mysql_query($sql);
							while($row = mysql_fetch_assoc($query)){
							?>
								<tr>
									<td><?php echo $row['unit']; ?></td>
									<td><?php echo $row['menu_name_th'], '[',getZoneName($row['order_inv']),']'; ?></td>
									<td><?php echo $row['menu_desc']; ?></td>
									<td><?php echo $row['start']; ?></td>
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