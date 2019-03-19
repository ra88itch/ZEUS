var mod = '?mod=zone_saunas';
$(document).ready(function(){
	$('.status').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('status','');
		ajaxCall( 'api.php', statusResponsed, { mod:'zone', zone_id: thisID, type: 'changeZoneStatus' } );
	});	
});
function statusResponsed(response){
	if(response.process == 'success'){
		if(response.active == 0){
			$('#status'+response.zone).addClass('lock');			
		}else{
			$('#status'+response.zone).removeClass('lock');
		}
	}else{
		alert(response.msg);
	}
}