<?php
defined('R88PROJ') or die ($system_error);
?>
<header id="header">
	<?php 
	if($module != 'dashboard'){
		echo '<div class="header-back"><a href="?"><img src="images/btn_back.png"></a></div>';
	}
	?>	
	<div class="header-logo"><a href="?"><img src="images/img-logo2.png"></a></div>
	<?php
	if($_SESSION['type'] > 1){
	?>
	<div class="header-birthday" id="birthday">
		<img src="images/birthday.png">
		<ul id="birthday-list">
			<li><span>S001</span>Nattapong<br>Kittirattanakowit</li>
			<li><span>S001</span>Nattapong<br>Kittirattanakowit</li>
			<li><span>S001</span>Nattapong<br>Kittirattanakowit</li>
		</ul>
	</div>
	<?php
	}
	?>
	<div class="header-user-info">
		<a href="?logout"><?php echo $txt_logout; ?></a><br>
		<?php echo $txt_welcome; ?>
		<a href="?mod=profile">
			<?php echo $_SESSION['user_name']; ?>
		</a>
	</div>
</header>
<section class="content">