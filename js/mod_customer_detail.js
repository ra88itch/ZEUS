var invoiceID = '';
var menus = [];
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
				case '19':
				case '20':
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
		ajaxCall( 'api.php', setInvoiceDetails, { mod:'customer_detail', type:'getInvoiceDetails', invoiceID:invoiceID });
		ajaxCall( 'api.php', setMenu, { mod:'menu' });
	});		
}
function setInvoiceDetails(response){
	if(response.process == 'success'){
		var thisIsMember = false;
		var html = '';
		var details = response.details;
		var count = details.length;
		html += '<table><thead><tr><td>Unit</td><td>Description</td><td>Status</td></tr></thead><tbody id="order_list">';
		for(var i=0; i < count; i++ ){
			var arr = details[i];

			// CASH ORDER
			if(arr.thisis == 'cash'){
				var btn = 'รับเงินแล้ว';
				html += '<tr id="cash"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+btn+'</td></tr>';

			// COUPON ORDER	
			} else if(arr.thisis == 'coupon'){
				var btn = '';
				html += '<tr id="coupon'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+btn+'</td></tr>';

			// RESTAURANT ORDER	
			} else if(arr.thisis == 'restaurant'){
				if(arr.order_end == '0000-00-00 00:00:00'){
					var btn = '<input id="finishRestaurant'+arr.id+'" class="finishRestaurant" type="button" value="หยุดเวลา"> ';
				}else{
					var btn = 'หยุดเวลาแล้ว';
				}
				html += '<tr id="resturant'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+btn+'</td></tr>';

			// MASSAGE ORDER	
			} else if(arr.thisis == 'massage'){
				var hours = arr.times_min / 60;
				if(arr.order_end == '0000-00-00 00:00:00'){
					//var btn = '<input id="finishMassage'+arr.id+'" class="finishMassage" type="button" value="หยุดเวลา 2 ชั่วโมง"> ';
					var btn = '<input id="finishMassageHours'+arr.id+'" class="finishMassageHours" type="button" value="หยุดเวลา กำหนดเอง"> ';
				}else{
					var btn = 'หยุดเวลาแล้ว';
				}
				btn += ' <input id="deleteMassage'+arr.id+'" class="deleteMassage" type="button" value="ลบรายการ"> ';
				if(arr.order_name != null){
					html += '<tr id="massage'+arr.id+'"><td>'+hours+' ชั่วโมง</td><td>'+arr.order_name+' ('+arr.employee_name+')</td><td>'+btn+'</td></tr>';
				}else{
					html += '<tr id="massage'+arr.id+'"><td>'+hours+' ชั่วโมง</td><td>'+details[i-1].order_name+' ('+arr.employee_name+')</td><td>'+btn+'</td></tr>';
				}

			// SNOOKER ORDER	
			} else if(arr.thisis == 'snooker'){
				
				if(arr.order_end == '0000-00-00 00:00:00'){
					html += '<tr id="snooker'+arr.id+'"><td>'+arr.times_min+' นาที</td><td>'+arr.order_name+'</td><td><input id="finishSnooker'+arr.id+'" class="finishSnooker" type="button" value="หยุดเวลา"></td></tr>';
				}else{
					var hours = Math.floor( arr.times_min / 60);          
					var minutes = arr.times_min % 60;
					html += '<tr id="snooker'+arr.id+'"><td>'+hours+'.'+minutes+' ชั่วโมง</td><td>'+arr.order_name+'</td><td>หยุดเวลา</td></tr>';
				}
				
			// SAUNA ORDER
			} else if(arr.thisis == 'sauna'){
				if(arr.customer_id == 0){
					if(arr.order_end == '0000-00-00 00:00:00'){
						var btn = '<input id="finishSauna'+arr.id+'" class="finishSauna" type="button" value="คืนบัตร"> ';
					}else{;
						var btn = 'คืนบัตรแล้ว ';
					}
					//btn += ' <input id="deleteSauna'+arr.id+'" class="deleteSauna" type="button" value="ลบรายการ"> ';
				}else{
					var btn = 'สมาชิก';
				}				
				html += '<tr id="sauna'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+btn+'</td></tr>';

			// FOOD 'n DRINK ORDER
			} else if(arr.thisis == 'order'){
				if(arr.status_id < 5){
					var btn = '<input id="cancel'+arr.id+'" class="cancelOrder" type="button" value="ยกเลิกรายการ"> <input id="finish'+arr.id+'" class="finishOrder" type="button" value="เสิร์ฟแล้ว"> '+arr.status;
				}else if(arr.status_id == 7){
					var btn = arr.status;
				}else{
					var btn = '<input id="cancel'+arr.id+'" class="cancelOrder" type="button" value="ยกเลิกรายการ"> '+arr.status;
				}
				//btn += ' <input id="deleteOrder'+arr.id+'" class="deleteOrder" type="button" value="ลบรายการ"> ';
				html += '<tr id="order'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+btn+'</td></tr>';
			
			// MEMBER
			} else if(arr.thisis == 'member'){
				html += '<tr id="order'+arr.id+'"><td>1</td><td>'+arr.order_name+'</td><td>NOT PAID</td></tr>';
				thisIsMember = true;
			}			
		}
		if(thisIsMember != true){
			html += '</tbody></table><div><div class="col6"><p class="add-menu open">เพิ่มรายการอาหาร</p></div><div class="col6"><p class="open new-zone">เพิ่มโซน</p></div></div>';
		}else{
			html += '</tbody></table>';	
		}
		
	}else{
		var html = $('#customer-detail > div').html();
		html += '<table><thead><tr><td>Unit</td><td>Description</td><td>Status</td></tr></thead><tbody id="order_list">';
		html += '</tbody></table><div><div class="col6"><p class="add-menu open">เพิ่มรายการอาหาร</p></div><div class="col6"><p class="open new-zone">เพิ่มโซน</p></div></div>';
	}
	$('#customer-detail > div').html(html);
	
	$('#customer-detail .new-zone').click(function(){
		ajaxCall( 'api.php', newZoneResponse, { mod:'customer_detail', type:'addMoreZone', invoiceID:response.invoice_id });
	});	
	$('#customer-detail .add-menu').click(function(){
		$('#menu-list').addClass('visible');
		$('#search_menu').val('');
	});	
	// deleteOrder
	$('.cancelOrder').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		cancelOrder(thisID);
	});	
	/*$('.deleteOrder').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		deleteOrder(thisID);
	});*/	
	$('.deleteSauna').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		deleteSauna(thisID);
	});	
	$('.deleteMassage').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		deleteMassage(thisID);
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
function setMenu(response){
	if(response.process == 'success'){
		menus = response.menu;
		
		var html = '<h3>เพิ่มรายการอาหาร</h3><span class="close">ปิด</span>';
		html += '<input type="text" id="search_menu" val="">';

		var cooking_list = response.cooking_list;
		var count_cooking_list = cooking_list.length;
		html += '<select id="cooking_list">';
		for(var i=0; i < count_cooking_list; i++ ){
			var arr = cooking_list[i];
			html += '<option value="'+arr.id+'">'+arr.type_cooking+'</option>';			
		}
		/*var meat_list = response.meat_list;
		var count_meat_list = meat_list.length;
		html += '</select><select id="meat_list">';
		for(var i=0; i < count_meat_list; i++ ){
			var arr = meat_list[i];
			html += '<option value="'+arr.id+'">'+arr.type_meat+'</option>';			
		}*/
		html += '</select><div id="show_all">แสดงทั้งหมด</div><div id="menu"></div>';
		
		$('#menu-list').html(html);
		$('#meat_list').change(function(){
			filterMenu();
		});
		$('#cooking_list').change(function(){
			filterMenu();
		});
		$('#show_all').click(function(){
			showMenu();
		});
		$('#search_menu').keyup(function(){
			searchMenu();
		});
		//showMenu();
		filterMenu();
	}
}
function searchMenu(){	
	var search_menu = $('#search_menu').val();
	var count = menus.length;
	var html = '';
	for(var i=0; i < count; i++ ){
		var arr = menus[i];		
		html += '<div class="col3 mm" style="display:none;"><p id="menu'+arr.id+'">'+arr.menu_name_th+'<br>( '+arr.price+' บาท )</p></div>';
	}
	$('#menu').html(html+'<br class="clear">');
	$( "div.col3.mm:contains('"+search_menu+"')" ).css( "display", "block" );
	for(var i=0; i < count; i++ ){
		var arr = menus[i];
		$('#menu'+arr.id).data('menu_details', arr);

		$('#menu'+arr.id).unbind('click');
		$('#menu'+arr.id).click(function(){
			var menu_details = $(this).data('menu_details');
			createPopup(menu_details);
		});
	}
	$('#menu-list .close').unbind('click');
	$('#menu-list .close').click(function(){
		$('#menu-list').removeClass('visible');
	});
}
function showMenu(){	
	var cooking_list = $('#cooking_list').val();
	//var meat_list = $('#meat_list').val();	
	var count = menus.length;
	var html = '';
	for(var i=0; i < count; i++ ){
		var arr = menus[i];
		html += '<div class="col3"><p id="menu'+arr.id+'">'+arr.menu_name_th+'<br>( '+arr.price+' บาท )</p></div>';
	}
	$('#menu').html(html+'<br class="clear">');
	for(var i=0; i < count; i++ ){
		var arr = menus[i];
		$('#menu'+arr.id).data('menu_details', arr);

		$('#menu'+arr.id).unbind('click');
		$('#menu'+arr.id).click(function(){
			var menu_details = $(this).data('menu_details');
			createPopup(menu_details);
		});
	}
	$('#menu-list .close').unbind('click');
	$('#menu-list .close').click(function(){
		$('#menu-list').removeClass('visible');
	});
}
function filterMenu(){	
	var cooking_list = $('#cooking_list').val();
	//var meat_list = $('#meat_list').val();	
	var count = menus.length;
	var html = '';
	for(var i=0; i < count; i++ ){
		var arr = menus[i];
		if(arr.cooking_type == cooking_list){			
			html += '<div class="col3"><p id="menu'+arr.id+'">'+arr.menu_name_th+'<br>( '+arr.price+' บาท )</p></div>';
		}/*else if(cooking_list == '7' && cooking_list == arr.cooking_type){			
			html += '<div class="col3"><p id="menu'+arr.id+'">'+arr.menu_name_th+'<br>( '+arr.price+' บาท )</p></div>';
		}else if(cooking_list == '8' && cooking_list == arr.cooking_type){			
			html += '<div class="col3"><p id="menu'+arr.id+'">'+arr.menu_name_th+'<br>( '+arr.price+' บาท )</p></div>';
		}else if(cooking_list == '9' && cooking_list == arr.cooking_type){			
			html += '<div class="col3"><p id="menu'+arr.id+'">'+arr.menu_name_th+'<br>( '+arr.price+' บาท )</p></div>';
		}*/
	}

	$('#menu').html(html+'<br class="clear">');
	for(var i=0; i < count; i++ ){
		var arr = menus[i];
		$('#menu'+arr.id).data('menu_details', arr);

		$('#menu'+arr.id).unbind('click');
		$('#menu'+arr.id).click(function(){
			var menu_details = $(this).data('menu_details');
			createPopup(menu_details);
		});
	}
	$('#menu-list .close').unbind('click');
	$('#menu-list .close').click(function(){
		$('#menu-list').removeClass('visible');
	});
}
function createPopup(response){
	var html = '';
	html += '<div class="onerow menudetail">';
	html += '<div class="col4"><img style="height:350px; width:350px;" src="images/menu/'+response.id+'.jpg"></div>';
	html += '<div class="col8"><div>';
	html += '<h2 class="title">'+response.menu_name_th+'<span class="price">'+response.price+'<span> / '+response.unit+'</h2>'
	html += '<div class="order_units"><div class="col6"><input type="hidden" id="order_id" value="'+response.id+'"><select id="order_units"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="24">24</option></select><select id="order_takehome"><option value="0">ทานที่ร้าน</option><option value="1">กลับบ้าน</option></select><br><textarea id="order_desc"></textarea></div><div class="col6 last"><div class="desc">'+response.menu_desc+'</div></div><br class="clear"></div>';
	html += '<div id="order_now">สั่ง</div><div id="cancel">ยกเลิก</div>';
	html += '</div></div>';
	$('#pop .warp').html(html);
	$('#pop').show();

	$('#pop #cancel').unbind('click');
	$('#pop #cancel').click(function(){
		$('#pop').hide();
	});

	$('#pop #order_now').unbind('click');
	$('#pop #order_now').click(function(){
		var order_id = $('#order_id').val();
		var order_units = $('#order_units').val();
		var order_takehome = $('#order_takehome').val();
		var order_desc = $('#order_desc').val();
		ajaxCall( 'api.php', nowOrderResponse, { mod:'customer_detail', type:'addOrder', invoiceID:invoiceID, units:order_units, menuID:order_id, takehome:order_takehome, orderDesc:order_desc });
		$('#pop').hide();
	});
}
function nowOrderResponse(response){
	var details = response.details;
	var count = details.length;
	for(var i=0; i < count; i++ ){
		var arr = details[i];
		if(arr.status_id < 5){
			var btn = '<input id="cancel'+arr.id+'" class="cancelOrder" type="button" value="ยกเลิกรายการ"> <input id="finish'+arr.id+'" class="finishOrder" type="button" value="เสิร์ฟแล้ว"> '+arr.status;
		}else{
			var btn = arr.status;
		}
		//btn += ' <input id="deleteOrder'+arr.id+'" class="deleteOrder" type="button" value="ลบรายการ"> ';
		$('#order_list').append('<tr id="order'+arr.id+'"><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+btn+'</td></tr>');	
	}
	/*$('.deleteOrder').click(function(){
		$(this).unbind('click');
		var thisID = $(this).attr('id');
		deleteOrder(thisID);
	});	*/
	$('.cancelOrder').unbind('click');
	$('.cancelOrder').click(function(){
		var thisID = $(this).attr('id');
		cancelOrder(thisID);
	});
	
	$('.finishOrder').unbind('click');
	$('.finishOrder').click(function(){
		var thisID = $(this).attr('id');
		finishOrder(thisID);
	});

}
function finishResponse(response){
	ajaxCall( 'api.php', setInvoiceDetails, { mod:'customer_detail', type:'getInvoiceDetails', invoiceID:response.invoice_id });
}
function deleteOrder(thisID){
	var thisID = thisID.replace('deleteOrder','');
	ajaxCall( 'api.php', finishResponse, { mod:'customer_detail', type:'deleteOrder', orderID:thisID });
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
function finishSauna(thisID){
	var thisID = thisID.replace('finishSauna','');
	ajaxCall( 'api.php', finishResponse, { mod:'customer_detail', type:'finishSauna', orderID:thisID });
}
function finishRestaurant(thisID){
	var thisID = thisID.replace('finishRestaurant','');
	ajaxCall( 'api.php', finishResponse, { mod:'customer_detail', type:'finishRestaurant', orderID:thisID });
}
function deleteSauna(thisID){
	var thisID = thisID.replace('deleteSauna','');
	ajaxCall( 'api.php', finishResponse, { mod:'customer_detail', type:'deleteSauna', orderID:thisID });
}
function deleteMassage(thisID){
	var thisID = thisID.replace('deleteMassage','');
	ajaxCall( 'api.php', finishResponse, { mod:'customer_detail', type:'deleteMassage', orderID:thisID });
}

function newZoneResponse(response){
	if(response.process == 'success'){
		var html = '<div>';
		html += response.html
		html += '<div style="text-align:right;" id="cancel">ยกเลิก</div>';
		html += '</div>';
		$('#pop .warp').html(html);
		$('#pop').show();

		$('#pop #cancel').unbind('click');
		$('#pop #cancel').click(function(){
			$('#pop').hide();
		});
	}
}
function massageCal(){	
	var thisVal = $('.form #massage_type').val(); 
	var hours = $('.form #hours').val();
	if(thisVal == 0){
		var msgVal = hours*msgPrice1 ;
	}else if(thisVal == 1){
		var msgVal = hours*msgPrice1 ;
	}else if(thisVal == 2){
		var msgVal = (hours*msgPrice1)+msgPrice2 ;
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
		var hours			= $('.form #hours').val();
		var massage_type	= $('.form #massage_type').val();
		var massager_id		= $('.form #massager_id').val();
		var total			= $('.form #total').val();
		//alert(hours);
		if(total !='' && massager_id!='' && hours!=''){
			finishMassageHoursStep2(thisID, hours, massage_type, massager_id, total);
			$('#pop').hide();
		}else{
			alert('กรอกข้อมูลไม่ครบ');
		}
	});	
}
function finishMassageHoursStep2(thisID, hours, massage_type, massager_id, total){
	ajaxCall( 'api.php', finishResponse, { mod:'customer_detail', type:'finishMassageHours', orderID:thisID, hours:hours, massage_type:massage_type, massager_id:massager_id, total:total });
}