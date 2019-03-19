var mod = '?mod=locker';
$(document).ready(function(){
	$('#add').click(function(){
		ajaxCall( 'api.php', addResponsed, { mod:'locker', type: 'add' } );
	});
	$('.member').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('locker','');
		ajaxCall( 'api.php', addNewResponsed, { mod:'booking', locker_id: thisID, type: 'locker' } );
	});	
	$('.edit').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('edit','');
		ajaxCall( 'api.php', editResponsed, { mod:'locker', locker_id: thisID, type: 'edit' } );
	});	
	$('.status').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('status','');
		ajaxCall( 'api.php', statusResponsed, { mod:'locker', locker_id: thisID, type: 'changeMemberStatus' } );
	});	
	$('.renew').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('renew','');
		ajaxCall( 'api.php', renewResponsed, { mod:'locker', locker_id: thisID, type: 'renewLocker' } );
	});	

});
function addResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var firstname			= $('#firstname').val();
			var lastname			= $('#lastname').val();
			var phone				= $('#phone').val();
			var address				= $('#address').val();
			var locker_type			= $('#locker_type').val();
			var locker_no			= $('#locker_no').val();
			if(firstname==''){
				alert('กรุณาเติมชื่อ');
				return false;
			}
			ajaxCall( 'api.php', addNewResponsed, { 
				mod:'locker', 
				type: 'addNewMember',
				firstname: firstname,
				lastname: lastname,
				phone: phone,
				address: address,
				locker_type: locker_type,
				locker_no: locker_no
			});
		});	
	}else{
		alert(response.msg);
	}
}
function editResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var locker_id			= $('#locker_id').val();
			var firstname			= $('#firstname').val();
			var lastname			= $('#lastname').val();
			var phone				= $('#phone').val();
			var address				= $('#address').val();
			var locker_no			= $('#locker_no').val();
			if(firstname==''){
				alert('กรุณาเติมชื่อ');
				return false;
			}
			ajaxCall( 'api.php', editMemberResponsed, { 
				mod:'locker', 
				type: 'updateMember', 
				locker_id: locker_id, 
				firstname: firstname,
				lastname: lastname,
				phone: phone,
				address: address,
				locker_no: locker_no
			});			
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
function addNewResponsed(response){
	if(response.process == 'success'){
		alert(response.msg);
		location.reload(mod);
	}else{
		alert(response.msg);
	}
}
function editMemberResponsed(response){
	if(response.process == 'success'){
		alert(response.msg);
		location.reload(mod);
	}else{
		alert(response.msg);
	}
}
function statusResponsed(response){
	if(response.process == 'success'){
		if(response.active == 0){
			$('#status'+response.locker).addClass('lock');			
		}else{
			$('#status'+response.locker).removeClass('lock');
		}
	}else{
		alert(response.msg);
	}
}
function renewResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var locker_id			= $('#locker_id').val();
			var start_date			= $('#expire').val();
			ajaxCall( 'api.php', renewSubmit, { 
				mod:'locker', 
				type: 'renewLockerSubmit', 
				locker_id: locker_id, 
				start_date: start_date
			});			
		});	
	}else{
		alert(response.msg);
	}

}

function renewSubmit(response){
	if(response.process == 'success'){
		alert(response.msg);
		location.reload(mod);
	}else{
		alert(response.msg);
	}
}