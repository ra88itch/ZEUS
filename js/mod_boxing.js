var mod = '?mod=ecoupon';
var chooseEcoupon		=	[];
$(document).ready(function(){
	$('#ecoupon-search').click(function(){
		var mobile				= $('#ecoupon-mobile').val();
		ajaxCall( 'api.php', searchResponsed, { 
			mod:'ecoupon', 
			type: 'search',
			mobile: mobile
		} );
	});
	/*
	$('#add').click(function(){
		ajaxCall( 'api.php', addResponsed, { mod:'ecoupon', type: 'add' } );
	});
	$('.member').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('member','');
		ajaxCall( 'api.php', addNewResponsed, { mod:'booking', member_id: thisID, type: 'member' } );
	});	
	$('.edit').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('edit','');
		ajaxCall( 'api.php', editResponsed, { mod:'ecoupon', member_id: thisID, type: 'edit' } );
	});
	$('.use').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('use','');
		window.location.href = mod+'&desc='+thisID;
		//ajaxCall( 'api.php', editResponsed, { mod:'ecoupon', member_id: thisID, type: 'edit' } );
	});*/

});
function searchResponsed(response){
	if(response.view == 'getAddForm'){
		addResponsed(response);
	}else{
		myTicketResponsed(response);
	}
	goback = 1;
}
function addResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var name				= $('#name').val();
			var mobile				= $('#mobile').val();
			var enable_sms			= $('#enable_sms').val();
			if(name==''){
				alert('กรุณาเติมชื่อ');
				return false;
			}
			ajaxCall( 'api.php', addNewResponsed, { 
				mod:'ecoupon', 
				type: 'addNewMember',
				name: name,
				mobile: mobile,
				enable_sms: enable_sms
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
			var name				= $('#name').val();
			var mobile				= $('#mobile').val();
			var enable_sms			= $('#enable_sms').val();
			ajaxCall( 'api.php', editMemberResponsed, { 
				mod:'ecoupon', 
				type: 'updateMember', 
				memberID: memberID, 
				name: name, 
				mobile: mobile,
				enable_sms: enable_sms
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

function myTicketResponsed(response){
	var customer = response.customer;
	var myTicket = response.myTicket;
	var count = myTicket.length;

	var zoneMyTicket = '<h3>eCoupon ของ'+customer.name+'</h3>';
	//zoneMyTicket += '<div class="col3"><p id="buy_eCoupon"> + ซื้อ eCoupon</p></div>';
	for(var i=0; i < count; i++ ){
		var arr = myTicket[i];
		var ecouponDesc = arr.ecouponDesc;
		var zone_category = ecouponDesc.discount_zone_category;
		switch(zone_category){
			case '1':
			case '2':
			case '12':
			case '13':
				var html = '<a href="?mod=massage&ecust='+customer.id+'"><div class="col3"><p>'+ecouponDesc.ecoupon_name+'<br>(คงเหลือ '+arr.qty+')</p></div></a>';
				break;
			case '3':
			case '10':
				var html = '<a href="?mod=sauna&ecust='+customer.id+'"><div class="col3"><p>'+ecouponDesc.ecoupon_name+'<br>(คงเหลือ '+arr.qty+')</p></div></a>';
				break;
			case '8':
			case '9':
				var html = '<a href="?mod=snooker&ecust='+customer.id+'"><div class="col3"><p>'+ecouponDesc.ecoupon_name+'<br>(คงเหลือ '+arr.qty+')</p></div></a>';
				break;
			case '6':
			case '7':
				var html = '<a href="?mod=restaurant&ecust='+customer.id+'"><div class="col3"><p>'+ecouponDesc.ecoupon_name+'<br>(คงเหลือ '+arr.qty+')</p></div></a>';
				break;
			default:
				var html = '';
		}
		//var html = '<div class="col3"><p id="myTicket'+arr.id+'" class="myTicket" data-qty="'+arr.qty+'" data-name="'+ecouponDesc.ecoupon_name+'" data-zoneCate="'+ecouponDesc.discount_zone_category+'">'+ecouponDesc.ecoupon_name+' ('+arr.qty+')</p></div>';
		
		zoneMyTicket += html;
	}
	$('#zone-myTicket').html(zoneMyTicket);
	$('#booking').html('<input type="button" id="ecoupon-sell" class="submit" value="ขาย eCoupon"> &nbsp;&nbsp;&nbsp; <input type="button" id="ecoupon-booking" class="submit" value="ยืนยัน"><input type="hidden" id="name" value="'+customer.name+'"><input type="hidden" id="customer_id" value="'+customer.id+'">');

	
	$('#ecoupon-sell').click(function(){
		
		var ecouponList = $('#ecoupon-list').html();

		if(ecouponList == ''){
			var html = '<h3>eCoupon</h3><span class="close">ปิด</span>';
			html += '<div id="discount-ecoupon">';

			html += '</div>';
			$('#ecoupon-list').html(html+'<br class="clear">');	


			$('#ecoupon-list .close').unbind('click');
			$('#ecoupon-list .close').click(function(){
				$('#ecoupon-list').removeClass('visible');
			});	
		}

		$('#ecoupon-list').addClass('visible');
		ajaxCall( 'api.php', setECoupon, { mod:'ecoupon', type:'ecouponList' });
	});

	showResult();
	//zoneSelected();
	//booking();
}
function showResult(){
	$('#zone-myTicket').slideDown();
	$('#zone-chooseTicket').slideDown();
	$('#booking').slideDown();
	$('.search-zone').slideUp();
}
function hideResult(){
	$('#zone-myTicket').slideUp();
	$('#zone-chooseTicket').slideUp();
	$('#booking').slideUp();
	$('.search-zone').slideDown();
}
/*
function zoneSelected(){
	$('.myTicket').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('myTicket','');		
		var dataQty = $(this).attr('data-qty');		
		var dataName = $(this).attr('data-name');
		var dataZoneCate = $(this).attr('data-zoneCate');		
		
		if(dataQty > 0){
			var len = chooseEcoupon.length;
			if(len > 0){
				for(var i=0; i < len; i++){
					var arr = chooseEcoupon[i];
					if(arr.id == thisID){
						arr.qty++;
					}else{				
						chooseEcoupon.push({id:thisID,name:dataName,qty:dataQty,zoneCate:dataZoneCate});
					}
				}
			}else{
				chooseEcoupon.push({id:thisID,name:dataName,qty:1,zoneCate:dataZoneCate});
			}
			dataQty--;
			$(this).attr('data-qty', dataQty);
			$(this).text(dataName+' ('+dataQty+')');
		}

		//setChooseTicket();
	});	
}
/*function setChooseTicket(){	
	if(chooseEcoupon.length > 0) {
	var html = '<h3>ใช้ eCoupon</h3>';
		for(var i = 0; i < chooseEcoupon.length; i++) {
			html += '<div class="col3"><p id="chooseTicket'+chooseEcoupon[i].id+'" class="chooseTicket" data-qty="'+chooseEcoupon[i].qty+'">'+chooseEcoupon[i].name+' (<span id="chooseTicketQty'+chooseEcoupon[i].id+'">'+chooseEcoupon[i].qty+'</span>)</p></div>';
		}
		$('#zone-chooseTicket').html(html);
	} else {
		$('#zone-chooseTicket').html('');
	}
}
function booking(){	
	$('#ecoupon-booking').click(function(){
		var name = $('#name').val();
		if(name != ''){
			ajaxCall('api.php', bookingResponse,{mod:'ecoupon', type:'chkbill', order:chooseEcoupon, name:name});
		}else{
			alert('กรุณาใส่ข้อมูลลูกค้า');
		}
	});
}*/
function bookingResponse(response){
	$('#ecoupon-mobile').val('');
	if(response.process == 'failed' ){
		alert('ทำรายการใหม่อีกครั้ง');
		hideResult();		
	}else{
		alert('ทำรายการเรียบร้อย')
		hideResult();
	}
	chooseEcoupon	=	[];
	$('#zone-chooseTicket').html('');
}

function setECoupon(response){
	if(response.process == 'success'){
		var ecoupon = response.ecoupon;
		var count = ecoupon.length;
		var html = '';
		for(var i=0; i < count; i++ ){
			var arr = ecoupon[i];
			html += '<div class="col3"><p id="ecoupon'+arr.id+'">'+arr.ecoupon_name+'['+arr.qty+' ใบ]<br>('+arr.price+' บาท)</p></div>';
		}
		$('#discount-ecoupon').html(html);

		for(var i=0; i < count; i++ ){
			var arr = ecoupon[i];
			$('#ecoupon'+arr.id).click(function(){
				$('#ecoupon-list').removeClass('visible');
				var customer_id = $('#customer_id').val();
				var name = $('#name').val();
				var thisID = $(this).attr('id');
				thisID = thisID.replace('ecoupon','');

				if(customer_id != ''){
					//ajaxCall('api.php', bookingResponse,{mod:'ecoupon', type:'chkbill', order:chooseEcoupon, name:name});
					ajaxCall('api.php', bookingResponse, { mod:'ecoupon', type:'chkbill', ecoupon_id:thisID, customer_id:customer_id, name:name });
				}else{
					alert('กรุณาใส่ข้อมูลลูกค้า');
				}
			});
		}
	}
}