var invoiceID = '';
var menus = [];
var price_total = parseFloat(0);
var price_food = parseFloat(0);
var company, address, vat, vat_id, charge;
$(document).ready(function(){
	getInvoice(0);
	$('#dates').change(function(){
		//alert($(this).val());
		getInvoice($(this).val());
		$('#inv-list').fadeIn();
		$('#customer-detail').fadeOut();
	});
});

function getInvoice(date_val){
	ajaxCall( 'api.php', setInvoice, { mod:'bill_history', type:'getInvoice', date_val:date_val });
}
function setInvoice(response){
	goback = 0;
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
					fitness += html;
					break;
				case '6':
				case '7':
					restaurant += html;
					break;
				case '8':
				case '9':
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
			$('#inv-list').fadeOut();
		var text = $(this).html();
		text = text.replace('<br>',' - ');
		$('#customer-detail > h3').html(text);
		$('#customer-detail').fadeIn();
		invoiceID = $(this).attr('id');
		invoiceID = invoiceID.replace('inv','');
		ajaxCall( 'api.php', setInvoiceDetails, { mod:'check_bill', type:'chkBillDetails', invoiceID:invoiceID });
		//$('#customer-detail iframe').attr('src', 'print.php?inv='+invoiceID);
	});		
}
function setInvoiceDetails(response){
	if(response.process == 'success'){
		goback = 1;
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

			// RESTAURANT ORDER
			if(arr.thisis == 'restaurant'){
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
				}
				var hours = Math.floor( arr.times_min / 60);          
				var minutes = arr.times_min % 60;
				html += '<tr id="snooker'+arr.id+'"><td>'+hours+'.'+minutes+' ชั่วโมง</td><td>'+arr.order_name+'</td><td>'+price+'</td></tr>';
				price_total = parseFloat(price_total)+parseFloat(price);

			// SAUNA ORDER
			} else if(arr.thisis == 'sauna'){
				/*if(arr.order_end == '0000-00-00 00:00:00'){
					var price = '<input id="finishSauna'+arr.id+'" class="finishSauna" type="button" value="คืนบัตร"> ';
					chk_bill = 0;
				}else{*/
					var price = arr.total;
				//}
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
				html += '<tr id="order'+arr.id+'"><td>1</td><td>สมัครสมาชิก '+arr.order_name+'</td><td>'+arr.total+'</td></tr>';
				thisIsMember = true;
			}
		}
		if(chk_bill=='1'){
			html += '<tr><td colspan="2">รวม</td><td>'+response.total+'</td></tr>';
			var pok = '';
			//if(response.pok == true){
				pok = '&pok&bill='+response.invoice_bill+'&receive='+response.receive+'&change='+response.change;
			//}
			if(response.member_status==true){
				$('#customer-detail iframe').attr('src', 'print.php?inv='+response.invoice_id+'&member_id='+response.member_id+''+pok);
			}else{
				response.discount = 0;
				$('#customer-detail iframe').attr('src', 'print.php?inv='+response.invoice_id+''+pok);
			}
			
			html += '</tbody></table>';	
		}else{
			
			
		
		}
		html += '</tbody></table>';	
	
	}else{
		var html = $('#customer-detail > div').html();
		html += '<table><thead><tr><td>Unit</td><td>Description</td><td>Status</td></tr></thead><tbody id="order_list">';
		html += '</tbody></table><div class="check_bill" id="member">MEMBER</div><div class="check_bill" id="check_bill">CHECK BILL</div>';	
	}
	$('#customer-detail > div').html(html);
	$('#customer-detail #member').click(function(){
		checkBillWithMember(response.invoice_id);
	});	
	$('#customer-detail #check_bill').click(function(){
		checkBillForm(response.invoice_id, response.member_id, response.grand_total, response.vat, response.discount);
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
	$('.finishMassage').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		finishMassage(thisID);
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

}

function finishResponse(response){
	ajaxCall( 'api.php', setInvoiceDetails, { mod:'check_bill', type:'chkBillDetails', invoiceID:invoiceID });
	//ajaxCall( 'api.php', setInvoiceDetails, { mod:'customer_detail', type:'getInvoiceDetails', invoiceID:response.invoice_id });
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
function finishMassage(thisID){
	var thisID = thisID.replace('finishMassage','');
	ajaxCall( 'api.php', finishResponse, { mod:'customer_detail', type:'finishMassage', orderID:thisID });
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

function checkBillForm(invoice_id, member_id, grand_total, vat, discount){
	//console.log(invoice_id +' - '+ member_id);
	var html = '<h3 class="title">ชำระเงิน</h3>';
	html += '<ul class="form">';
	html += '<li><span class="label">ยอดที่ต้องชำระ</span><input type="text" value="'+grand_total+'" disabled></li>';
	html += '<li><span class="label">รูปแบบการจ่ายเงิน</span><select id="payment_method"><option value="1">ชำระด้วยเงินสด</option><option value="2">ชำระผ่านบัตร</option></select></li>';
	html += '<li><span class="label">จำนวนเงินที่รับมา</span><input id="cash" type="text"></li>';
	html += '</ul>';
	html += '<div class="submit"><input type="button" value="ชำระเงิน" id="submit"> or <span id="cancel">ยกเลิก</span></div>';
	
	$('#pop .warp').html(html);
	$('#pop').show();

	$('#pop #cancel').unbind('click');
	$('#pop #cancel').click(function(){
		$('#pop').hide();
	});

	$('#pop #submit').unbind('click');
	$('#pop #submit').click(function(){	
		var payment_method = $('#payment_method').val();
		var cash = $('#cash').val();
		
		if(payment_method=='2'){			
			cash = grand_total;
		}

		if(payment_method!='' && (cash >= grand_total)){			
			$('#pop').hide();
			ajaxCall( 'api.php', setInvoiceDetails, { mod:'check_bill', type:'cash', invoiceID:invoice_id, memberID:member_id, grandTotal:grand_total, paymentMethod:payment_method, cash:cash, vat:vat, discount:discount });
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

function finishMassageHoursStep1(thisID){
	var thisID = thisID.replace('finishMassageHours','');
	var html = '<div>';
		html += '<ul class="form"><li><span class="label">จำนวนชั่วโมง</span><input type="text" id="hours" maxlength="20"></li></ul>';
		html += '<div style="text-align:center;" id="submit">ยืนยัน</div>';
		html += '<div style="text-align:center;" id="cancel">ยกเลิก</div>';
		html += '</div>';
		$('#pop .warp').html(html);
		$('#pop').show();

		$('#pop #cancel').unbind('click');
		$('#pop #cancel').click(function(){
			$('#pop').hide();
		});

		$('#pop #submit').unbind('click');
		$('#pop #submit').click(function(){
			var hours = $('.form #hours').val();
			alert(hours);
			if(hours > 2){
				finishMassageHoursStep2(thisID, hours);
				$('#pop').hide();
			}
		});	
}
function finishMassageHoursStep2(thisID, hours){
	ajaxCall( 'api.php', finishResponse, { mod:'customer_detail', type:'finishMassageHours', orderID:thisID, hours:hours });
}