$(document).ready(function(){
	$('#snooker-volumn').focus();
	$('#snooker-search').click(function(){
		search();
	});
});
function search(){
	var volumn = $('#snooker-volumn').val();
	if(volumn == ''){ volumn = 0; }
	$('.search-zone').slideUp(function(){		
		ajaxCall( 'api.php', searchResponse, { mod:'snooker', volumn:volumn });
	});
}
function searchResponse(response){
	var zone = response.zone;
	var count = zone.length;

	var privateZone = '<h3>ห้อง VIP</h3>';
	var publicZone = '<h3>ห้องรวม</h3>';
	for(var i=0; i < count; i++ ){
		var arr = zone[i];
		var html = '<div class="col3"><p id="zone'+arr.id+'">'+arr.zone+' ('+arr.zone_volumn+')</p></div>';
		if(arr.zone_category == '9'){
			privateZone += html;
		}else{
			publicZone += html;
		}
	}
	$('#zone-private').html(privateZone);
	$('#zone-public').html(publicZone);
	$('#booking').html('<input type="button" id="snooker-booking" class="submit" value="จองโต๊ะ">');

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
	$('#snooker-booking').click(function(){
		if($(".result .col3 p.selected").length > 0){
			hideResult();
			var zoneValue = '';
			var customerValue = $('#snooker-volumn').val();
			var invoiceID= $('#invoice-id').val();
			$(".result .col3 p.selected").each(function(){
				zoneValue += $(this).attr('id')+',';
			});
			ajaxCall( 'api.php', bookingResponse, { mod:'booking', zoneValue:zoneValue, customerValue:customerValue, type:'snooker', invoiceID:invoiceID });
		}
	});		
}
function bookingResponse(response){
	$('#snooker-volumn').val('');
	if(response.process == 'failed' ){
		alert('เลือกโต๊ะใหม่อีกครั้ง');
		hideResult();		
	}else{
		alert('จองโต๊ะเรียบร้อย')
		hideResult();
	}
}
function showResult(){
	$('#zone-private').slideDown();
	$('#zone-public').slideDown();
	$('#booking').slideDown();
	$('.search-zone').slideUp();
}
function hideResult(){
	$('#zone-private').slideUp();
	$('#zone-public').slideUp();
	$('#booking').slideUp();
	$('.search-zone').slideDown();
}