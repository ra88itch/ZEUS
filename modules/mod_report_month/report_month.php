<?php
defined('R88PROJ') or die($system_error);

if(chkPermission('admin')!=true){
	session_destroy(); 
	header('Location: http://'.$_SERVER['HTTP_HOST']);
}
?>
<style>

#sec-order .result .col3 {
	height:105px;
}
#customer-detail table {
	background-color: rgba(255, 255, 255, 0.8);
	width:100%;
}
#customer-detail table thead{
	background-color: rgba(0, 0, 0, 0.4);
	width:100%;
}
#customer-detail table thead td{
	text-align:center;
}
#customer-detail iframe{
	height:500px;
	overflow-y:scroll;
	width:100%;
}
</style>
<section id = "sec-order">
	<div class="warp">
		<div id="customer-detail" class="result">
			<h3>รายรับประจำเดือน </h3>
		</div>
		<div class="result">
			<div class="col12"><form action="pdf/" method="get" target="_blank">
				<p style="text-align:left;">
					
					<select name="date">
					<?php
					$ahtml = '';
					$sql = "SELECT DISTINCT(DATE_FORMAT(`checkout`, '%Y-%m')) AS `date` FROM `invoice_bill` ORDER BY `checkout` DESC";
					$query = mysql_query($sql);
					while($result = mysql_fetch_assoc($query)){
						//$ahtml .= '<a href="pdf/index.php?report=monthly_report&date='.$result['date'].'" target="blank">Report of '.$result['date'].'</a><br>';

						$ahtml .= '<option value="'.$result['date'].'">'.$result['date'].'</option>';

					}
					echo $ahtml;
					?>
					</select>
					<input type="hidden" name="report" value="monthly_report">
					<input type="submit" value="Generate PDF">
					
				</p>
			</form></div>
		</div>
	</div>
</section>