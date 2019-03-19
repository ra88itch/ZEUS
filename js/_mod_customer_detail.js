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
		var restaurant = '<div><h3>เลือกข้อมูลลูกค้านวด</h3>';
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
					snooker += html;
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
		$('#customer-detail').html('<h3>'+text+'</h3>');
		$('#customer-detail').fadeIn();
		invoiceID = $(this).attr('id');
		invoiceID = invoiceID.replace('inv','');
		ajaxCall( 'api.php', setInvoiceDetails, { mod:'customer_detail', type:'getInvoiceDetails', invoiceID:invoiceID });
		ajaxCall( 'api.php', setMenu, { mod:'menu' });
	});		
}
function setInvoiceDetails(response){
	if(response.process == 'success'){
		var html = $('#customer-detail').html();
		var details = response.details;
		var count = details.length;
		html += '<table><thead><tr><td>Unit</td><td>Description</td><td>Amount</td><td>Status</td></tr></thead><tbody>';
		for(var i=0; i < count; i++ ){
			var arr = details[i];
			var amount = arr.unit * arr.price;
			html += '<tr><td>'+arr.unit+'</td><td>'+arr.order_name+'</td><td>'+amount+'</td><td>Status</td></tr>';
		}
		html += '</tbody></table><div class="open">ADD MENU</div>';
		$('#customer-detail').html(html);
		$('#customer-detail .open').click(function(){
			$('#menu-list').addClass('visible');
		});
		
	}
}
function setMenu(response){
	if(response.process == 'success'){
		menus = response.menu;
		
		var html = '<h3>เพิ่มรายการอาหาร</h3><span class="close">ปิด</span>';
		var cooking_list = response.cooking_list;
		var count_cooking_list = cooking_list.length;
		html += '<select id="cooking_list">';
		for(var i=0; i < count_cooking_list; i++ ){
			var arr = cooking_list[i];
			html += '<option value="'+arr.id+'">'+arr.type_cooking+'</option>';			
		}
		var meat_list = response.meat_list;
		var count_meat_list = meat_list.length;
		html += '</select><select id="meat_list">';
		for(var i=0; i < count_meat_list; i++ ){
			var arr = meat_list[i];
			html += '<option value="'+arr.id+'">'+arr.type_meat+'</option>';			
		}
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

		showMenu();
	}
}
function showMenu(){	
	var cooking_list = $('#cooking_list').val();
	var meat_list = $('#meat_list').val();	
	var count = menus.length;
	var html = '';
	for(var i=0; i < count; i++ ){
		var arr = menus[i];
		html += '<div class="col3"><p id="menu'+arr.id+'">'+arr.menu_name_th+'<br>( '+arr.price+' บาท )</p></div>';
	}
	$('#menu').html(html+'<br class="clear">');
	$('#menu-list .close').click(function(){
		$('#menu-list').removeClass('visible');
	});
}
function filterMenu(){	
	var cooking_list = $('#cooking_list').val();
	var meat_list = $('#meat_list').val();	
	var count = menus.length;
	var html = '';
	for(var i=0; i < count; i++ ){
		var arr = menus[i];
		if(arr.cooking_type == cooking_list && arr.meat_type == meat_list){			
			html += '<div class="col3"><p id="menu'+arr.id+'">'+arr.menu_name_th+'<br>( '+arr.price+' บาท )</p></div>';
		}else if(cooking_list == '7' && cooking_list == arr.cooking_type){			
			html += '<div class="col3"><p id="menu'+arr.id+'">'+arr.menu_name_th+'<br>( '+arr.price+' บาท )</p></div>';
		}else if(cooking_list == '8' && cooking_list == arr.cooking_type){			
			html += '<div class="col3"><p id="menu'+arr.id+'">'+arr.menu_name_th+'<br>( '+arr.price+' บาท )</p></div>';
		}else if(cooking_list == '9' && cooking_list == arr.cooking_type){			
			html += '<div class="col3"><p id="menu'+arr.id+'">'+arr.menu_name_th+'<br>( '+arr.price+' บาท )</p></div>';
		}
	}
	$('#menu').html(html+'<br class="clear">');
	$('#menu-list .close').click(function(){
		$('#menu-list').removeClass('visible');
	});
}