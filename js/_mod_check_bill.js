var invoiceID = '';
var menus = [];
var price_total = parseFloat(0);
var price_food = parseFloat(0);
var company, address, vat, vat_id, charge;
$(document).ready(function(){
	getSystems();
	getInvoice();
});
function getSystems(){
	ajaxCall( 'api.php', getSystemsResponse, { mod:'check_bill', type:'getSystems' });
}
function getSystemsResponse(response){
	var system = response.systems;
	company = system.company;
	address = system.address;
	vat		= system.vat;
	vat_id	= system.vat_id;
	charge	= system.charge;
}
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
		for(var i=0; i < count; i++ ){
			var arr = invoices[i];
			var html = '<div class="col3"><p id="inv'+arr.id+'">'+arr.zone+'<br>('+arr.checkin+')</p></div>';
			switch (arr.zone_category){
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
		var mergeHtml = massage+'<br class="clear"></div>'+fitness+'<br class="clear"></div>'+restaurant+'<br class="clear"></div>'+snooker+'<br class="clear"></div>'+sauna+'<br class="clear"></div>';
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
		ajaxCall( 'api.php', setInvoiceDetails, { mod:'customer_detail', type:'getInvoiceDetails', invoiceID:invoiceID });
	});		
}
function setInvoiceDetails(response){
	if(response.process == 'success'){	
		price_total = parseFloat(0);
		price_food = parseFloat(0);
		var html = '';
		var details = response.details;
		var count = details.length;
		html += '<table><thead><tr><td>Unit</td><td>Description</td><td>Price</td></tr></thead><tbody id="order_list">';
		for(var i=0; i < count; i++ ){
			var arr = details[i];

			// MASSAGE ORDER
			if(arr.thisis == 'massage'){
				if(arr.order_end == '0000-00-00 00:00:00'){
					var price = '<input id="finishMassage'+arr.id+'" class="finishMassage" type="button" value="หยุดเวลา"> ';
				}else{
					var price = arr.total;
					}
				if(arr.order_name != null){
					html += '<tr id="massage'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+' ('+arr.employee_name+')</td><td>'+price+'</td></tr>';
				}else{
					html += '<tr id="massage'+arr.id+'"><td>'+arr.unit+'</td><td>'+details[i-1].order_name+' ('+arr.employee_name+')</td><td>'+price+'</td></tr>';
				}
				price_total = parseFloat(price_total)+parseFloat(price);

			// SNOOKER ORDER	
			} else if(arr.thisis == 'snooker'){
				if(arr.order_end == '0000-00-00 00:00:00'){
					var price = '<input id="finishSnooker'+arr.id+'" class="finishSnooker" type="button" value="หยุดเวลา">';				
				}else{
					var price = arr.total;
				}
				html += '<tr id="snooker'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+price+'</td></tr>';
				price_total = parseFloat(price_total)+parseFloat(price);

			// SAUNA ORDER
			} else if(arr.thisis == 'sauna'){
				if(arr.order_end == '0000-00-00 00:00:00'){
					var price = '<input id="finishSauna'+arr.id+'" class="finishSauna" type="button" value="คืนบัตร"> ';
				}else{;
					var price = arr.total;
				}
				html += '<tr id="sauna'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+price+'</td></tr>';
				price_total = parseFloat(price_total)+parseFloat(price);

			// FOOD 'n DRINK ORDER
			} else if(arr.thisis == 'order'){
				if(arr.status_id < 5){
					var price = '<input id="cancel'+arr.id+'" class="cancelOrder" type="button" value="ยกเลิกรายการ"> <input id="finish'+arr.id+'" class="finishOrder" type="button" value="เสิร์ฟแล้ว"> ';
				}else{
					var price = arr.total;

				}
				html += '<tr id="order'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+price+'</td></tr>';
				price_food = parseFloat(price_food)+parseFloat(price);
			}
		}
		console.log(price_food);
		var service_charge = parseFloat(price_food*charge)/100;
		html += '<tr><td colspan="2">ราคารวม</td><td>'+(parseFloat(price_total)+parseFloat(price_food))+'</td></tr>';
		html += '<tr><td colspan="2">service charge</td><td>'+service_charge+'</td></tr>';
		
		var total = parseFloat(price_total+price_food+service_charge);
		html += '<tr><td colspan="2">ภาษีมูลค่าเพิ่ม</td><td>'+(parseFloat((total*vat)/100)).toFixed(2)+'</td></tr>';
		html += '<tr><td colspan="2">ราคารวมทั้งหมด</td><td>'+(total+parseFloat((total*vat)/100)).toFixed(2)+'</td></tr>';
		html += '</tbody></table><div class="check_bill">CHECK BILL</div>';	
	}else{
		var html = $('#customer-detail > div').html();
		html += '<table><thead><tr><td>Unit</td><td>Description</td><td>Status</td></tr></thead><tbody id="order_list">';
		html += '</tbody></table><div class="check_bill">CHECK BILL</div><div class="payment_detail"></div><div class="print"></div>';	
	}
	$('#customer-detail > div').html(html);
	$('#customer-detail .check_bill').click(function(){
		
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

}

function finishResponse(response){
	ajaxCall( 'api.php', setInvoiceDetails, { mod:'customer_detail', type:'getInvoiceDetails', invoiceID:response.invoice_id });
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