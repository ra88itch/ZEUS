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
	$sql = "SELECT COUNT(*) FROM $table WHERE `zone_category`='3' OR `zone_category`='15'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	return $result['COUNT(*)'];
}
function lastPage($rows_count){
	return ceil($rows_count/20);
}
?>
<section id="sec-account">
	<div class="warp">
		<?php showNav('zone', $_GET['mod'], $_GET['page']);?>		
		<div class="onerow">
			<div class="result">
				<div class="col12">
					<p>
						<table>
							<thead>
								<tr>
									<td></td>
									<td>NAME</td>
									<td></td>
									<td></td>
									<td></td>									
									<td>STATUS</td>
									<td></td>
								</tr>
							</thead>
							<tbody>
							<?php							
							$start_on = ($_GET['page']-1)*20;
							$sql = "SELECT * FROM `zone` WHERE `zone_category`='3' OR `zone_category`='15' ORDER BY `id` ASC LIMIT ".$start_on.", 20";
							$query = mysql_query($sql);
							while($row = mysql_fetch_assoc($query)){
							?>
								<tr>
									<td></td>
									<td><?php echo $row['zone']; ?></td>
									<td></td>
									<td></td>
									<td></td>
									<td><span class="icon24 status<?php echo ($row['active']==0 ? ' lock' : '' ); ?>" id="status<?php echo $row['id']; ?>"></span></td>
									<td></td>
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