var myOrder		=	[];
var menu;
var	cook		=	[];
var meat		=	[];
var currentMenu,currentMenuName,currentMenuPrice;
$(document).ready(function(){
	ajaxCall( 'api.php', setMenu, { mod:'menu' });
	//ajaxCall('api.php',setMenu,{mod:'menu',type:'list'});
});
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
		showMenu();
		$('#add').click(function(){
			$('#menu-list').addClass('visible');
			$('#search_menu').val('');
		});	
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
		}


		/*if(arr.cooking_type == cooking_list && arr.meat_type == meat_list){			
			html += '<div class="col3"><p id="menu'+arr.id+'">'+arr.menu_name_th+'<br>( '+arr.price+' บาท )</p></div>';
		}else if(cooking_list == '7' && cooking_list == arr.cooking_type){			
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
	html += '<div class="col4"><img src="images/menu/'+response.id+'.jpg"></div>';
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
		//var number	=	$('#number').val();
		var order_units = $('#order_units').val();
		
		//var	note	=	$('#note').val();
		var order_desc = $('#order_desc').val();
		var order_id = $('#order_id').val();
		var order_takehome = $('#order_takehome').val();

		myOrder.push({id:order_id,name:response.menu_name_th,number:order_units,note:order_desc,price:response.price});

		setMyOrder();
	});
}
function setMyOrder(){
	$('#pop #cancel').click();
	$('#menu-list .close').click();
	$('#order_list').html('');
	var sumPrice	=	0;
	if(myOrder.length > 0) {
		var html;			
		for(var i = 0; i < myOrder.length; i++) {
			sumPrice	=	sumPrice + (myOrder[i].price * myOrder[i].number);
			html +=	'<tr>';
			html +=	'<td>'+myOrder[i].number+'</td><td>'+myOrder[i].name+'</td>';
			html +=	'<td><span id = "del'+myOrder[i].id+'" class = "del">ลบรายการ</span></td>';
			html +=	'</tr>';
		}
		$('#order_list').html(html);
		$('.del').click(function(){
			var thisID	=	$(this).attr('id');
			thisID		=	thisID.replace('del','');
			for(var i = 0; i < myOrder.length; i++) {
				if(myOrder[i].id == thisID) {
					myOrder.splice(i,1);
					break;
				}
			}
			setMyOrder();			
		});	
		$('#chkbill').css('display','block');
		$('#chkbill').unbind('click');
		$('#chkbill').click(function(){
			var name = $('#name').val();
			if(name != ''){
				ajaxCall('api.php',chkbillResponse,{mod:'cashorder', type:'chkbill', order:myOrder, name:name});
			}else{
				alert('กรุณาใส่ข้อมูลลูกค้า');
			}
		});
	} else {
		$('#menuOrder').html('');
		$('#chkbill').css('display','none');
	}
}
function chkbillResponse(response){
	if(response.process == 'success') {
		alert('เพิ่มรายการสำเร็จ');
		location.reload('?mod=cashorder');
	} else {
		alert(response.msg);
	}
}