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
function accountName($employee_id){
	$sql = "SELECT * FROM `account` WHERE `id`='$employee_id'";
	$query = mysql_query($sql);
	$row = mysql_fetch_assoc($query);
	return $row['firstname'].' '.$row['lastname'];
}
?>
<section id="sec-account">
	<div class="warp">
		<?php showNav('massage_money', $_GET['mod'], $_GET['page']);?>		
		<div class="onerow">
			<div class="result">
				<div class="col12">
					<p>
						<table>
							<thead>
								<tr>
									<td></td>
									<td>DATE</td>
									<td>MASSGER NO.</td>
									<td></td>
									<td>CREATE BY</td>
									<td>EDIT</td>
									<td></td>
								</tr>
							</thead>
							<tbody>
							<?php							
							$start_on = ($_GET['page']-1)*20;
							$sql = "SELECT * FROM `massage_money` ORDER BY `date` DESC LIMIT ".$start_on.", 20";
							$query = mysql_query($sql);
							while($row = mysql_fetch_assoc($query)){
							?>
								<tr>
									<td></td>
									<td><a href="pdf/index.php?report=massager&date=<?php echo $row['date']; ?>" target="blank"><?php echo $row['date']; ?></a></td>
									<td><?php echo $row['massager_no']; ?></td>
									<td></td>
									<td><?php echo accountName($row['employee_id']); ?></td>
									<td><span class="icon24 edit" id="edit<?php echo $row['id']; ?>"></span></td>
									<td></td>
								</tr>
							<?php
							}
							?>
							</tbody>
						</table>
					</p>
				</div>
				<div id="add" class="add">CREATE MASSAGER PROFILE</div>
			</div>
		</div>
	</div>
</section>