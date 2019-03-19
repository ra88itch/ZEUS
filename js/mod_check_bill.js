var invoiceID = '';
var menus = [];
var price_total = parseFloat(0);
var price_food = parseFloat(0);
var company, address, vat, vat_id, charge;
$(document).ready(function(){
	getInvoice();
});

function getInvoice(){
	ajaxCall( 'api.php', setInvoice, { mod:'customer_detail', type:'getInvoice' });
}
function setInvoice(response){
	if(response.process == 'success'){
		var invoices = response.invoices;
		var count = invoices.length;
		var massage = '<div><h3>เลือกข้อมูลลูกค้านวด</h3>';
		var fitness = '<div><h3>เลือกข้อมูลลูกค้าฟิตเนส</h3>';
		var restaurant = '<div><h3>เลือกข้อมูลลูกค้าห้องอาหาร</h3>';
		var snooker = '<div><h3>เลือกข้อมูลลูกค้าสนุ๊กเกอร์</h3>';
		var sauna = '<div><h3>เลือกข้อมูลลูกค้าซาวน่า</h3>';
		var cash = '<div><h3>เลือกข้อมูลลูกค้าเงินสด</h3>';
		for(var i=0; i < count; i++ ){
			var arr = invoices[i];
			var html = '<div class="col3"><p id="inv'+arr.id+'">'+arr.zone+'<br>('+arr.checkin+')</p></div>';
			switch (arr.zone_category){
				case '0':
					cash += html;
					break;
				case '1':
				case '2':
					massage += html;
					break;
				case '3':
				case '4':
				case '5':
				case '15':
				case '19':
				case '20':					
				case '21':				
				case '22':
					fitness += html;
					break;
				case '6':
				case '7':
					restaurant += html;
					break;
				case '8':
				case '9':
				case '23':
					snooker += html;
					break;
				case '10':
					sauna += html;
					break;
			}
		}
		var mergeHtml = cash+'<br class="clear"></div>'+massage+'<br class="clear"></div>'+fitness+'<br class="clear"></div>'+restaurant+'<br class="clear"></div>'+snooker+'<br class="clear"></div>'+sauna+'<br class="clear"></div>';
		$('#inv-list').html(mergeHtml);
		zoneSelected();
	}
}
function zoneSelected(){
	$('.result .col3 p').click(function(){
		goback = 1;
			$('#inv-list').fadeOut();
		var text = $(this).html();
		text = text.replace('<br>',' - ');
		$('#customer-detail > h3').html(text);
		$('#customer-detail').fadeIn();
		invoiceID = $(this).attr('id');
		invoiceID = invoiceID.replace('inv','');
		ajaxCall( 'api.php', setDiscount, { mod:'discount' });
		ajaxCall( 'api.php', setInvoiceDetails, { mod:'check_bill', type:'chkBillDetails', invoiceID:invoiceID });

		//$('#customer-detail iframe').attr('src', 'print.php?inv='+invoiceID);
	});		
}
function setInvoiceDetails(response){
	if(response.process == 'success'){
		$('#customer-detail iframe').attr('src', '');
		var chk_bill = 1;
		price_total = parseFloat(0);
		price_food = parseFloat(0);
		var html = '';
		var details = response.details;
		var count = details.length;
		html += '<table><thead><tr><td>จำนวน</td><td>รายละเอียด</td><td>ราคา</td></tr></thead><tbody id="order_list">';
		for(var i=0; i < count; i++ ){
			var arr = details[i];

			// CASH ORDER
			if(arr.thisis == 'cash'){
				var price = arr.total;
				html += '<tr id="cash"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+price+'</td></tr>';
			
			// ECOUPON ORDER	
			} else if(arr.thisis == 'ecoupon'){
				var price = arr.total;
				html += '<tr id="ecoupon'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+price+'</td></tr>';

			// COUPON ORDER		
			}else if(arr.thisis == 'coupon'){
				var price = arr.total;
				html += '<tr id="coupon'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+price+'</td></tr>';

			// RESTAURANT ORDER	
			} else if(arr.thisis == 'restaurant'){
				if(arr.order_end == '0000-00-00 00:00:00'){
					var btn = '<input id="finishRestaurant'+arr.id+'" class="finishRestaurant" type="button" value="หยุดเวลา"> ';
					chk_bill = 0;
				}else{
					var btn = 'หยุดเวลาแล้ว';
				}
				html += '<tr id="resturant'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+btn+'</td></tr>';

			// MASSAGE ORDER	
			} else if(arr.thisis == 'massage'){
				if(arr.order_end == '0000-00-00 00:00:00'){
					//var price = '<input id="finishMassage'+arr.id+'" class="finishMassage" type="button" value="หยุดเวลา 2 ชั่วโมง"> ';
					var price = '<input id="finishMassageHours'+arr.id+'" class="finishMassageHours" type="button" value="หยุดเวลา กำหนดเอง"> ';
					chk_bill = 0;
				}else{
					var price = arr.total;
				}
				if(arr.order_name != null){
					html += '<tr id="massage'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+' ('+arr.employee_name+')</td><td>'+price+'</td></tr>';
				}else{
					html += '<tr id="massage'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+' ('+arr.employee_name+')</td><td>'+price+'</td></tr>';
				}
				price_total = parseFloat(price_total)+parseFloat(price);

			// SNOOKER ORDER	
			} else if(arr.thisis == 'snooker'){
				if(arr.order_end == '0000-00-00 00:00:00'){
					var price = '<input id="finishSnooker'+arr.id+'" class="finishSnooker" type="button" value="หยุดเวลา">';				
					chk_bill = 0;
				}else{
					var price = arr.total;
					/*if(arr.coupon ==0 && arr.price < 200){
						price += ' <input id="freeSnooker'+arr.id+'" class="freeSnooker" type="button" value="ใช้คูปองฟรี 1 ชั่วโมง">';
					}*/
				}
				var hours = Math.floor( arr.times_min / 60);          
				var minutes = arr.times_min % 60;
				html += '<tr id="snooker'+arr.id+'"><td>'+hours+'.'+minutes+' ชั่วโมง</td><td>'+arr.order_name+'</td><td>'+price+'</td></tr>';
				price_total = parseFloat(price_total)+parseFloat(price);

			// SAUNA ORDER
			} else if(arr.thisis == 'sauna'){
				if(arr.customer_id == 0){
					var price = arr.total;
					/*if(arr.coupon ==0){
						price += ' <input id="freeSauna'+arr.id+'" class="freeSauna" type="button" value="ใช้คูปองฟรี 1 ครั้ง">';
					}*/
				}else{
					var price = 'สมาชิก';
				}
				
				html += '<tr id="sauna'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+price+'</td></tr>';
				price_total = parseFloat(price_total)+parseFloat(price);

			// FOOD 'n DRINK ORDER
			} else if(arr.thisis == 'order'){
				if(arr.status_id < 5){
					var price = '<input id="cancel'+arr.id+'" class="cancelOrder" type="button" value="ยกเลิกรายการ"> <input id="finish'+arr.id+'" class="finishOrder" type="button" value="เสิร์ฟแล้ว"> ';
					chk_bill = 0;
				}else{
					var price = arr.total;

				}
				html += '<tr id="order'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+price+'</td></tr>';
				price_food = parseFloat(price_food)+parseFloat(price);
			
			// MEMBER
			} else if(arr.thisis == 'member'){
				html += '<tr id="member'+arr.id+'"><td>1</td><td>สมัครสมาชิก '+arr.order_name+'</td><td>'+arr.total+'</td></tr>';
				thisIsMember = true;
			
			// LOCKER
			} else if(arr.thisis == 'locker'){
				html += '<tr id="locker'+arr.id+'"><td>1</td><td>'+arr.order_name+'</td><td>'+arr.total+'</td></tr>';
			
			// DISCOUNT
			} else if(arr.thisis == 'discount'){
				html += '<tr id="discount'+arr.id+'"><td>1</td><td>'+arr.order_name+'</td><td>-'+arr.total+'</td></tr>';
			}
		}
		if(chk_bill=='1'){
			html += '<tr><td colspan="2">รวม</td><td>'+response.total+'</td></tr>';
			var pok = '';
			if(response.pok == true){
				pok = '&pok&bill='+response.invoice_bill+'&receive='+response.receive+'&change='+response.change;
			}
			if(response.member_status==true){
				html += '<tr><td colspan="2">ส่วนลดค่าอาหาร</td><td>'+response.discount+'</td></tr>';
				html += '<tr><td colspan="2">ยอดหลังหักส่วนลด</td><td>'+response.grand_total+'</td></tr>';
				$('#customer-detail iframe').attr('src', 'print.php?inv='+response.invoice_id+'&member_id='+response.member_id+''+pok);
			}else{
				response.discount = 0;
				$('#customer-detail iframe').attr('src', 'print.php?inv='+response.invoice_id+''+pok);
			}
			if(response.receive){
				html += '<tr><td colspan="2">จำนวนเงินที่รับ</td><td>'+response.receive+'</td></tr>';
				html += '<tr><td colspan="2">จำนวนเงินทอน</td><td>'+response.change+'</td></tr>';
			}
			html += '</tbody></table><div class="check_bill" id="member">สมาชิก</div><div class="check_bill" id="discount">คูปองส่วนลด</div><div class="check_bill" id="ecoupon">eCoupon</div><div class="check_bill" id="check_bill">ชำระเงิน</div>';	
		}else{
			html += '</tbody></table>';	
		}
	
	}else{
		var html = $('#customer-detail > div').html();
		html += '<table><thead><tr><td>Unit</td><td>Description</td><td>Status</td></tr></thead><tbody id="order_list">';
		html += '</tbody></table><div class="check_bill" id="member">สมาชิก</div><div class="check_bill" id="discount">คูปองส่วนลด</div><div class="check_bill" id="ecoupon">eCoupon</div><div class="check_bill" id="check_bill">ชำระเงิน</div>';	
	}

	html += '<br><br><br>';

	$('#customer-detail > div').html(html);

	$('#discount').click(function(){
		$('#discount-list').addClass('visible');
	});
	$('#ecoupon').click(function(){
		
		var ecouponList = $('#ecoupon-list').html();

		if(ecouponList == ''){
			var html = '<h3>eCoupon</h3><span class="close">ปิด</span>';
			html += '<input type="text" id="ecoupon_mobile" placeholder="ใส่เบอร์โทรศัพท์"><button id="ecoupon_search">ค้นหา</button>';
			html += '<div id="discount-ecoupon">';

			html += '</div>';
			$('#ecoupon-list').html(html+'<br class="clear">');	


			$('#ecoupon-list .close').unbind('click');
			$('#ecoupon-list .close').click(function(){
				$('#ecoupon-list').removeClass('visible');
			});	
		}

		$('#ecoupon-list').addClass('visible');
		$('#ecoupon_search').click(function(){
			var ecoupon_mobile = $('#ecoupon_mobile').val();
			ajaxCall( 'api.php', setECoupon, { mod:'ecoupon', type:'listTicket', mobile:ecoupon_mobile });
		});	
	});
	$('#customer-detail #member').click(function(){
		checkBillWithMember(response.invoice_id);
	});	
	$('#customer-detail #check_bill').click(function(){
		checkBillForm(response.invoice_id, response.member_id, response.grand_total, response.vat, response.discount, response.entertainer);
	});	
	$('.cancelOrder').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		cancelOrder(thisID);
	});	
	$('.finishOrder').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		finishOrder(thisID);
	});
	$('.finishSnooker').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');		
		finishSnooker(thisID);
	});
	$('.finishSauna').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		finishSauna(thisID);
	});
	$('.finishRestaurant').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		finishRestaurant(thisID);
	});
	$('.finishMassageHours').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		finishMassageHoursStep1(thisID);
	});

	$('.freeSnooker').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');		
		freeSnooker(thisID);
	});
	$('.freeSauna').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		freeSauna(thisID);
	});

}

function finishResponse(response){
	ajaxCall( 'api.php', setInvoiceDetails, { mod:'check_bill', type:'chkBillDetails', invoiceID:invoiceID });
}
function cancelOrder(thisID){
	var thisID = thisID.replace('cancel','');
	ajaxCall( 'api.php', finishResponse, { mod:'customer_detail', type:'cancelOrder', orderID:thisID });
}
function finishOrder(thisID){
	var thisID = thisID.replace('finish','');
	ajaxCall( 'api.php', finishResponse, { mod:'customer_detail', type:'finishOrder', orderID:thisID });
}
function finishSnooker(thisID){
	var thisID = thisID.replace('finishSnooker','');
	ajaxCall( 'api.php', finishResponse, { mod:'customer_detail', type:'finishSnooker', orderID:thisID });
}
function freeSnooker(thisID){
	var thisID = thisID.replace('freeSnooker','');
	ajaxCall( 'api.php', finishResponse, { mod:'check_bill', type:'freeSnooker', orderID:thisID });
}
function freeSauna(thisID){
	var thisID = thisID.replace('freeSauna','');
	ajaxCall( 'api.php', finishResponse, { mod:'check_bill', type:'freeSauna', orderID:thisID });
}

function checkBillWithMember(invoice_id){
	var html = '<h3 class="title">กรอกรหัสสมาชิก</h3>';
	html += '<ul class="form">';
	html += '<li><span class="label">รหัสสมาชิก</span><input type="text" id="member_id"></li>';
	html += '</ul>';
	html += '<div class="submit"><input type="button" value="คำนวนส่วนลด" id="submit"> or <span id="cancel">ยกเลิก</span></div>';
	$('#pop .warp').html(html);
	$('#pop').show();

	$('#pop #cancel').unbind('click');
	$('#pop #cancel').click(function(){
		$('#pop').hide();
	});

	$('#pop #submit').unbind('click');
	$('#pop #submit').click(function(){
		var member_id = $('#member_id').val();
		if(member_id!=''){			
			$('#pop').hide();
			ajaxCall( 'api.php', setInvoiceDetails, { mod:'check_bill', type:'chkBillDetails', invoiceID:invoice_id, memberID:member_id });
		}else{
			alert('ข้อมูลไม่ครบ');
		}		
	});
}

function checkBillForm(invoice_id, member_id, grand_total, vat, discount, entertainer){
	var count = entertainer.length;
	var option = '';
	for(var i=0; i < count; i++ ){
		var arr = entertainer[i];
		option += '<option value="'+arr.id+'">'+arr.entertainer_name+'</option>';
	}
	var html = '<h3 class="title">ชำระเงิน</h3>';
	html += '<ul class="form">';
	html += '<li><span class="label">ยอดที่ต้องชำระ</span><input type="text" value="'+grand_total+'" disabled></li>';
	html += '<li><span class="label">รูปแบบการจ่ายเงิน</span><select id="payment_method"><option value="">กรุณาเลือก</option><option value="1">ชำระด้วยเงินสด</option><option value="2">ชำระผ่านบัตร</option><option value="3">เอ็นเตอร์เทน</option></select></li>';
	html += '<li class="entertainer" style="display:none;"><span class="label">ผู้อนุมัติ</span><select id="entertainer">'+option+'</select></li>';
	html += '<li><span class="label">จำนวนเงินที่รับมา</span><input id="cash" type="text"></li>';
	html += '</ul>';
	html += '<div class="submit"><input type="button" value="ชำระเงิน" id="submit"> or <span id="cancel">ยกเลิก</span></div>';
	
	$('#pop .warp').html(html);
	$('#pop').show();

	$('#payment_method').change(function(){
		var if_entertainer = $(this).val();
		if(if_entertainer=='3'){
			$('.entertainer').show();
		}else{
			$('.entertainer').hide();
		}
	});

	$('#pop #cancel').unbind('click');
	$('#pop #cancel').click(function(){
		$('#pop').hide();
	});

	$('#pop #submit').unbind('click');
	$('#pop #submit').click(function(){
		$('#check_bill, #member, #discount').hide();
		
		var payment_method = $('#payment_method').val();
		var cash = $('#cash').val();
		var entertainer_id = $('#entertainer').val();
		
		if(payment_method=='2'){			
			cash = grand_total;
		}

		if(payment_method!='' && (cash >= grand_total)){			
			$('#pop').hide();
			ajaxCall( 'api.php', setInvoiceDetails, { mod:'check_bill', type:'cash', invoiceID:invoice_id, memberID:member_id, grandTotal:grand_total, paymentMethod:payment_method, cash:cash, vat:vat, discount:discount, entertainerID:entertainer_id });
		}else{
			alert('ข้อมูลไม่ครบ');
		}
		
	});
}

function finishSauna(thisID){
	var thisID = thisID.replace('finishSauna','');
	ajaxCall( 'api.php', finishResponse, { mod:'customer_detail', type:'finishSauna', orderID:thisID });
}
function finishRestaurant(thisID){
	var thisID = thisID.replace('finishRestaurant','');
	ajaxCall( 'api.php', finishResponse, { mod:'customer_detail', type:'finishRestaurant', orderID:thisID });
}
function massageCal(){	
	var thisVal = $('.form #massage_type').val(); 
	var hours = $('.form #hours').val();
	var coupon = 0;
	if(thisVal == 0){
		var msgVal = hours*msgPrice1 ;
	}else if(thisVal == 1){
		var msgVal = hours*msgPrice1 ;

		if(coupon == 1){
			var discount = 2*msgPrice1 ;
			msgVal = msgVal-discount ;
		}
	}else if(thisVal == 2){
		if(coupon > 1){
			var msgVal = hours*msgPrice1 ;
		}else{
			var msgVal = (hours*msgPrice1)+msgPrice2 ;
		}
		if(coupon == 1 || coupon == 2){
			var discount = 2*msgPrice1 ;
			msgVal = msgVal-discount ;
		}
	}else if(thisVal == 3){
		var msgVal = (hours*msgPrice12);
	}else if(thisVal == 4){
		var msgVal = (hours*msgPrice13);
	}
	$('.form #total').val(msgVal);
}
function finishMassageHoursStep1(thisID){
	var thisID = thisID.replace('finishMassageHours','');
	var html = '<div>';
	html += '<ul class="form"><li><span class="label">จำนวนชั่วโมง</span><input type="text" id="hours" maxlength="20"></li>';
	html += '<li><span class="label">ประเภทการใช้บริการ</span><select id="massage_type"><option value="">เลือกประเภท</option><option value="0">ค่าชั่วโมงพนักงานนวด</option><option value="1">นวดแผนไทยห้องรวม</option><option value="2">นวดแผนไทยห้อง VIP</option><option value="3">นวดน้ำมัน / สปา</option><option value="4">ค่าบริการห้อง VIP</option></select></li>';
	html += '<li><span class="label">หมายเลขหมอนวด</span><input type="text" id="massager_id" maxlength="3"></li>';
	//html += '<li><span class="label">คูปองส่วนลด</span><select id="coupon"><option value="0">เลือกคูปอง</option><option value="1">ฟรีนวดไทย 2 ชม.</option><option value="2">ฟรีนวดแผนไทยห้อง VIP 2 ชม.</option><option value="3">ค่าบริการห้อง VIP</option></select></li>';
	html += '<li><span class="label">ราคารวม</span><input type="text" id="total" maxlength="20" disabled></li></ul>';
	html += '<div style="text-align:center;" id="submit">ยืนยัน</div>';
	html += '<div style="text-align:center;" id="cancel">ยกเลิก</div>';
	html += '</div>';
	$('#pop .warp').html(html);
	$('#pop').show();

	$('.form #massage_type').change(function(){
		massageCal();
	});
	$('.form #hours').keyup(function(){
		massageCal();
	});

	$('#pop #cancel').unbind('click');
	$('#pop #cancel').click(function(){
		$('#pop').hide();
	});

	$('#pop #submit').unbind('click');
	$('#pop #submit').click(function(){
		var hours = $('.form #hours').val();
		var massage_type	= $('.form #massage_type').val();
		var massager_id		= $('.form #massager_id').val();
		var coupon = 0;
		var total = $('.form #total').val();
		//alert(hours);
		if(total !='' && massager_id!='' && hours!=''){
			finishMassageHoursStep2(thisID, hours, massage_type, massager_id, total, coupon);
			$('#pop').hide();
		}else{
			alert('กรอกข้อมูลไม่ครบ');
		}
	});	
}
function finishMassageHoursStep2(thisID, hours, massage_type, massager_id, total, coupon){
	ajaxCall( 'api.php', finishResponse, { mod:'customer_detail', type:'finishMassageHours', orderID:thisID, hours:hours, massage_type:massage_type, massager_id:massager_id, total:total, coupon:coupon });
}
function setDiscount(response){
	if(response.process == 'success'){
		var discount = response.discount;
		var count = discount.length;
		var html = '<h3>คูปองส่วนลด</h3><span class="close">ปิด</span>';
		html += '<div id="discount-coupon">';
		for(var i=0; i < count; i++ ){
			var arr = discount[i];
			html += '<div class="col3"><p id="discount'+arr.id+'">'+arr.discount_name+'<br>( '+arr.discount_price+' บาท )</p></div>';
		}
		html += '</div>';
		$('#discount-list').html(html+'<br class="clear">');

		for(var i=0; i < count; i++ ){
			var arr = discount[i];
			$('#discount'+arr.id).click(function(){
				$('#discount-list').removeClass('visible');
				var thisID = $(this).attr('id');
				thisID = thisID.replace('discount','');
				ajaxCall( 'api.php', finishResponse, { mod:'discount', type:'addDiscount', invoiceID:invoiceID, discountID:thisID });
			});
		}

		$('#discount-list .close').unbind('click');
		$('#discount-list .close').click(function(){
			$('#discount-list').removeClass('visible');
		});
	}
}
function setECoupon(response){
	if(response.process == 'success'){
		var discount = response.discount;
		var count = discount.length;
		var html = '';
		for(var i=0; i < count; i++ ){
			var arr = discount[i];
			html += '<div class="col3"><p id="ecoupon'+arr.id+'">'+arr.discount_name+'<br>(คงเหลือ '+arr.discount_balance+')</p></div>';
		}
		$('#discount-ecoupon').html(html);

		for(var i=0; i < count; i++ ){
			var arr = discount[i];
			$('#ecoupon'+arr.id).click(function(){
				$('#ecoupon-list').removeClass('visible');
				var thisID = $(this).attr('id');
				thisID = thisID.replace('ecoupon','');
				ajaxCall( 'api.php', finishResponse, { mod:'ecoupon', type:'addTicket', invoiceID:invoiceID, discountID:thisID });
			});
		}
	}
}