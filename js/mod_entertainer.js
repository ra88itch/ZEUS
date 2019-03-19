var mod = '?mod=entertainer';
$(document).ready(function(){
	$('#add').click(function(){
		ajaxCall( 'api.php', addResponsed, { mod:'entertainer', type: 'add' } );
	});	
	$('.edit').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('edit','');
		ajaxCall( 'api.php', editResponsed, { mod:'entertainer', entertainer_id: thisID, type: 'edit' } );
	});	
	$('.status').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('status','');
		ajaxCall( 'api.php', statusResponsed, { mod:'entertainer', entertainer_id: thisID, type: 'changeEntertainerStatus' } );
	});	
});
function addResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var entertainer_name		= $('#entertainer_name').val();
			var status					= $('#status').val();
			if(entertainer_name==''){
				alert('Entertainer name is empty.');
				return false;
			}
			ajaxCall( 'api.php', addNewResponsed, { mod:'entertainer', type: 'addNewEntertainer', entertainer_name: entertainer_name, status:status } );
		});	
	}else{
		alert(response.msg);
	}
}
function editResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var entertainer_name		= $('#entertainer_name').val();
			var entertainer_id			= $('#entertainer_id').val();
			var status					= $('#status').val();
			if(entertainer_name==''){
				alert('Entertainer name is empty.');
				return false;
			}
			ajaxCall( 'api.php', editAccountResponsed, { mod:'entertainer', type: 'updateEntertainer', entertainer_name: entertainer_name, entertainer_id:entertainer_id, status:status } );			
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
			$('#status'+response.entertainer).addClass('lock');			
		}else{
			$('#status'+response.entertainer).removeClass('lock');
		}
	}else{
		alert(response.msg);
	}
}