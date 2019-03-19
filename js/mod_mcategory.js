var mod = 'mcategory';
$(document).ready(function(){	
	$('#add').click(function(){
		ajaxCall( 'api.php', addResponsed, { mod:'mcategory', type: 'add' } );
	});	
	$('.edit').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('edit','');
		ajaxCall( 'api.php', editResponsed, { mod:'mcategory', category_id: thisID, type: 'edit' } );
	});	
	$('.statusmember').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('member','');
		ajaxCall( 'api.php', statusResponsed, { mod:'mcategory', category_id: thisID, type: 'changeMemberStatus' } );
	});
	$('.statusemployee').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('employee','');
		ajaxCall( 'api.php', statusResponsed, { mod:'mcategory', category_id: thisID, type: 'changeEmployeeStatus' } );
	});
});

function addResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var category_name				= $('#category_name').val();
						
			if(category_name==''){
				alert('กรุณากรอก');
				return false;
			}		
			ajaxCall( 'api.php', addNewResponsed, { mod:'mcategory', type: 'addNewCategory', category_name: category_name } );
					
		});	
	}else{
		alert(response.msg);
	}
}
function editResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var category_id					= $('#category_id').val();
			var category_name				= $('#category_name').val();
						
			if(category_name==''){
				alert('กรุณากรอก');
				return false;
			}		
			ajaxCall( 'api.php', addNewResponsed, { mod:'mcategory', type: 'updateCategory', category_id: category_id, category_name: category_name } );
					
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
		location.reload(mod);
	}else{
		alert(response.msg);
	}
}
function statusResponsed(response){
	if(response.process == 'success'){
		if(response.active == 0){
			$('#'+response.perm).addClass('lock');			
		}else{
			$('#'+response.perm).removeClass('lock');
		}
	}else{
		alert(response.msg);
	}
}