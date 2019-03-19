$(document).ready(function(){
	$('#username').focus();

	$('input').keypress(function (ev) {
		var keycode = (ev.keyCode ? ev.keyCode : ev.which);
		if (keycode == '13') {
			$('.login-submit').click();
		}
	})

	$('.login-submit').click(function(){	
		var username = $('#username').val();
		var password = $('#password').val();
	
		ajaxCall( 'api.php?dev', loginResponse, { mod:'login', username: username, password: password } );
	});
});
function loginResponse(response){
	if(response.process == 'success'){
		$('.login-form p').hide();
		$('.login-form').append('<p class="login_S">Login Success</p>');
		setTimeout(function(){
			location.reload();
		},1500);
	}else{
		$('.login-form').append('<p class="login_f">Login Failed</p>');
	}
}