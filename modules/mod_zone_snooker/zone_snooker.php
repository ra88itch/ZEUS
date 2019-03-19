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
?>
<section id="sec-account">
	<div class="warp">
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
									<td>EDIT</td>
								</tr>
							</thead>
							<tbody>
							<?php							
							$start_on = ($_GET['page']-1)*20;
							$sql = "SELECT * FROM `zone` WHERE `zone_category`='8' || `zone_category`='9' ORDER BY `id` ASC LIMIT ".$start_on.", 20";
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
									<td><span class="icon24 edit" id="edit<?php echo $row['id']; ?>"></span></td>
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