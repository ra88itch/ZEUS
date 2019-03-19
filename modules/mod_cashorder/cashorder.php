<?php
defined('R88PROJ') or die ($system_error);
?>
<section id="sec-cashorder">
	<div class="warp">
		<div id="customer-detail" class="result">
			<h3></h3>
			<h2 style="color:#fff;">ชื่อลูกค้า  <input type="text" id="name"></h2>
			<div>
				<table>
					<thead><tr><td>จำนวน</td><td>รายละเอียด</td><td>สถานะ</td></tr></thead>
					<tbody id="order_list">
					</tbody>
				</table>
				<div class="open" id = "chkbill" style="display:none">ยืนยันรายการ</div>
				<div class="open" id = "add">เพิ่มรายการอาหาร</div>
			</div>			
		</div>
		<div id="menu-list" class="result"></div>		
	</div>
</section>