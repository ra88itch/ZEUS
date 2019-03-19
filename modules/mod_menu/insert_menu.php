<?php
defined('R88PROJ') or die($system_error);

	$valid_formats = array("jpg", "png", "gif", "bmp");
	$code		=	$_POST['menu_code'];
	$name_th	=	$_POST['menu_name_th'];
	$name_en	=	$_POST['menu_name_en'];
	$desc		=	$_POST['menu_desc'];
	$cook		=	$_POST['type_by_cook'];
	$meat		=	$_POST['type_by_meat'];
	$price		=	$_POST['price'];
	$unit		=	$_POST['unit'];
	$spaciel	=	$_POST['spaciel'];
	$active		=	$_POST['active'];
	$path		=	"../../images/menu/";
	if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
	{
		$name = $_FILES['menu_img']['name'];
		$size = $_FILES['menu_img']['size'];
		 
		if(strlen($name)) {
			list($txt, $ext) = explode(".", $name);
			$ext = strtolower($ext);
			if(in_array($ext,$valid_formats)) {
				if($size<(1024*1024)) {
						$actual_image_name = time().substr(str_replace(" ", "_", $txt), 5).".".$ext;
						$tmp = $_FILES['menu_img']['tmp_name'];
							if(move_uploaded_file($tmp, $path.$actual_image_name)) {
									$sql = "INSERT INTO `menu` (`id`, `menu_code`, `menu_name_th`, `menu_name_en`, `menu_image`, `menu_desc`, `type_by_cooking`, `type_by_meat`, `price`, `unit`, `special`, `active`) VALUES (NULL,'".$code."','".$name_th."','".$name_en."','".$actual_image_name."','".$desc."','".$cook."','".$meat."','".$price."','".$unit."','".$spaciel."','".$active."')";
									echo $sql;
									$query = mysql_query($sql);
							}
							else
					echo "failed";
				}
			else
				echo "Image file size max 1 MB";
			}
		else
			echo "Invalid file format..";
		}
		else
		echo "Please select image..!";
		 
		exit;
	}
?>