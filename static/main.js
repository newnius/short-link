$(function () {
	$('#main-tabs a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});

	$("#form-set-submit").click(function () {
		var url = $("#form-set-url").val();
		var token = $("#form-set-token").val();
		var remark = $('#form-set-remark').val();
		var valid_from = $('#form-set-valid-from').val();
		if (valid_from.length !== 0) {
			valid_from = moment(valid_from).unix();
		}
		var valid_to = $('#form-set-valid-to').val();
		if (valid_to.length !== 0) {
			valid_to = moment(valid_to).unix();
		}
		if (url.length < window.config.URL_MIN_LENGTH || url.length > window.config.URL_MAX_LENGTH) {
			$("#modal-msg-content").html("网址长度在 " + window.config.URL_MIN_LENGTH + " - " + window.config.URL_MAX_LENGTH);
			$("#modal-msg").modal('show');
			return true;
		}
		$("#form-set-submit").attr("disabled", "disabled");

		var ajax = $.ajax({
			url: window.config.BASE_URL + "/service?action=set",
			type: 'POST',
			data: {
				url: url,
				token: token,
				remark: remark,
				valid_from: valid_from,
				valid_to: valid_to
			}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				show_result(url, window.config.BASE_URL + "/" + res['token']);
			} else {
				$("#modal-msg-content").html(res["msg"]);
				$("#modal-msg").modal('show');
			}
			$("#form-set-submit").removeAttr("disabled");
		});
		ajax.fail(function (jqXHR, textStatus) {
			$("#form-set-submit").removeAttr("disabled");
			$("#modal-msg-content").html("Request failed :" + textStatus);
			$("#modal-msg").modal('show');
		});
	});

	$("#form-get-submit").click(function () {
		var token = $("#form-get-token").val();
		if (token.length === 0) {
			return true;
		}
		$("#form-get-submit").attr("disabled", "disabled");
		var ajax = $.ajax({
			url: window.config.BASE_URL + "/service?action=get",
			type: 'GET',
			data: {token: token}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				show_result(res['url'], window.config.BASE_URL + "/" + token);
			} else {
				$("#modal-msg-content").html(res["msg"]);
				$("#modal-msg").modal('show');
			}
			$("#form-get-submit").removeAttr("disabled");
		});
		ajax.fail(function (jqXHR, textStatus) {
			$("#form-get-submit").removeAttr("disabled");
			$("#modal-msg-content").html("Request failed :" + textStatus);
			$("#modal-msg").modal('show');
		});
	});

});

function show_result(url, shortUrl) {
	shortUrl = encodeURI(shortUrl);
	url = encodeURI(url);
	if (url.indexOf('//') === -1) {
		url = 'http://' + url;
	}
	$("#modal-result-title").text("短网址已生成");
	$("#modal-result-url").html('<a target="_blank" href="' + url + '">' + url + '</a>');
	$("#modal-result-token").html('<a target="_blank" href="' + shortUrl + '">' + shortUrl + '</a>');
	$("#modal-result-qrcode").attr("src", "");
	//$("#modal-result-qrcode").attr("src", "http://qr.liantu.com/api.php?w=160&m=5&text=" + encodeURIComponent(shortUrl));
	$("#modal-result-qrcode").attr("src", "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" + encodeURIComponent(shortUrl));
	$("#modal-result").modal("show");
}