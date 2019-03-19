<?php
defined('R88PROJ') or die($system_error);

$type = $_REQUEST['type'];
?>
<style>

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
#customer-detail iframe{
	height:500px;
	overflow-y:scroll;
	width:100%;
}
#ecoupon-list,
#discount-list {
	background-color:steelblue;
	bottom:0px;
	height:0px;	
	padding:0px;
	position:fixed;
	transition: height 1s;
	width:100%;
}
#ecoupon-list #menu,
#discount-list #menu{
	height:80%;
	overflow-y:scroll;
}
#ecoupon-list .open,
#discount-list .open{
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
#ecoupon-list .close,
#discount-list .close {
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
#ecoupon-list.result.visible,
#discount-list.result.visible {
	height:730px;
	transition: height 1s;
}
#ecoupon-list.result .col3,
#discount-list.result .col3{
	height:105px;
}
#ecoupon_mobile {
	position:absolute;
	top:25px;
	left:130px;
}
#ecoupon_search {
	position:absolute;
	top:33px;
	left:340px;
}

</style>
<section id = "sec-order">
	<div class="warp">
		<div id="inv-list" class="result"></div>
		<div id="customer-detail" class="result" style="display:none;">
			<h3></h3>
			<div style="width:70%; float:left;"></div>
			<section style="width:27%; float:right;">
				<iframe id="print-frame" src="" frameborder="0"></iframe>
			</section>
		</div>
		<div id="discount-list" class="result"></div>
		<div id="ecoupon-list" class="result"></div>
	</div>
</section>