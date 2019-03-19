<?php
defined('R88PROJ') or die($system_error);

$hostname = "localhost";
$database = "zeus_sys";
$username = "root";
$password = '';

$conn = mysql_connect($hostname,$username,$password) or die($system_db_conn);
mysql_select_db($database, $conn) or die($system_db_name);
mysql_query('SET NAMES UTF8');
?>