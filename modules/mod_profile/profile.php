<?php
defined('R88PROJ') or die($system_error);

$type = $_REQUEST['type'];
?>
<section id = "sec-proflie">
	<div class="warp">
		<div class="col12 change-password">
			<label>ชื่อผู้ใช้งาน</label><?php echo $_SESSION['user_name']; ?><br><br>
			<label>รหัสผ่านเดิม</label><input type="password" id="old-password"><br><br>
			<label>รหัสผ่านใหม่</label><input type="password" id="new-password"><br><br>
			<label>ยืนยันรหัสผ่าน</label><input type="password" id="confirm-password"><br><br>
			<label></label><input type="button" id="change-password" value="แก้ไข"> / <a href="?mod=daily">กดเพื่อสรุปรายรับ</a>
		</div>
		<br class="clear">
	</div>
</section>