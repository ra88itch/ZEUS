<?php
defined('R88PROJ') or die ($system_error);

if(chkPermission('admin')!=true){
	session_destroy(); 
	header('Location: http://'.$_SERVER['HTTP_HOST']);
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
		<div class="result">
			<h3>ระบบบริหารจัดการค่าชั่วโมงพนักงานนวด </h3>
		</div>
		<?php //showNav('massage_money', $_GET['mod'], $_GET['page']);?>		
		<div class="onerow">
			<div class="result">
				<div class="col12"><form action="pdf/" method="get" target="_blank">
					<p style="text-align:left;">
						<select name="date">
						<?php
						$ahtml = '';
						$sql = "SELECT DISTINCT DATE(`checkout`) AS `date` FROM `invoice_bill` ORDER BY `checkout` DESC";
						$query = mysql_query($sql);
						while($result = mysql_fetch_assoc($query)){
							//$ahtml .= '<a href="pdf/index.php?report=daily_report&date='.$result['date'].'" target="blank">Report of '.$result['date'].'</a><br>';

							$ahtml .= '<option value="'.$result['date'].'">'.$result['date'].'</option>';
						}
						echo $ahtml;
						?>
						</select>
						<input type="hidden" name="report" value="massager">
						<input type="submit" value="Generate PDF">
					</p>
				</form></div>
			</div>
		</div>
	</div>
</section>