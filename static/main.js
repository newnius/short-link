$(function () {
	$('#main-tabs a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});

	$("#form-set-submit").click(function () {
		set($("#form-set-url").val(), $("#form-set-token").val());
		return false;
	});

	$("#form-get-submit").click(function () {
		get($("#form-get-token").val());
		return false;
	});

	$('.date-picker').datetimepicker();
	/* reset time as the browser will remember last choice */
	$('#form-set-valid-from').val('');
	$('#form-set-valid-to').val('');

});


function set(url, token) {
	if (url.length < 1 || url.length > URL_MAX_LENGTH) {
		$("#modal-msg-content").html("网址长度在 1-500");
		$("#modal-msg").modal('show');
		return false;
	}
	$("#form-set-submit").attr("disabled", "disabled");
	var remark = $('#form-set-remark').val();
	var limit = $('#form-set-limit').val();
	var valid_from = $('#form-set-valid-from').val();
	if(valid_from.length !== 0){
		valid_from = moment(valid_from).unix();
	}
	var valid_to = $('#form-set-valid-to').val();
	if(valid_to.length !== 0){
		valid_to = moment(valid_to).unix();
	}

	var ajax = $.ajax({
		url: BASE_URL + "/service?action=set",
		type: 'POST',
		data: {
			url: url,
			token: token,
			remark: remark,
			limit: limit,
			valid_from: valid_from,
			valid_to: valid_to
		}
	});
	ajax.done(function (res) {
		if (res["errno"] === 0) {
			show_result(url, BASE_URL + "/" + res['token']);
		} else {
			$("#modal-msg-content").html(res["msg"]);
			$("#modal-msg").modal('show');
		}
		$("#form-set-submit").removeAttr("disabled");
	});
	ajax.fail(function (jqXHR, textStatus) {
		$("#form-set-submit").removeAttr("disabled");
		$("#modal-msg-content").html(res["msg"]);
		$("#modal-msg").modal('show');
	});
}

function get(token) {
	$("#form-get-submit").attr("disabled", "disabled");
	var ajax = $.ajax({
		url: BASE_URL + "/service?action=get",
		type: 'GET',
		data: {token: token}
	});
	ajax.done(function (res) {
		if (res["errno"] === 0) {
			show_result(res['url'], BASE_URL + "/" + token);
		} else {
			$("#modal-msg-content").html(res["msg"]);
			$("#modal-msg").modal('show');
		}
		$("#form-get-submit").removeAttr("disabled");
	});
	ajax.fail(function (jqXHR, textStatus) {
		$("#form-get-submit").removeAttr("disabled");
		$("#modal-msg-content").html(res["msg"]);
		$("#modal-msg").modal('show');
	});
}

function show_result(url, shortUrl) {
	$("#modal-result-title").text("短网址已生成");
	$("#modal-result-url").html("<a target='_blank' href='" + url + "'>" + url + "</a>");
	$("#modal-result-token").text(shortUrl);
	$("#modal-result-qrcode").attr("src", "http://qr.liantu.com/api.php?w=160&m=5&text=" + encodeURIComponent(shortUrl));
	$("#modal-result").modal("show");
}