$(document).ready(function(){
	$('#massage-volumn').focus();
	$('#massage-search').click(function(){
		search();
	});
});
function search(){
	var volumn = $('#massage-volumn').val();
	if(volumn == ''){ volumn = 0; }
	$('.search-zone').slideUp(function(){		
		ajaxCall( 'api.php', searchResponse, { mod:'massage', volumn:volumn });
	});
}
function searchResponse(response){

	var massager = response.massager;
	var massagerCount = massager.length;
	var massagerList = '<h3>พนักงาน</h3>';
	for(var i=0; i < massagerCount; i++ ){
		var arr = massager[i];
		var html = '<div class="col3"><p id="mass'+arr.id+'" class="massager"><img src="images/employee/'+arr.img+'"><span>'+arr.nickname+'</span></p></div>';
		massagerList += html;
	}
	$('#list-massager').html(massagerList);

	var zone = response.zone;
	var zoneCount = zone.length;
	var privateZone = '<h3>ห้อง VIP</h3>';
	var publicZone = '<h3>ห้องรวม</h3>';
	for(var i=0; i < zoneCount; i++ ){
		var arr = zone[i];
		var html = '<div class="col3"><p id="zone'+arr.id+'" class="room">'+arr.zone+' ('+arr.zone_volumn+')</p></div>';
		if(arr.zone_category == '2'){
			privateZone += html;
		}else{
			publicZone += html;
		}
	}
	$('#zone-private').html(privateZone);
	$('#zone-public').html(publicZone);
	$('#booking').html('<input type="button" id="massage-booking" class="submit" value="จอง">');

	showResult();
	zoneSelected();
	booking();
}
function zoneSelected(){
	$('.result .col3 p.massager').click(function(){
		goback = 1;
		$(this).addClass(function( index, currentClass ) {
			var addedClass;
			if ( currentClass === 'massager' ) {
				addedClass = 'selected';
			}else{
				$(this).removeClass('selected');
			}
			return addedClass;
		});
	});
	$('.result .col3 p.room').click(function(){
		goback = 1;
		$(this).addClass(function( index, currentClass ) {
			var addedClass;
			if ( currentClass === 'room' ) {
				addedClass = 'selected';
			}else{
				$(this).removeClass('selected');
			}
			return addedClass;
		});
	});
}
function booking(){
	$('#massage-booking').click(function(){
		if($(".result .col3 p.massager.selected").length >= $(".result .col3 p.room.selected").length && $(".result .col3 p.room.selected").length!=0){
			hideResult();
			var zoneValue = '';
			var customerValue = $('#massage-volumn').val();
			var invoiceID= $('#invoice-id').val();
			$(".result .col3 p.room.selected").each(function(){
				zoneValue += $(this).attr('id')+',';
			});
			var massengerValue = '';
			$(".result .col3 p.massager.selected").each(function(){
				massengerValue += $(this).attr('id')+',';
			});
			ajaxCall( 'api.php', bookingResponse, { mod:'booking', zoneValue:zoneValue, customerValue:customerValue, massengerValue:massengerValue, type:'massage', invoiceID:invoiceID });			
		}
	});		
}
function bookingResponse(response){
	$('#massage-volumn').val('');
	if(response.process == 'failed' ){
		alert('จองใหม่อีกครั้ง');
	}else{
		alert('จองเรียบร้อย')
	}
	hideResult();
}
function showResult(){
	$('#list-massager').slideDown();
	$('#zone-private').slideDown();
	$('#zone-public').slideDown();
	$('#booking').slideDown();
	$('.search-zone').slideUp();
}
function hideResult(){
	$('#list-massager').slideUp();
	$('#zone-private').slideUp();
	$('#zone-public').slideUp();
	$('#booking').slideUp();
	$('.search-zone').slideDown();
}