$(function () {
	$("#btn-signout").click(function (e) {
		e.preventDefault();
		var ajax = $.ajax({
			url: window.config.BASE_URL + "/service?action=user_signout",
			type: 'POST',
			data: {}
		});
		ajax.done(function (res) {
			window.location.pathname = "/";
		});
	});

	$("#btn-oauth-login").click(function (e) {
		e.preventDefault();
		var ajax = $.ajax({
			url: window.config.BASE_URL + "/service?action=oauth_get_url",
			type: 'POST',
			data: {}
		});
		ajax.done(function (res) {
			window.location.href = res['url'];
		});
	});

	$('.date-picker').datetimepicker();

	check_login();
});

function check_login() {
	var ajax = $.ajax({
		url: window.config.BASE_URL + "/service?action=check_login",
		type: 'GET',
		data: {}
	});
	ajax.done(function (res) {
		if (res['need_redirect']=='true') {
			$( "#btn-oauth-login" ).trigger( "click" );
		}
	});
}