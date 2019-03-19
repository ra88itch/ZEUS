<?php
defined('R88PROJ') or die ($system_error);
?>
<section id="sec-login">
	<div class="warp">
		<div class="login-box">
			<div class="login-logo">
				<img src="images/img-logo.png">
			</div>
			<div class="login-form">
				<p><label><?php echo $txt_username; ?></label><input type="text" id="username"></p>
				<p><label><?php echo $txt_password; ?></label><input type="password" id="password"></p>
			</div>
			<div class="login-submit">
				<p><?php echo $txt_login; ?></p>
			</div>
		</div>
	</div>
</section>
<section id="sec-login-dev">Devolope by <a href="http://ra88itch.com">ra88itch.com</a></section>