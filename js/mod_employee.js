var mod = '?mod=employee';
var wait = 0;
$(document).ready(function(){
	$('#add').click(function(){
		ajaxCall( 'api.php', addResponsed, { mod:'employee', type: 'add' } );
	});	
	$('.edit').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('edit','');
		ajaxCall( 'api.php', editResponsed, { mod:'employee', employeeID: thisID, type: 'edit' } );
	});	
	$('.status').click(function(){
		var thisID = $(this).attr('id');
		thisID = thisID.replace('status','');
		ajaxCall( 'api.php', statusResponsed, { mod:'employee', employeeID: thisID, type: 'changeEmployeeStatus' } );
	});	
});

function addResponsed(response){
	if(response.process == 'success'){
		setPop(response.html);
		$('#submit').click(function(){
			var code			= $('#emCode').val();
			var nickname		= $('#nickname').val();
			var firstname		= $('#firstname').val();
			var lastname		= $('#lastname').val();
			var dob				= $('#dob').val();
			var address			= $('#address').val();
			var position		= $('#position').val();
			var salary			= $('#salary').val();
			var active			= $('#active').val();
			var file			= $('#file').val();
			
			if(code==''){
				alert('Employee Code is empty.');
				return false;
			}
			if(nickname == '' && firstname == ''){
				alert('Firstname or nickname is empty');
				return false;
			}
			
			if(file!='') {
				wait = 1;
				$('#addMenu').submit();
			}
			
			ajaxCall( 'api.php', addNewResponsed, { mod:'employee', type: 'addNewEmployee', code: code, nickname: nickname, firstname: firstname, lastname: lastname, dob:dob, position:position, salary:salary, active:active, file:file ,address:address} );
			
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
		$('#submit').click(function(){
			var employeeID			= $('#employeeID').val();
			var code				= $('#code').val();
			var nickname			= $('#nickname').val();
			var firstname			= $('#firstname').val();
			var lastname			= $('#lastname').val();
			var dob					= $('#dob').val();
			var address				= $('#address').val();
			var position			= $('#position').val();
			var salary				= $('#salary').val();
			var active				= $('#active').val();
			var file				= $('#file').val();
			if(nickname == '' && firstname == ''){
				alert('Nickname or firstname is empty');
				return false;
			}
			if(file!='') {
				wait = 1;
				$('#editEmployee').submit();
			}
			
			ajaxCall( 'api.php', editEmployeeResponsed, { mod:'employee', type: 'updateEmployee', employeeID: employeeID, code:code, nickname: nickname, firstname: firstname, lastname: lastname, dob:dob, address:address, position:position, salary:salary, active:active, file:file } );			
		
		});	
		
		$('#editEmployee').submit(function(e){
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
	}else{
		alert(response.msg);
	}
}
function editEmployeeResponsed(response){
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
		if(response.active == 0){
			$('#status'+response.employee).addClass('lock');			
		}else{
			$('#status'+response.employee).removeClass('lock');
		}
	}else{
		alert(response.msg);
	}
}
function imageIsLoaded(e) {
	$('#img_preview').attr('src', e.target.result);
	$('#img_preview').attr('width', '250px');
};