<?php
defined('R88PROJ') or die($system_error);

$type = $_REQUEST['type'];
?>
<section id = "sec-sauna">
	<div class="warp">
		<div class="col12 search-zone">
			<h3>ใส่จำนวนลูกค้า</h3>
			<input type="text" name="sauna-volumn" id="sauna-volumn">
			<input type="button" id="sauna-search" value="ค้นหา">
		</div>
		<div id="zone-sauna" class="result"></div>
		<div id="zone-fitness" class="result"></div>
		<div id="zone-fitness-2" class="result"></div>
		<div id="booking" class="submit"></div>
		<?php if(isset($_GET['inv']) && $_GET['inv']!=''){ $inv = $_GET['inv']; }else{ $inv = '0'; } ?>
		<input type="hidden" name="invoice-id" id="invoice-id" value="<?php echo $inv; ?>">
	</div>
</section>