$(document).ready(function(){
	getOrderList();
	setInterval(function() {
		getOrderList();
	}, 8000);	
});

function getOrderList(){
	ajaxCall( 'api.php', setOrderList, { mod:'k_kitchen', type:'getOrderList' });
}
function setOrderList(response){	
	if(response.process == 'success'){	
		var show_printed = 0;
		var html = '';
		var details = response.details;
		var count = details.length;
		for(var i=0; i < count; i++ ){
			var arr = details[i];
			var takeHome = '';
			var cooking = '';
			var already = '';
			var finish = '';
			var cancel = '';
			if(arr.take_home == '1'){
				takeHome = ' / กลับบ้าน';
			}
			if(show_printed=='0' && arr.printed == '0'){
				show_printed = arr.id;
			}
			/*if(arr.order_status < 3){
				cooking = '<input id="cooking'+arr.id+'" class="cooking" type="button" value="กำลังปรุง"> ';
			}*/
			if(arr.order_status < 4){
				already = '<input id="already'+arr.id+'" class="already" type="button" value="ปรุงเสร็จ"> ';
			}
			if(arr.order_status == 7){
				cancel = 'ลูกค้ายกเลิกรายการ - <input id="deleteOrder'+arr.id+'" class="deleteOrder" type="button" value="ลบรายการ"> ';
			}else{
				cancel = '<input id="cancel'+arr.id+'" class="cancel" type="button" value="วัตถุดิบหมด"> ';
			}
			
			
			var bttn = cooking+already+finish+cancel;
			html += '<tr><td>'+arr.unit+'</td><td id="order'+arr.id+'" class="order" style="cursor:pointer;">['+arr.zone_name+'] '+arr.order_name+takeHome+' - '+arr.order_start+'</td><td>'+bttn+'</td></tr>';
		}
		$('#order_list').html(html);
		if(show_printed!='0'){
			$('#order-detail iframe').attr('src', 'print-kitchen.php?order_id='+show_printed);
		}
		
	}else{
		$('#order_list').html('');
		setInterval(function() {
			//location.reload();
			window.location.replace("?mod=kitchen");
		}, 10000);
	}
	
	// cooking,already,finish,cancel
	$('.order').click(function(){
		$('#order-detail iframe').attr('src', '');
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		thisID = thisID.replace('order','');
		$('#order-detail iframe').attr('src', 'print-kitchen.php?order_id='+thisID);
	});	
	$('.cooking').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		thisID = thisID.replace('cooking','');
		ajaxCall( 'api.php', changeOrderStatusResp, { mod:'k_kitchen', type:'cooking', orderID:thisID });
	});	
	$('.already').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		thisID = thisID.replace('already','');
		ajaxCall( 'api.php', changeOrderStatusResp, { mod:'k_kitchen', type:'already', orderID:thisID });
	});	
	$('.finish').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		thisID = thisID.replace('finish','');
		ajaxCall( 'api.php', changeOrderStatusResp, { mod:'k_kitchen', type:'finish', orderID:thisID });
	});	
	$('.cancel').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		thisID = thisID.replace('cancel','');
		ajaxCall( 'api.php', changeOrderStatusResp, { mod:'k_kitchen', type:'cancel', orderID:thisID });
	});	
	$('.deleteOrder').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		var thisID = thisID.replace('deleteOrder','');
		ajaxCall( 'api.php', changeOrderStatusResp, { mod:'k_kitchen', type:'deleteOrder', orderID:thisID });
	});	
	
}
function changeOrderStatusResp(response){
	if(response.process == 'success'){	
		getOrderList();
	}
}