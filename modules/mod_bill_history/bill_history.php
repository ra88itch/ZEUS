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
		<div class="result">
			<div class="col12"><p>
				เลือกวันที่ 
				<select id="dates">
				<?php
					$html = '';
					$sql = "SELECT DISTINCT DATE(`checkout`) AS `checkout_date` FROM `invoice` WHERE (DATE(`checkin`)>=subdate(current_date, 90)) AND `checkout` != '0000-00-00 00:00:00' ORDER BY `checkin` DESC";
					$query = mysql_query($sql);	
					while($results = mysql_fetch_assoc($query)) {
						
						$html .= '<option value="'.$results['checkout_date'].'">'.$results['checkout_date'].'</option>'	;
					}
					echo $html;
				?>
				</select>
			</p></div>
		</div>
		<div id="inv-list" class="result"></div>
		<div id="customer-detail" class="result">
			<h3></h3>
			<div style="width:70%; float:left;"></div>
			<section style="width:27%; float:right;">
				<iframe id="print-frame" src="" frameborder="0"></iframe>
			</section>
		</div>
		<div id="menu-list" class="result"></div>
	</div>
</section>