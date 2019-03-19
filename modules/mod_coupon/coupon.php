<?php
defined('R88PROJ') or die ($system_error);
?>
<section id="sec-coupon">
	<div class="warp">
		<div class="result">
		<?php 
		$sql = "SELECT * FROM `coupon` WHERE `active`='1'";
		$query = mysql_query($sql);
		$i = 0;
		while($rows = mysql_fetch_assoc($query)){
			$i++;
		?>
			<div class="col3">
				<p id="coupon<?php echo $rows['id']; ?>" class="coupon"><?php echo $i, '. ', $rows['coupon_name']; ?></p>
			</div>	
		<?php } ?>
		</div>
	</div>
</section>