var mod = '?mod=coupon';
$(document).ready(function(){
	$('.coupon').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('coupon', '');
		showForm(thisID);
	});	
});
function setPop(html){
	$('#pop > .warp').html(html);
	$('#pop').show();
	$('#pop #cancel').click(function(){
		$('#pop').hide();
		$('#pop > .warp').html('');		
	});
}
function showForm(couponID){
	var html = '<h3 class="title">ซื้อคูปอง</h3>';
	html += '<ul class="form">';
	html += '<li><span class="label">ระบุจำนวน</span><input type="text" id="unit"></li>';
	html += '</ul>';
	html += '<div class="submit"><input type="button" id="submit" value="ยืนยัน"> or <span id="cancel">ยกเลิก</span></div>';

	setPop(html);
	$('#submit').click(function(){
		var unit			= $('#unit').val();
		if(unit==''){
			alert('กรุณากรอกจำนวน');
			return false;
		}
		ajaxCall( 'api.php', addOrderResponsed, { mod:'coupon', type: 'addToOrder', couponID:couponID, unit: unit } );
	});	
}
function addOrderResponsed(response){
	if(response.process == 'success'){
		alert(response.msg);
		location.reload(mod);
	}else{
		alert(response.msg);
	}
}