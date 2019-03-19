<?php
defined('R88PROJ') or die ('SYSTEM ERROR');

function getTypeCooking($cookingId){
	$cooking_sql = "SELECT `type_cooking` FROM `menu_type_cooking` WHERE `id` = '".$cookingId."'";
	$cooking_query = mysql_query($cooking_sql);
	$cooking_result = mysql_fetch_assoc($cooking_query);
	return $cooking_result['type_cooking'];
}
function getTypeMeat($meatId){
	$meat_sql = "SELECT `type_meat` FROM `menu_type_meat` WHERE `id` = '".$meatId."'";
	$meat_query = mysql_query($meat_sql);
	$meat_result = mysql_fetch_assoc($meat_query);
	return $meat_result['type_meat'];
}
function getUnitName($unitId){
	$unit_sql = "SELECT `unit_name` FROM `menu_unit` WHERE `id` = '".$unitId."'";
	$unit_query = mysql_query($unit_sql);
	$unit_result = mysql_fetch_assoc($unit_query);
	return $unit_result['unit_name'];
}
function listMenu(){
	$menu = array();	
	$sql = "SELECT * FROM `menu` WHERE `active` = '1'";
	$query = mysql_query($sql);	
	while($results = mysql_fetch_assoc($query)) {		
		array_push($menu,array(
				'id' 				=>	$results['id'],
				'menu_code'			=>	$results['menu_code'],
				'menu_name_th'		=>	$results['menu_name_th'],
				'menu_name_en'		=>	$results['menu_name_en'],
				'menu_image'		=>	$results['menu_image'],
				'menu_desc'			=>	$results['menu_desc'],
				'cooking_type'		=>	$results['type_by_cooking'],
				'meat_type'			=>	$results['type_by_meat'],
				'price'				=>	$results['price'],
				'unit'				=>	getUnitName($results['unit']),
				'special'			=>	$results['special']				
			)
		);	
	}

	$cooking_list = array();
	$cooking_sql = "SELECT * FROM `menu_type_cooking`";
	$cooking_query = mysql_query($cooking_sql);	
	while($results = mysql_fetch_assoc($cooking_query)) {		
		array_push($cooking_list,array(
				'id' 				=>	$results['id'],
				'type_cooking'		=>	$results['type_cooking']			
			)
		);	
	}

	$meat_list = array();
	$meat_sql = "SELECT * FROM `menu_type_meat`";
	$meat_query = mysql_query($meat_sql);	
	while($results = mysql_fetch_assoc($meat_query)) {		
		array_push($meat_list,array(
				'id' 			=>	$results['id'],
				'type_meat'		=>	$results['type_meat']			
			)
		);	
	}
	
	if(empty($menu)) {
		global $system_status_failed;
		$json_arr = array(
			'process' => $system_status_failed
		);
	} else {
		global $system_status_success;
		$json_arr = array(
			'process' => $system_status_success,
			'menu' => $menu,
			'cooking_list' => $cooking_list,
			'meat_list' => $meat_list

		);
	}
	//print_r($json_arr);
	returnJSON($json_arr);
}
chkSession();
listMenu();
?>