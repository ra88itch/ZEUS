<?php
defined('R88PROJ') or die ($system_error);

if($_SESSION['type']==1) {
	$cook_sql = "SELECT * FROM `menu_type_cooking` ORDER BY id ASC";
	$cook_query = mysql_query($cook_sql);
	$cook_option = '';
	while($cook_results = mysql_fetch_assoc($cook_query)) {
		$cook_option.= "<option value = ".$cook_results['id'].">".$cook_results['type_cooking']."</option>";
	}
	
	$meat_sql = "SELECT * FROM `menu_type_meat` ORDER BY id ASC";
	$meat_query = mysql_query($meat_sql);
	$meat_option = '';
	while($meat_results = mysql_fetch_assoc($meat_query)) {
		$meat_option.= "<option value=".$meat_results['id'].">".$meat_results['type_meat']."</option>";
	}
}
?>
<div id = "container">
	<? if($_SESSION['type']==1) { ?>
	<div id="add_menu" class="pageBox">
		<div class="label_header">Add Menu</div>
		<div class="menu" id="add_menu_field">
			<form id = "add_menu_form" method="post" enctype="multipart/form-data" action = "?dev&mod=addmenu">
				<span class="label">Menu Code</span>
				<input type="text" id="menu_code" name="menu_code"><br><br>
				<span class="label">Menu Name TH</span>
				<input type="text" id="menu_name_th" name="menu_name_th"><br><br>
				<span class="label">Menu Name EN</span>
				<input type="text" id="menu_name_en" name = "menu_name_en"><br><br>
				<span class="label">Menu Image</span>
				<input type="file" name="menu_img" id = "menu_img"><br><br>
				<span class="label">Menu Description</span>
				<textarea id="menu_desc" name = "menu_desc"></textarea><br><br>
				<span class="label">Type By Cook</span>
				<select id = "type_by_cook" name="type_by_cook">
					<?php echo $cook_option;?>
				</select><br><br>
				<span class="label">Type By Meat</span>
				<select id = "type_by_meat" name="type_by_meat">
					<?php echo $meat_option;?>
				</select><br><br>
				<span class="label">Price</span>
				<input type="text" id="price" name="price"></br><br>
				<span class="label">Unit</span>
				<input type="text" id="unit" name="unit"></br><br>
				<span class="label">Spacial</span>
				<select id="spacial" name="spacial">
					<option value = "0">Disable</option>
					<option value = "1" selected>Enable</option>
				</select></br><br>
				<span class="label">Active</span>
				<select id="active" name="active">
					<option value = "0">Disable</option>
					<option value = "1" selected>Enable</option>
				</select></br><br>
				<input type="submit" id="add_menu_btn" value="Add Menu">
			</form>
		</div>
	</div>
	<? } ?>
	<div id="menu_food" class="pageBox">
		<div class="label_header">Food</div>
		<div class="menu" id="listMenu"></div>
	</div>
	
	<div id="menu_drink" class="pageBox">
		<div class="label_header">Drink</div>
		<div class="menu" id="listDrink"></div>
	</div>
	
	<div id="menu_detail" class="pageBox">
		<div class="label_header">Menu Detail</div>
		<div class="menu" id="menuDetail"></div>
	</div>
</div>