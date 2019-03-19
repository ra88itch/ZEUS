<?php
defined('R88PROJ') or die($system_error);

$type = $_REQUEST['type'];
?>
<section id = "sec-snooker">
	<div class="warp">
		<div class="col12 search-zone">
			<h3>ใส่จำนวนลูกค้า</h3>
			<input type="text" name="snooker-volumn" id="snooker-volumn">
			<input type="button" id="snooker-search" value="ค้นหา">
		</div>
		<div id="zone-private" class="result"></div>
		<div id="zone-public" class="result"></div>
		<div id="booking" class="submit"></div>
		<?php if(isset($_GET['inv']) && $_GET['inv']!=''){ $inv = $_GET['inv']; }else{ $inv = '0'; } ?>
		<input type="hidden" name="invoice-id" id="invoice-id" value="<?php echo $inv; ?>">
	</div>
</section>