var mod = '?mod=account';
$(document).ready(function(){
	$('#add').click(function(){
		ajaxCall( 'api.php', addResponsed, { mod:'massager_money', type: 'add' } );
	});	
	$('.edit').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('edit','');
		ajaxCall( 'api.php', editResponsed, { mod:'massager_money', profile_id: thisID, type: 'edit' } );
	});	
});
function addResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var date			= $('#date').val();
			var massager		= $('#massager').val();

			if(date=='' || massager==''){
				alert('ข้อมูลไม่ครบถ้วน');
				return false;
			}
			ajaxCall( 'api.php', addNewResponsed, { mod:'massager_money', type: 'addMassagerProfile', date: date, massager: massager } );
		});	
	}else{
		alert(response.msg);
	}
}
function editResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var date			= $('#date').val();
			var massager		= $('#massager').val();
			var profile_id		= $('#profile_id').val();
			if(date=='' || massager==''){
				alert('ข้อมูลไม่ครบถ้วน');
				return false;
			}
			ajaxCall( 'api.php', editAccountResponsed, { mod:'massager_money', type: 'editMassagerProfile', date: date, massager: massager, profile_id:profile_id } );			
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
	var d = new Date();
	var toDay = d.getDate() + '/' + (d.getMonth() + 1) + '/' + (d.getFullYear());
	$('#date').datepicker({ dateFormat: "yy-mm-dd" });
}
function addNewResponsed(response){
	if(response.process == 'success'){
		alert(response.msg);
		location.reload(mod);
	}else{
		alert(response.msg);
	}
}
function editAccountResponsed(response){
	if(response.process == 'success'){
		alert(response.msg);
		location.reload(mod);
	}else{
		alert(response.msg);
	}
}