var goback = 0;
$(document).ready(function(){
	birthday();

	$('.header-back a').click(function(e){
		e.preventDefault();
		if(goback == 1){
			goback = 0;
			location.reload(); 
		}else{
			location.replace('?'); 
		}		
	});
});

function ajaxCall( _url, _successfunction, _data  ){
	$.post(_url, _data, _successfunction , "json" );
}
function birthday(){
	ajaxCall('api.php',birthdayResponse,{mod:'birthday'});
}
function birthdayResponse(response){
	if(response.process == 'success'){
		var birthList = response.customer;
		var len = birthList.length;
		var html = '';
		for(var i=0; i < len; i++){
			var arr = birthList[i];
			html += '<li><span>'+arr.id+'</span>'+arr.firstname+'<br>'+arr.lastname+'</li>';
		}		
	}else{
		var html = '<li><span></span>NO DATA</li>';
	}
	$('#birthday-list').html(html);
	$('#birthday').click(function(e){
		var birthdaybox = $('#birthday-list');
		var open = birthdaybox.is(':visible');
		if (!open){
			birthdaybox.slideDown('medium', function(e){
				$('body').click(function(e){
					birthdaybox.slideUp();
				});
			})
		}else{
			birthdaybox.slideUp('medium', function(e){
			});
		}
		birthdaybox.click(function(e){
			e.stopPropagation();
		});
		$('body').unbind('click');
		e.preventDefault();
		
	});
}
function setDateFeild(){
	var d = new Date();
	var currentYear = d.getFullYear();
	var currentMonth = d.getMonth();
	currentMonth++;
	var currentDate = d.getDate();

	console.log(currentYear);

	// Year
	var option = '';
	for(var year = 2016; year <= currentYear; currentYear--){
		option += '<option value="'+currentYear+'">'+currentYear+'</option>';
	}
	$('#yyyy').html(option);

	// Month
	var option = '';
	for(var i = 1; i <= 12; i++){
		var selected = '';
		if(i == currentMonth){
			selected = ' selected';
		}
		option += '<option value="'+i+'"'+selected+'>'+i+'</option>';
	}
	$('#mm').html(option);

	// Date
	var option = '';
	for(var i = 1; i <= 31; i++){
		var selected = '';
		if(i == currentDate){
			selected = ' selected';
		}
		option += '<option value="'+i+'"'+selected+'>'+i+'</option>';
	}
	$('#dd').html(option);
}