<?php
defined('R88PROJ') or die($system_error);

$type = $_REQUEST['type'];
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
			<h3>รายรับแคชเชียร์ประจำวัน</h3>
			<div style="width:70%; float:left;">
				<input id="search" value="เลือกวันที่">
			</div>
			<section style="width:27%; float:right;">
				<iframe id="print-frame" src="" frameborder="0"></iframe>
			</section>
		</div>
	</div>
</section>