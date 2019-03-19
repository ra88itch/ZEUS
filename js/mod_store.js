var mod = '?mod=stock';
$(document).ready(function(){
	$('#add').click(function(){
		ajaxCall( 'api.php', addResponsed, { mod:'store', type: 'add' } );
	});	
	$('.edit').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('edit','');
		ajaxCall( 'api.php', editResponsed, { mod:'store', stock_id: thisID, type: 'edit' } );
	});	
	$('[id^=increase]').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('increase','');
		ajaxCall( 'api.php', transactionResponsed, { mod:'store', stock_id: thisID, type: 'transaction', dotype : 1 } );
	});	
	$('[id^=decrease]').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('decrease','');
		ajaxCall( 'api.php', transactionResponsed, { mod:'store', stock_id: thisID, type: 'transaction', dotype : 0 } );
	});	
	$('[id^=stock]').click(function(){
		var thisID	=	$(this).attr('id');
		thisID		=	thisID.replace('stock','');
		ajaxCall( 'api.php', historyResponse, { mod:'store', stock_id: thisID, type: 'history' } );
	});
	$('#filter').keyup(function(){		
		var thisVal = $('#filter').val();
		if(thisVal!=''){
			$('.row').hide();
			$('.row:contains('+thisVal+')').show();
		}else{
			$('.row').show();
		}
	});
});

function addResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var code		=	$('#code').val();
			var name		=	$('#name').val();
			var unit		=	$('#unit').val();
			var new_unit	=	$('#new_unit').val();
			var minimum		=	$('#minimum').val();
			
			if(name==''){
				alert('Stock name is empty.');
				return false;
			}
			ajaxCall( 'api.php', addNewResponsed, { mod:'store', type: 'addNewStock', code: code, name: name, unit: unit, new_unit:new_unit, minimum:minimum} );
		});	
	}else{
		alert(response.msg);
	}
}
function editResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var code		=	$('#code').val();
			var name		=	$('#name').val();
			var unit		=	$('#unit').val();
			var stock_id	=	$('#stock_id').val();
			var minimum		=	$('#minimum').val();
			var amount		=	$('#amount').val();
			if(name == ''){
				alert('กรุณาใส่ชื่อรายการ');
				return false;
			}
			ajaxCall( 'api.php', editStockResponsed, { mod:'store', type: 'updateStock', stock_id: stock_id, code: code, name:name, unit: unit, minimum:minimum, amount:amount } );			
		});	
	}else{
		alert(response.msg);
	}
}
function transactionResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var stock_id	=	$('#stock_id').val();
			var quantity	=	$('#quantity').val();
			var dotype		=	$('#dotype').val();
			var	amount		=	$('#amount').html();
			var	comment		=	$('#comment').val();
			var employee_position = 0;
			if(quantity == 0 || quantity == ''){
				alert('จำนวนไม่ถูกต้อง');
				return false;
			} else {
				if(dotype == 0) {
					employee_position = $('#employee_position').val();
					if(quantity > parseFloat(amount)) {
						console.log(quantity,amount);
						alert('รายการไม่เพียงพอ');
						return false;
					}
				}
			}
			
			ajaxCall( 'api.php', addTransactionResponsed, { mod:'store', type: 'addNewTransaction', stock_id: stock_id, quantity: quantity, dotype: dotype, employee_position:employee_position, comment:comment} );
		});	
	}else{
		alert(response.msg);
	}
}
function historyResponse(response){
	if(response.process == 'success') {
		setPop(response.html);
	} else {
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
	alert(response.msg);
	if(response.process == 'success'){
		location.reload(mod);
	}
}
function editStockResponsed(response){
	alert(response.msg);
	if(response.process == 'success'){
		location.reload(mod);
	}
}
function addTransactionResponsed(response){
	alert(response.msg);
	if(response.process == 'success'){
		location.reload(mod);
	}
}
function imageIsLoaded(e) {
	$('#img_preview').attr('src', e.target.result);
	$('#img_preview').attr('width', '250px');
};