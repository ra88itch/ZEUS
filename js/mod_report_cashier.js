$(document).ready(function(){
	//createPopup();

	$('#search').click(function(){
		//createPopup();
	});
});
function createPopup(response){
	
	var html = '<h3 class="title">เลือกวันที่ ที่ต้องการดึงข้อมูลจากระบบ</h3>';
	html += '<ul class="form">';
	html += '<li><span class="label">เลือกวันที่</span>วันที่ <input type="text" id="date" maxlength="2" value="" style="width:70px; margin-right:10px;" &nbsp;>เดือน <input type="text" id="month" maxlength="2" value="" style="width:70px; margin-right:10px;" &nbsp;>ปี <input type="text" id="year" maxlength="4" value="" style="width:70px; margin-right:10px;" &nbsp;></li>';
	html += '</ul>';
	
	html += '<div class="submit"><input type="button" id="submit" value="แสดง"> / <span id="cancel">ยกเลิก</span></div>';

	$('#pop .warp').html(html);
	$('#pop').show();

	$('#pop #cancel').unbind('click');
	$('#pop #cancel').click(function(){
		$('#pop').hide();
	});

	$('#pop #submit').unbind('click');
	$('#pop #submit').click(function(){
		var date = ($('#year').val()-543)+'-'+$('#month').val()+'-'+$('#date').val();
		ajaxCall( 'api.php', setdayResult, { mod:'report_cashier', type:'dayResult', date:date });
		//$('#customer-detail iframe').attr('src', 'daily.php?date='+date);
		//ajaxCall( 'api.php', setBill, { mod:'daily', type:'dayResult', date:date });
		//ajaxCall( 'api.php', nowOrderResponse, { mod:'customer_detail', type:'addOrder', invoiceID:invoiceID, units:order_units, menuID:order_id, takehome:order_takehome, orderDesc:order_desc });
		$('#pop').hide();
	});
}


function setdayResult(response){
	if(response.process == 'success'){		
		$('.result > .col12 > p').html(response.html);
	}
}