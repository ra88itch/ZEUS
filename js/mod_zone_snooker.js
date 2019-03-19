var mod = '?mod=zone_snooker';
$(document).ready(function(){
	$('.edit').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('edit','');
		ajaxCall( 'api.php', editForm, { mod:'zone_snooker', zone: thisID, type: 'getControlForm' } );
	});	
	$('.status').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('status','');
		ajaxCall( 'api.php', statusResponsed, { mod:'zone_snooker', zone: thisID, type: 'changeStatus' } );
	});	
});
function editForm(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var extra			= $('#extra').val();
			var zone			= $('#zone').val();
			ajaxCall( 'api.php', editResponsed, { mod:'zone_snooker', type: 'setControl', zone: zone, extra: extra } );		
		});	
	}else{
		alert(response.msg);
	}
}
function setPop(html){
	$('#pop > .warp').html(html);
	$('#pop').show();
	$('#pop #cancel').click(function(){
		$('#pop').hide();
		$('#pop > .warp').html('');		
	});
}
function editResponsed(response){
	if(response.msg){
		alert(response.msg);
	}
}
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