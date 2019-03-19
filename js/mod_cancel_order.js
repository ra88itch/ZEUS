$(document).ready(function(){
	setOrderList();
});

function getOrderList(){
	ajaxCall( 'api.php', setOrderList, { mod:'k_kitchen', type:'getOrderList' });
}
function setOrderList(response){	
	$('.order').click(function(){
		$('#order-detail iframe').attr('src', '');
		var thisID = $(this).attr('id');
		thisID = thisID.replace('order','');
		$('#order-detail iframe').attr('src', 'print-cancel.php?order_id='+thisID);
	});		
}
function changeOrderStatusResp(response){
	if(response.process == 'success'){	
		getOrderList();
	}
}