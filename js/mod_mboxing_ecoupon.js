var mod = '?mod=account';
$(document).ready(function(){
	$('#add').click(function(){
		ajaxCall( 'api.php', addResponsed, { mod:'mboxing_ecoupon', type: 'add' } );
	});	
	$('.edit').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('edit','');
		ajaxCall( 'api.php', editResponsed, { mod:'mboxing_ecoupon', coupon_id: thisID, type: 'edit' } );
	});	
	$('.status').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('status','');
		ajaxCall( 'api.php', statusResponsed, { mod:'mboxing_ecoupon', coupon_id: thisID, type: 'changeCouponStatus' } );
	});	
});
function addResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var coupon_name		= $('#coupon_name').val();
			var times			= $('#times').val();
			var price			= $('#price').val();
			var status			= $('#status').val();
			var discount			= $('#discount').val();
			if(coupon_name==''){
				alert('Username is empty.');
				return false;
			}
			if(price==''){
				alert('Price is empty.');
				return false;
			}
			ajaxCall( 'api.php', addNewResponsed, { mod:'mboxing_ecoupon', type: 'addNewCoupon', coupon_name: coupon_name, times: times, price: price, status: status, discount:discount } );
		});	
	}else{
		alert(response.msg);
	}
}
function editResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var coupon_name		= $('#coupon_name').val();
			var times			= $('#times').val();
			var price			= $('#price').val();
			var status			= $('#status').val();
			var coupon_id		= $('#coupon_id').val();
			var discount			= $('#discount').val();
			if(coupon_name==''){
				alert('Username is empty.');
				return false;
			}
			if(price==''){
				alert('Price is empty.');
				return false;
			}
			ajaxCall( 'api.php', editAccountResponsed, { mod:'mboxing_ecoupon', type: 'updateCoupon', coupon_name: coupon_name, times: times, price: price, status: status, coupon_id: coupon_id, discount:discount } );			
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
		if(response.status == 0){
			$('#status'+response.coupon).addClass('lock');			
		}else{
			$('#status'+response.coupon).removeClass('lock');
		}
	}else{
		alert(response.msg);
	}
}