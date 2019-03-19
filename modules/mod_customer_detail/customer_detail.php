<?php
defined('R88PROJ') or die($system_error);

$type = $_REQUEST['type'];
?>
<style>
#menu-list {
	background-color:steelblue;
	bottom:0px;
	height:0px;	
	padding:0px;
	position:fixed;
	transition: height 1s;
	width:100%;
}
#menu-list #menu {
	height:80%;
	overflow-y:scroll;
}
#menu-list .open {
	border: 1px solid #fff;
    color: #fff;
    cursor: pointer;
    display: block;
    padding: 8px 0;
    position: absolute;
    right: 10px;
    text-align: center;
    top: -30px;
    width: 40px;
}
#menu-list .close {
	border: 1px solid #fff;
    color: #fff;
    cursor: pointer;
    display: block;
    padding: 8px 0;
    position: absolute;
    right: 10px;
    text-align: center;
    top: 10px;
    width: 40px;
}
#menu-list.result.visible {
	height:730px;
	transition: height 1s;
}
#menu-list.result .col3 {
	height:105px;
}
#sec-order .result .col3 {
	height:105px;
}
#customer-detail table {
	background-color: rgba(255, 255, 255, 0.8);
	width:100%;
}
#customer-detail table thead{
	background-color: rgba(0, 0, 0, 0.4);
	width:100%;
}
#customer-detail table thead td{
	text-align:center;
}
#customer-detail .open {
	color:#fff;
	cursor:pointer;
}
#menu-list > select,
#menu-list > #search_menu {
	font-size:1.2em;
	padding:10px 5px;
	position:absolute;
	top:20px;
}
#menu-list #cooking_list {
	left:550px;
}
#menu-list #meat_list {
	left:500px;
}
#menu-list > #search_menu {
	left:270px;
}
#menu-list #show_all {
	background-color:#fff;
	border:1px solid #ddd;
	border-radius:3px;
	cursor:pointer;
	font-size:1.2em;
	left:780px;
	padding:10px 5px;
	position:absolute;
	top:20px;
}
</style>
<section id = "sec-order">
	<div class="warp">
		<div id="inv-list" class="result"></div>
		<div id="customer-detail" class="result">
			<h3></h3>
			<div></div>
		</div>
		<div id="menu-list" class="result"></div>
	</div>
</section>