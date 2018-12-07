$(function () {
	$("#btn-signout, #btn-signout-header").click(function (e) {
		e.preventDefault();
		console.log('loginout');
		var ajax = $.ajax({
			url: "/service?action=user_signout",
			type: 'POST',
			data: {}
		});
		ajax.done(function (res) {
			window.location.pathname = "/";
		});
	});

	$('.date-picker').datetimepicker();
});