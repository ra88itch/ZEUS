var mod = '?mod=salary';
$(document).ready(function(){
	$('#add').click(function(){
		ajaxCall( 'api.php', addResponsed, { mod:'salary', type: 'add' } );
	});	
	
	$('.edit').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('edit','');
		ajaxCall( 'api.php', editResponsed, { mod:'salary', salaryID: thisID, type: 'edit' } );
	});	
	/*
	$('.status').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('status','');
		ajaxCall( 'api.php', statusResponsed, { mod:'salary', account_id: thisID, type: 'changeAccountStatus' } );
	});
	*/
});
function addResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var name				= $('#name').val();
			var start_date			= $('#start_date').val();
			var end_date			= $('#end_date').val();
			if(name==''||start_date==''||end_date==''){
				alert('Start or End Date Empty');
				return false;
			}
			ajaxCall( 'api.php', addNewResponsed, { mod:'salary', type: 'addNewSalary', name: name, start_date: start_date, end_date: end_date} );
		});	
	}else{
		alert(response.msg);
	}
}

function addNewResponsed(response){
	if(response.process == 'success'){
		alert(response.msg);
		location.reload(mod);
	}else{
		alert(response.msg);
	}
}

function editResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var	dayoff		=	$('#dayoff').val();
			var late		=	$('#latetime').val();
			var salaryID	=	$('#salaryDetailID').val();
			ajaxCall( 'api.php', editSalaryResponsed, { mod:'salary', type: 'updateSalary', salaryID: salaryID, dayoff:dayoff, late:late} );			
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
	$('#start_date').datepicker({ dateFormat: "yy-mm-dd" });
	$('#end_date').datepicker({ dateFormat: "yy-mm-dd" });
}

function editSalaryResponsed(response){
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