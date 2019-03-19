<?php
defined('R88PROJ') or die($system_error);
?>
<style>
#order-detail table{
	background-color: rgba(255, 255, 255, 0.8);
    width: 100%;
}
#order-detail table thead {
	background-color: rgba(0, 0, 0, 0.4);
    width: 100%;
}
#order-detail table thead td {
	text-align: center;
}
#order-detail iframe {
	height: 500px;
    overflow-y: scroll;
    width: 100%;
}
</style>
<section id="sec-kitchen">
	<div class="warp">
		<div id="order-detail" class="result">
			<h3>รายการอาหาร</h3>
			<div style="width:70%; float:left;">
				<table>
					<thead>
						<tr>
							<td>จำนวน</td>
							<td>รายการอาหาร</td>
							<td>สถานะ</td>
						</tr>
					</thead>
					<tbody id="order_list">
						
					</tbody>
				</table>
			</div>
			<section style="width:27%; float:right;">
				<iframe frameborder="0" src="print-kitchen.php?inv=17" id="print-frame"></iframe>
			</section>
		</div>
	</div>
</section>