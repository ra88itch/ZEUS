var mod = '?mod=massager';
$(document).ready(function(){
	$('#add').click(function(){
		ajaxCall( 'api.php', addResponsed, { mod:'massager', type: 'add' } );
	});	
	$('.edit').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('edit','');
		ajaxCall( 'api.php', editResponsed, { mod:'massager', massager_id: thisID, type: 'edit' } );
	});	
	$('.status').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('status','');
		ajaxCall( 'api.php', statusResponsed, { mod:'massager', massager_id: thisID, type: 'changeAccountStatus' } );
	});	
});
function addResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var massager_no			= $('#massager_no').val();
			var nickname			= $('#nickname').val();
			var firstname			= $('#firstname').val();
			var lastname			= $('#lastname').val();
			var bank_account		= $('#bank_account').val();
			var status				= $('#status').val();
			if(massager_no=='' || nickname=='' || firstname=='' || lastname=='' || bank_account==''){
				alert('ข้อมูลไม่ครบ');
				return false;
			}
			ajaxCall( 'api.php', addNewResponsed, { mod:'massager', type: 'addNewAccount', massager_no: massager_no, nickname: nickname, firstname: firstname, lastname:lastname, bank_account:bank_account, status:status } );
		});	
	}else{
		alert(response.msg);
	}
}
function editResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var massager_id			= $('#massager_id').val();
			var massager_no			= $('#massager_no').val();
			var nickname			= $('#nickname').val();
			var firstname			= $('#firstname').val();
			var lastname			= $('#lastname').val();
			var bank_account		= $('#bank_account').val();
			var status				= $('#status').val();
			ajaxCall( 'api.php', editAccountResponsed, { mod:'massager', type: 'updateAccount', massager_id: massager_id, massager_no: massager_no, nickname: nickname, firstname: firstname, lastname:lastname, bank_account:bank_account, status:status } );			
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
function editAccountResponsed(response){
	if(response.process == 'success'){
		alert(response.msg);
		location.reload(mod);
	}else{
		alert(response.msg);
	}
}
function statusResponsed(response){
	if(response.process == 'success'){
		//alert(response.msg);
		if(response.active == 0){
			$('#status'+response.account).addClass('lock');			
		}else{
			$('#status'+response.account).removeClass('lock');
		}
	}else{
		alert(response.msg);
	}
}