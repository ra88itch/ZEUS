$(document).ready(function(){
	$('#old-password').focus();
	$('#change-password').click(function(){
		chkPassword();
	});
});
function chkPassword(){
	var oldPass = $('#old-password').val();
	var newPass = $('#new-password').val();
	var confirmPass = $('#confirm-password').val();
	if(oldPass==''){
		alert('โปรดยืนยันรหัสผ่านเดิม');
		$('#old-password').focus();
		return false;
	}
	if(newPass==''){
		alert('โปรดยืนยันรหัสผ่านใหม่');
		$('#new-password').focus();
		return false;
	}
	if(confirmPass==''){
		alert('โปรดยืนยันรหัสผ่าน');
		$('#confirm-password').focus();
		return false;
	}
	if(newPass != confirmPass){
		alert('รหัสผ่านไม่ตรงกัน');
		$('#new-password').val('');
		$('#confirm-password').val('');
		$('#new-password').focus();
		$('#confirm-password').focus();
		return false;
	}else{
		changePassword(oldPass, newPass);
	}
}
function changePassword(oldPass, newPass){
	ajaxCall( 'api.php', changePasswordResponse, { mod:'profile', oldPass:oldPass, newPass:newPass });
}
function changePasswordResponse(response){
	if(response.process == 'failed' ){
		alert('รหัสผ่านเดิมไม่ถูกต้อง');
		$('#old-password').focus();
		return false;
	}else{
		$('#old-password').val('')
		$('#new-password').val('');
		$('#confirm-password').val('');
		alert('แก้ไขรหัสผ่านเรียบร้อย');
	}
}