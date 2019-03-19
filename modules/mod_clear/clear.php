<?php
defined('R88PROJ') or die ($system_error);

if(chkPermission('admin')!=true){
	session_destroy(); 
	header('Location: http://'.$_SERVER['HTTP_HOST']);
}

if(isset($_POST['submit']) && $_POST['submit'] == 'delete'){
	$sql = "DELETE FROM `invoice` WHERE `id`='".$_POST['invoice']."'";
	//mysql_query($sql);
}

function getZoneDetail($zone_id){
	$sql = "SELECT * FROM `zone` WHERE `id`='".$zone_id."'";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['zone'];
}
function getMemberDetail($member_id){
	$sql = "SELECT * FROM `customer` WHERE `id`='".$member_id."'";
	$query = mysql_query($sql);	
	$results = mysql_fetch_assoc($query);
	return $results['firstname'].' '.$results['lastname'];
}
?>
<section id="sec-account">
	<div class="warp">
		<?php //showNav('invoice', $_GET['mod'], $_GET['page']);?>		
		<div class="onerow">
			<div class="result">
				<div class="col12">
					<p>
						<table>
							<thead>
							</thead>
							<tbody>
							<?php							
							$start_on = ($_GET['page']-1)*20;
							$sql = "SELECT * FROM `invoice` WHERE DATE(`checkout`)='0000-00-00' ORDER BY `id` DESC";
							$query = mysql_query($sql);
							while($row = mysql_fetch_assoc($query)){
							?>
								<tr>
									<td width="70%">
									<?php 
									if($row['zone'] > 0){
										echo getZoneDetail($row['zone']);
									}else if($row['zone'] == 0 && $row['member_id'] == 0){
										echo $row['zone_extra'];
									}else{
										echo getMemberDetail($row['member_id']);
									}
									?>									
									</td>									
									<td width="20%">
										<form action="#" method="POST">
											<input type="hidden" name="invoice" value="<?php echo $row['id']; ?>">
											<button type="submit" name="submit" value="delete">
												<span class="icon24 delete"></span>
											</button>
										</form>
									</td>
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