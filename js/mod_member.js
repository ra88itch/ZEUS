var mod = '?mod=member';
$(document).ready(function(){
	$('#add').click(function(){
		ajaxCall( 'api.php', addResponsed, { mod:'member', type: 'add' } );
	});
	$('.member').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('member','');
		ajaxCall( 'api.php', addNewResponsed, { mod:'booking', member_id: thisID, type: 'member' } );
	});	
	$('.edit').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('edit','');
		ajaxCall( 'api.php', editResponsed, { mod:'member', member_id: thisID, type: 'edit' } );
	});	
	$('.status').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('status','');
		ajaxCall( 'api.php', statusResponsed, { mod:'member', member_id: thisID, type: 'changeMemberStatus' } );
	});	

});
function addResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var firstname			= $('#firstname').val();
			var lastname			= $('#lastname').val();
			var dob					= ($('#year').val()-543)+'-'+$('#month').val()+'-'+$('#date').val();
			var email				= $('#email').val();
			var phone				= $('#phone').val();
			var address				= $('#address').val();
			var type				= $('#customer_type').val();
			var unit				= $('#unit').val();
			var status				= $('#status').val();
			var cardID				= $('#cardID').val();
			if(firstname==''){
				alert('กรุณาเติมชื่อ');
				return false;
			}
			if($('#year').val()=='' || $('#year').val() < 2450 || $('#year').val() > 2600){
				alert('ปีเกิดไม่ถูกต้อง');
				return false;
			}
			if($('#month').val()=='' || ($('#month').val() < 0 && $('#month').val() > 13)){
				alert('เดือนเกิดไม่ถูกต้อง');
				return false;
			}
			if($('#date').val()=='' || ($('#date').val() < 0 && $('#date').val() > 32)){
				alert('วันเกิดไม่ถูกต้อง');
				return false;
			}
			ajaxCall( 'api.php', addNewResponsed, { 
				mod:'member', 
				type: 'addNewMember',
				firstname: firstname,
				lastname: lastname,
				dob: dob,
				email: email,
				phone: phone,
				address: address,
				cus_type: type,
				unit: unit,
				status: status,
				cardID: cardID
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
			var memberID			= $('#member').val();
			var firstname			= $('#firstname').val();
			var lastname			= $('#lastname').val();
			var dob					= ($('#year').val()-543)+'-'+$('#month').val()+'-'+$('#date').val();
			var email				= $('#email').val();
			var phone				= $('#phone').val();
			var address				= $('#address').val();
			var type				= $('#customer_type').val();
			var status				= $('#status').val();
			var cardID				= $('#cardID').val();

			if($('#year').val() < 2450 || $('#year').val() > 2600){
				alert('ปีเกิดไม่ถูกต้อง');
				return false;
			}
			if($('#month').val() < 0 || $('#month').val() > 13){
				alert('เดือนเกิดไม่ถูกต้อง');
				return false;
			}
			if($('#date').val() < 0 || $('#date').val() > 32){
				alert('วันกิดไม่ถูกต้อง');
				return false;
			}
			ajaxCall( 'api.php', editMemberResponsed, { 
				mod:'member', 
				type: 'updateMember', 
				memberID: memberID, 
				firstname: firstname,
				lastname: lastname,
				dob: dob,
				email: email,
				phone: phone,
				address: address,
				cus_type: type,
				status: status,
				cardID: cardID
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
	/*var d = new Date();
	var toDay = d.getDate() + '/' + (d.getMonth() + 1) + '/' + (d.getFullYear());
	$('#dob').datepicker({ dateFormat: "yy-mm-dd" });*/
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
			$('#status'+response.member).addClass('lock');			
		}else{
			$('#status'+response.member).removeClass('lock');
		}
	}else{
		alert(response.msg);
	}
}