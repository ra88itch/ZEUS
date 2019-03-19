var mod = '?mod=account';
$(document).ready(function(){
	$('#add').click(function(){
		ajaxCall( 'api.php', addResponsed, { mod:'account', type: 'add' } );
	});	
	$('.edit').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('edit','');
		ajaxCall( 'api.php', editResponsed, { mod:'account', account_id: thisID, type: 'edit' } );
	});	
	$('.status').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('status','');
		ajaxCall( 'api.php', statusResponsed, { mod:'account', account_id: thisID, type: 'changeAccountStatus' } );
	});	
});
function addResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var username			= $('#username').val();
			var acc_class			= $('#class').val();
			var password			= $('#password').val();
			var cfmpassword	= $('#cfmpassword').val();
			var firstname			= $('#firstname').val();
			var lastname			= $('#lastname').val();
			var status				= $('#status').val();
			if(username==''){
				alert('Username is empty.');
				return false;
			}
			if(password=='' || password!=cfmpassword){
				alert('Password not match or empty.');
				return false;
			}
			ajaxCall( 'api.php', addNewResponsed, { mod:'account', type: 'addNewAccount', username: username, acc_class: acc_class, password: password, firstname: firstname, lastname:lastname, status:status } );
		});	
	}else{
		alert(response.msg);
	}
}
function editResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var account			= $('#account').val();
			var acc_class			= $('#class').val();
			var password			= $('#password').val();
			var cfmpassword	= $('#cfmpassword').val();
			var firstname			= $('#firstname').val();
			var lastname			= $('#lastname').val();
			var status				= $('#status').val();
			if(password!=cfmpassword){
				alert('Password not match.');
				return false;
			}
			ajaxCall( 'api.php', editAccountResponsed, { mod:'account', type: 'updateAccount', account: account, acc_class: acc_class, password: password, firstname: firstname, lastname:lastname, status:status } );			
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