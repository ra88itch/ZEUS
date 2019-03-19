$(document).ready(function(){
	hideResult();
	$('#restaurant-volumn').focus();
	$('#restaurant-search').click(function(){
		search();		
	});
});
function search(){
	var volumn = $('#restaurant-volumn').val();
	if(volumn == ''){ volumn = 0; }
	$('.search-zone').slideUp(function(){		
		ajaxCall( 'api.php', searchResponse, { mod:'restaurant', volumn:volumn });
	});
}
function searchResponse(response){
	var zone = response.zone;
	var count = zone.length;

	var indoor = '<h3>ห้องแอร์</h3>';
	var outdoor = '<h3>ด้านนอก</h3>';
	for(var i=0; i < count; i++ ){
		var arr = zone[i];
		var html = '<div class="col3"><p id="zone'+arr.id+'">'+arr.zone+' ('+arr.zone_volumn+')</p></div>';
		if(arr.zone_category == '6'){
			indoor += html;
		}else{
			outdoor += html;
		}
	}
	$('#indoor').html(indoor);
	$('#outdoor').html(outdoor);
	$('#booking').html('<input type="button" id="restaurant-booking" class="submit" value="จองโต๊ะ">');

	showResult();
	zoneSelected();
	booking();
}
function zoneSelected(){
	$('.result .col3 p').click(function(){
		goback = 1;
		$(this).addClass(function( index, currentClass ) {
			var addedClass;
			if ( currentClass === '' ) {
				addedClass = 'selected';
			}else{
				$(this).removeClass('selected');
			}
			return addedClass;
		});
	});		
}
function booking(){
	$('#restaurant-booking').click(function(){
		if($(".result .col3 p.selected").length > 0){
			hideResult();
			var zoneValue = '';
			var customerValue = $('#restaurant-volumn').val();
			var invoiceID= $('#invoice-id').val();
			$(".result .col3 p.selected").each(function(){
				zoneValue += $(this).attr('id')+',';
			});
			ajaxCall( 'api.php', bookingResponse, { mod:'booking', zoneValue:zoneValue, customerValue:customerValue, type:'restaurant', invoiceID:invoiceID });
		}
	});		
}
function bookingResponse(response){
	$('#restaurant-volumn').val('');
	if(response.process == 'failed' ){
		alert('เลือกโต๊ะใหม่อีกครั้ง');
		hideResult();		
	}else{
		alert('จองโต๊ะเรียบร้อย');
	}
}
function showResult(){
	$('#indoor').slideDown();
	$('#outdoor').slideDown();
	$('#booking').slideDown();
	$('.search-zone').slideUp();
}
function hideResult(){
	$('#indoor').slideUp();
	$('#outdoor').slideUp();
	$('#booking').slideUp();
	$('.search-zone').slideDown();
}