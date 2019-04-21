<?php defined('R88PROJ') or die ($system_error); ?>
<style>
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
<section id="sec-member">
	<div class="warp">
		<div class="col12 search-zone">
			<h3>กรอกหมายเลขโทรศััพท์ / ชื่อ</h3>
			<input type="text" name="ecoupon-mobile" id="ecoupon-mobile">
			<input type="button" id="ecoupon-search" value="ค้นหา">
		</div>
		<div id="zone-myTicket" class="result"></div>
		<div id="zone-chooseTicket" class="result"></div>
		<div id="booking" class="submit"></div>

		<div id="ecoupon-list" class="result"></div>
	</div>
</section>