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

function showNav2($table, $mod, $salary_id, $current){
	$rows_count = rowsCount2($table, $salary_id);
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
function rowsCount2($table, $salary_id){
	$sql = "SELECT COUNT(*) FROM $table WHERE `salary_ref`='$salary_id'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	return $result['COUNT(*)'];
}
function getEmployee($employeeID){
	$sql = "SELECT * FROM `employee` WHERE `id`='$employeeID'";
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	$name = $result['firstname'].' '.$result['lastname'];
	return array($name, $result['salary'], $result['position']);
}

function calSalary($salaryDetailID) {
	$sql	=	"SELECT * FROM `salary_detail` WHERE `id` = '".$salaryDetailID."'";
	$query	=	mysql_query($sql);
	$result	=	mysql_fetch_assoc($query);
	
	$getEmployee	=	getEmployee($result['employee_id']);
	$day_off		=	$getEmployee[1]/30*$result['day_off'];
	$late_value		=	$result['late']*5;	
	$paid			=	$getEmployee[1]-$day_off-$late_value;
	if($getEmployee[2]==1) {
		$extraMoney	=	calExtraSalary($result['salary_ref'],$result['employee_id'])*100;
		$paid		=	$paid + $extraMoney;
	}
	return $paid;
}

function calExtraSalary($salaryID,$employeeID) {
	$sql	=	"SELECT * FROM `salary` WHERE `id` = '".$salaryID."'";
	$query	=	mysql_query($sql);
	$result	=	mysql_fetch_assoc($query);
	
	$start	=	$result['start_date']." 00:00:00";
	$end	=	$result['end_date']." 23:59:59";
	$sql	=	"SELECT * FROM  `order_massage` WHERE `employee_id` = '".$employeeID."' AND `start` >= '".$start."' AND end <= '".$end."' ";
	$query	=	mysql_query($sql);
	$rows	=	mysql_num_rows($query);
	return $rows;
}
?>
<section id="sec-salary">
	<div class="warp">
		<?php 
		if(!isset($_GET['salary_id']) && $_GET['salary_id'] ==''){
			showNav('salary', $_GET['mod'], $_GET['page']);
		} else {
			showNav2('salary_detail', $_GET['mod'], $_GET['salary_id'], $_GET['page']);
		}
		?>		
		<div class="onerow">
			<div class="result">
				<div class="col12">
					<p>
						<?php
						if(!isset($_GET['salary_id']) && $_GET['salary_id'] ==''){
						?>
						<table>
							<thead>
								<tr>
									<td>ID</td>
									<td>NAME</td>
									<td>START DATE</td>
									<td>END DATE</td>
									<td>CREATE DATE</td>
								</tr>
							</thead>
							<tbody>
							<?php							
							$start_on = ($_GET['page']-1)*20;
							$sql = "SELECT * FROM `salary` ORDER BY `id` ASC LIMIT ".$start_on.", 20";
							$query = mysql_query($sql);
							while($row = mysql_fetch_assoc($query)){
							?>
								<tr>
									<td><?php echo $row['id']; ?></td>
									<td><a href="?mod=salary&salary_id=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a></td>
									<td><?php echo $row['start_date']; ?></td>
									<td><?php echo $row['end_date'];?></td>
									<td><?php echo $row['create_date']; ?></td>
								</tr>
							<?php
							}
							?>
							</tbody>
						</table>
						<?php
						} else {
						?>
						<table>
							<thead>
								<tr>
									<td>ID</td>
									<td>EMPLOYEE NAME</td>
									<td>SALARY</td>
									<td>DAY OFF</td>
									<td>LATE</td>
									<td>PAID</td>
									<td>EDIT</td>
								</tr>
							</thead>
							<tbody>
							<?php							
							$start_on = ($_GET['page']-1)*20;
							$sql = "SELECT * FROM `salary_detail` WHERE `salary_ref` = '".$_GET['salary_id']."' ORDER BY `id` ASC LIMIT ".$start_on.", 20";
							$query = mysql_query($sql);
							while($row = mysql_fetch_assoc($query)){
								$getEmployee = getEmployee($row['employee_id']);
								$paid	=	calSalary($row['id']);
							?>
								<tr>
									<td><?php echo $row['id']; ?></td>
									<td><?php echo $getEmployee[0]; ?></td>
									<td><?php echo $getEmployee[1]; ?></td>
									<td><?php echo $row['day_off']; ?></td>
									<td><?php echo $row['late']; ?></td>
									<td><?php echo $paid; ?></td>
									<td><span id="edit<?php echo $row['id']; ?>" class="icon24 edit"></span></td>
									
								</tr>
							<?php
							}
							?>
							</tbody>
						</table>
						<?php
						}
						?>
					</p>
				</div>
				<?php
				if(!isset($_GET['salary_id']) && $_GET['salary_id'] ==''){
				?>
				<div id="add" class="add">ADD NEW SALARY</div>
				<?php
				}
				?>
			</div>
		</div>
	</div>
</section>