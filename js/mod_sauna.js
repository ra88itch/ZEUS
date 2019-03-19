$(document).ready(function(){
	hideResult();
	$('#sauna-volumn').focus();
	$('#sauna-search').click(function(){
		search();		
	});
});
function search(){
	var volumn = $('#sauna-volumn').val();
	if(volumn == ''){ volumn = 0; }
	$('.search-zone').slideUp(function(){		
		ajaxCall( 'api.php', searchResponse, { mod:'sauna', volumn:volumn });
	});
}
function searchResponse(response){
	var zone = response.zone;
	var count = zone.length;

	var sauna = '<h3>ซาวน่า</h3>';
	var fitness = '<h3>ฟิตเนส</h3>';
	var fitness_2 = '<h3>ฟิตเนสพนักงาน</h3>';
	for(var i=0; i < count; i++ ){
		var arr = zone[i];
		var html = '<div class="col3"><p id="zone'+arr.id+'">'+arr.zone+' ('+arr.zone_volumn+')</p></div>';
		if(arr.zone_category == '10'){
			sauna += html;
		}else if(arr.zone_category == '3'){
			fitness += html;
		}else{
			fitness_2 += html;
		}
	}
	$('#zone-sauna').html(sauna);
	$('#zone-fitness').html(fitness);
	$('#zone-fitness-2').html(fitness_2);
	$('#booking').html('<input type="button" id="sauna-booking" class="submit" value="จอง">');

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
	$('#sauna-booking').click(function(){
		if($(".result .col3 p.selected").length > 0){
			hideResult();
			var zoneValue = '';
			var customerValue = $('#sauna-volumn').val();
			var invoiceID= $('#invoice-id').val();
			$(".result .col3 p.selected").each(function(){
				zoneValue += $(this).attr('id')+',';
			});
			ajaxCall( 'api.php', bookingResponse, { mod:'booking', zoneValue:zoneValue, customerValue:customerValue, type:'sauna', invoiceID:invoiceID });
		}
	});		
}
function bookingResponse(response){
	$('#sauna-volumn').val('');
	if(response.process == 'failed' ){
		alert('เลือกใหม่อีกครั้ง');
		hideResult();		
	}else{
		alert('จองเรียบร้อย');
	}
}
function showResult(){
	$('#zone-sauna').slideDown();
	$('#zone-fitness').slideDown();
	$('#booking').slideDown();
	$('.search-zone').slideUp();
}
function hideResult(){
	$('#zone-sauna').slideUp();
	$('#zone-fitness').slideUp();
	$('#booking').slideUp();
	$('.search-zone').slideDown();
}