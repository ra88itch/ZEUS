var mod = '?mod=mmenu';
var wait = 0;
$(document).ready(function(){
	$('#cooking_type').change(function(){
		var thisID = $(this).val();
		window.location.href = mod+'&type='+thisID;
	});	
	$('#add').click(function(){
		ajaxCall( 'api.php', addResponsed, { mod:'mmenu', type: 'add' } );
	});	
	$('.edit').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('edit','');
		ajaxCall( 'api.php', editResponsed, { mod:'mmenu', menu_id: thisID, type: 'edit' } );
	});	
	$('.status').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('status','');
		ajaxCall( 'api.php', statusResponsed, { mod:'mmenu', menu_id: thisID, type: 'changeMenuStatus' } );
	});	
});

function addResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		var d = new Date();
		var toDay = d.getDate() + '/' + (d.getMonth() + 1) + '/' + (d.getFullYear());
		$('#future_date').datepicker({ dateFormat: "yy-mm-dd" });
		$('#submit').click(function(){
			var name_th				= $('#menu_name_th').val();
			var menu_desc			= $('#menu_desc').val();
			var typeCook			= $('#type_by_cooking').val();
			var typeMeat			= $('#type_by_meat').val();
			var price				= $('#price').val();
			var unit				= $('#unit').val();
			var stock_id			= $('#stock_id').val();
			var status				= $('#active').val();
			var file				= $('#file').val();

			var future_price		= $('#future_price').val();
			var future_date			= $('#future_date').val();
			
			if(name_th==''){
				alert('Menu name is empty');
				return false;
			}
			
			if(file!='') {
				wait = 1;
				$('#addMenu').submit();
			}else{
				ajaxCall( 'api.php', addNewResponsed, { mod:'mmenu', type: 'addNewMenu', name_th: name_th, menu_desc: menu_desc, typeCook:typeCook, typeMeat:typeMeat, price:price, unit:unit, status:status, file:file, future_price:future_price, future_date:future_date, stock_id:stock_id } );
			}		
		});	
		
		$('#addMenu').submit(function(e){
			e.preventDefault();
			$.ajax({
				url: "api.php", // Url to which the request is send
				type: "POST",             // Type of request to be send, called as method
				data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
				contentType: false,       // The content type used when sending data to the server.
				cache: false,             // To unable request pages to be cached
				processData:false,        // To send DOMDocument or non processed data file it is set to false
				success: function(data)   // A function to be called if request succeeds
				{
					alert(data);
					location.reload(mod);
				}
			});
		});
	}else{
		alert(response.msg);
	}
}
function editResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		var d = new Date();
		var toDay = d.getDate() + '/' + (d.getMonth() + 1) + '/' + (d.getFullYear());
		$('#future_date').datepicker({ dateFormat: "yy-mm-dd" });
		$('#submit').click(function(){
			var menu_id				= $('#menu_id').val();
			var name_th				= $('#menu_name_th').val();
			var menu_desc			= $('#menu_desc').val();
			var typeCook			= $('#type_by_cooking').val();
			var typeMeat			= $('#type_by_meat').val();
			var price				= $('#price').val();
			var unit				= $('#unit').val();
			var stock_id			= $('#stock_id').val();
			var status				= $('#active').val();
			var file				= $('#file').val();

			var future_price		= $('#future_price').val();
			var future_date			= $('#future_date').val();

			if(name_th==''){
				alert('Menu name is empty');
				return false;
			}
			if(file!='') {
				wait = 1;				
				$('#editMenu').submit();
			}else{
				ajaxCall( 'api.php', editMenuResponsed, { mod:'mmenu', type: 'updateMenu', menu_id: menu_id, name_th: name_th, menu_desc: menu_desc, typeCook:typeCook, typeMeat:typeMeat, price:price, unit:unit, status:status, file:file, future_price:future_price, future_date:future_date, stock_id:stock_id } );			
			}
		});	
		
		$('#editMenu').submit(function(e){
			e.preventDefault();
			$.ajax({
				url: "api.php", // Url to which the request is send
				type: "POST",             // Type of request to be send, called as method
				data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
				contentType: false,       // The content type used when sending data to the server.
				cache: false,             // To unable request pages to be cached
				processData:false,        // To send DOMDocument or non processed data file it is set to false
				success: function(data)   // A function to be called if request succeeds
				{
					alert(data);
					location.reload(mod);
				}
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

	$(function() {
		$("#file").change(function() {
			var file = this.files[0];
			var imagefile = file.type;
			var match= ["image/jpeg","image/jpg"];
			if(!((imagefile==match[0]) || (imagefile==match[1]) ))	{
					//subtleMessage('Message', 'Invalid file', 'OK');
					alert('Invalid File');
					$('#submit').attr('disabled','true');
					return false;
			}	else	{
				var reader = new FileReader();
				reader.onload = imageIsLoaded;
				reader.readAsDataURL(this.files[0]);
				$('#submit').removeAttr('disabled');
			}
		});
	});
}
function addNewResponsed(response){
	if(response.process == 'success'){
		alert(response.msg);
		if(wait==0) { 
			location.reload(mod);
		}
	}else if(response.process == 'already'){
		//alert(response.msg);
		ajaxCall( 'api.php', editResponsed, { mod:'mmenu', menu_id: response.menu_id, type: 'edit' } );
	}else{
		alert(response.msg);
	}
}
function editMenuResponsed(response){
	if(response.process == 'success'){
		alert(response.msg);
		if(wait==0) {
			location.reload(mod);
		}
	}else{
		alert(response.msg);
	}
}
function statusResponsed(response){
	if(response.process == 'success'){
		//alert(response.msg);
		if(response.active == 0){
			$('#status'+response.menu).addClass('lock');			
		}else{
			$('#status'+response.menu).removeClass('lock');
		}
	}else{
		alert(response.msg);
	}
}
function imageIsLoaded(e) {
	$('#img_preview').attr('src', e.target.result);
	$('#img_preview').attr('width', '250px');
};