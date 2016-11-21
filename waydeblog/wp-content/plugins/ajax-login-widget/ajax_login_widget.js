var alw_status;
var alw_loginForm, alw_registerForm, alw_lostPasswordForm;
var alw_loginMessage, alw_registerMessage, alw_lostPasswordMessage;
var alw_sack = new sack();

var alw_otheronload = window.onload;
window.onload = alw_init;
function alw_init() {

	if (alw_otheronload) alw_otheronload();

	alw_status = 0;

	alw_loginForm = document.getElementById("alw_loginForm");
	alw_registerForm = document.getElementById("alw_registerForm");
	alw_lostPasswordForm = document.getElementById("alw_lostPasswordForm");

	alw_loginMessage = document.getElementById("alw_loginMessage");
	alw_registerMessage = document.getElementById("alw_registerMessage");
	alw_lostPasswordMessage = document.getElementById("alw_lostPasswordMessage");
}


function alw_showLogin() {

	document.getElementById("alw_login").style.display = "none";
	document.getElementById("alw_register").style.display = "none";
	document.getElementById("alw_lostPassword").style.display = "none";

	if (0 != alw_timeout) {
		setTimeout('alw_showLogin2();', alw_timeout);
	} else {
		alw_showLogin2();
	}
}

function alw_showLogin2() {
	document.getElementById("alw_loading_login").style.display = "none";
	document.getElementById("alw_login").style.display = "block";
	alw_loginForm.log.focus();

}

function alw_showRegister() {

	document.getElementById("alw_login").style.display = "none";
	document.getElementById("alw_register").style.display = "none";
	document.getElementById("alw_lostPassword").style.display = "none";

	if (0 != alw_timeout) {
		setTimeout('alw_showRegister2();', alw_timeout);
	} else {
		alw_showRegister2();
	}
}

function alw_showRegister2() {

	document.getElementById("alw_loading_register").style.display = "none";
	document.getElementById("alw_register").style.display = "block";

	alw_registerForm.user_login.focus();
}


function alw_showLostPassword() {

	document.getElementById("alw_login").style.display = "none";
	document.getElementById("alw_register").style.display = "none";
	document.getElementById("alw_lostPassword").style.display = "none";

	if (0 != alw_timeout) {
		setTimeout('alw_showLostPassword2();', alw_timeout);
	} else {
		alw_showLostPassword2();
	}
}

function alw_showLostPassword2() {

	document.getElementById("alw_loading_lost").style.display = "none";
	document.getElementById("alw_lostPassword").style.display = "block";

	alw_lostPasswordForm.user_login.focus();
}


function alw_login() {

	if (0 != alw_status) {
		return;
	}

	if (alw_loginForm.log.value == '') {
		alert("Please enter username.");
		alw_loginForm.log.focus();
		return;
	}

	if (alw_loginForm.pwd.value == '') {
		alert("Please enter password.");
		alw_loginForm.pwd.focus();
		return;
	}

          document.getElementById("alw_loading_login").style.display = "inline";

	alw_sack.setVar("log", alw_loginForm.log.value);
	alw_sack.setVar("pwd", alw_loginForm.pwd.value);
	alw_sack.setVar("rememberme", alw_loginForm.rememberme.value);

	alw_sack.requestFile = alw_base_uri + "/wp-content/plugins/ajax-login-widget/login.php";
	alw_sack.method = "POST";
	alw_sack.onError = alw_ajaxError;
	alw_sack.onCompletion = alw_loginHandleResponse;
	alw_sack.runAJAX();
	alw_status = 1;

}

function alw_loginHandleResponse() {
	alw_status = 0;

	var responselines = alw_sack.response.split("\n",2);
	if (responselines[0] == alw_failure) {
                    document.getElementById("alw_loading_login").style.display = "none";
		alert(responselines[1]);
		return;
	}
	if (responselines[0] == alw_success) {
		
		if (alw_redirectOnLogin == '')
			window.location.reload(true);
		else
			window.location.href = alw_redirectOnLogin;
			
		return;
	}

	alert("Unknown login response.");

}

function alw_register() {

	if (0 != alw_status) {
		return;
	}

	if (alw_registerForm.user_login.value == '') {
		alert("Please enter username.");
		alw_registerForm.user_login.focus();
		return;
	}

	if (alw_registerForm.user_email.value == '') {
		alert("Please enter e-mail address.");
		alw_registerForm.user_email.focus();
		return;
	}

          document.getElementById("alw_loading_register").style.display = "inline";

	alw_sack.setVar("user_login", alw_registerForm.user_login.value);
	alw_sack.setVar("user_email", alw_registerForm.user_email.value);

	alw_sack.requestFile = alw_base_uri + "/wp-content/plugins/ajax-login-widget/register.php";
	alw_sack.method = "POST";
	alw_sack.onError = alw_ajaxError;
	alw_sack.onCompletion = alw_registerHandleResponse;
	alw_sack.runAJAX();
	alw_status = 1;

}

function alw_registerHandleResponse() {

	alw_status = 0;
          document.getElementById("alw_loading_register").style.display = "none";

	var responselines = alw_sack.response.split("\n",2);
	if (responselines[0] == alw_failure) {
		alert(responselines[1]);
		return;
	}
	if (responselines[0] == alw_success) {
		alert("Registration complete. Please check your e-mail.");
		alw_loginMessage.innerHTML = "Your password is in your mail.<br/>";
		alw_loginForm.log.value = alw_registerForm.user_login.value;
		alw_registerForm.user_login.value = "";
		alw_registerForm.user_email.value = "";
		alw_showLogin();
		alw_loginForm.pwd.focus();
		return;
	}

	alert("Unknown registration response.");

}

function alw_retrievePassword() {
	if (0 != alw_status) {
		return;
	}

	if (alw_lostPasswordForm.user_login.value == '') {
		alert("Please enter username.");
		alw_lostPasswordForm.user_login.focus();
		return;
	}

	if (alw_lostPasswordForm.user_email.value == '') {
		alert("Please enter e-mail address.");
		alw_lostPasswordForm.user_email.focus();
		return;
	}

          document.getElementById("alw_loading_lost").style.display = "inline";

	alw_sack.setVar("user_login", alw_lostPasswordForm.user_login.value);
	alw_sack.setVar("user_email", alw_lostPasswordForm.user_email.value);

	alw_sack.requestFile = alw_base_uri + "/wp-content/plugins/ajax-login-widget/lostpassword.php";
	alw_sack.method = "POST";
	alw_sack.onError = alw_ajaxError;
	alw_sack.onCompletion = alw_lostPasswordHandleResponse;
	alw_sack.runAJAX();
	alw_status = 1;
}

function alw_lostPasswordHandleResponse() {
	alw_status = 0;
          document.getElementById("alw_loading_lost").style.display = "none";

	var responselines = alw_sack.response.split("\n",2);
	if (responselines[0] == alw_failure) {
		alert(responselines[1]);
		return;
	}
	if (responselines[0] == alw_success) {
		alert("Check your e-mail for the reset password link.");
		alw_loginMessage.innerHTML = "Your reset password link is in your e-mail.<br/>";
		alw_loginForm.log.value = alw_lostPasswordForm.user_login.value;
		alw_lostPasswordForm.user_login.value = "";
		alw_lostPasswordForm.user_email.value = "";
		alw_showLogin();
		alw_loginForm.pwd.focus();
		return;
	}

	alert("Unknown password retrieval response.");

}

function alw_ajaxError() {
	alert("We are sorry, there was an error while sending the request.\nPlease try again!\nIf error persists, please contact the webmaster.");
	
	alert(alw_sack.responseStatus[0] + ':\n' + alw_sack.response);
	alw_sack = new sack();
}

function alw_loginOnEnter(e) {

	if(window.event) // IE
		keynum = e.keyCode;
	else if(e.which) // Netscape/Firefox/Opera
		keynum = e.which;
	else
		keynum = 0;

	if (keynum==13)
		alw_login();

}
function alw_registerOnEnter(e) {

	if(window.event) // IE
		keynum = e.keyCode;
	else if(e.which) // Netscape/Firefox/Opera
		keynum = e.which;
	else
		keynum = 0;

	if (keynum==13)
		alw_register();

}
function alw_retrievePasswordOnEnter(e) {

	if(window.event) // IE
		keynum = e.keyCode;
	else if(e.which) // Netscape/Firefox/Opera
		keynum = e.which;
	else
		keynum = 0;

	if (keynum==13)
		alw_retrievePassword();

}
