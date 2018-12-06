function register_events_link() {
	$('#btn-link-add').click(function (e) {
		$('#modal-link-title').html('添加短链接');
		$('#form-link-url').val('');
		$('#form-link-token').val('');
		$('#form-link-token').removeAttr('disabled');
		$('#form-link-remark').val('');
		$('#form-link-valid-from').val('');
		$('#form-link-valid-to').val('');
		$('#form-link-submit-type').val('add');
		$("#form-link-msg").html('');
		$('#form-link-remove').addClass('hidden');
		$('#modal-link').modal('show');
	});

	$("#form-link-submit").click(function (e) {
		var url = $('#form-link-url').val();
		var token = $('#form-link-token').val();
		if (url.length < 1 || url.length > URL_MAX_LENGTH) {
			$("#form-link-msg").html("网址长度在 1-500");
			return false;
		}
		$("#form-link-submit").attr("disabled", "disabled");
		var remark = $('#form-link-remark').val();
		var valid_from = $('#form-link-valid-from').val();
		if (valid_from.length !== 0) {
			valid_from = moment(valid_from).unix();
		}
		var valid_to = $('#form-link-valid-to').val();
		if (valid_to.length !== 0) {
			valid_to = moment(valid_to).unix();
		}
		var action = 'set';
		if ($('#form-link-submit-type').val() === 'update') {
			action = 'update';
		}
		var ajax = $.ajax({
			url: BASE_URL + "/service?action=" + action,
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
				$("#modal-link").modal('hide');
				$('#table-link').bootstrapTable("refresh");
			} else {
				$("#form-link-msg").html(res["msg"]);
			}
			$("#form-link-submit").removeAttr("disabled");
			$("#form-link-remove").removeAttr("disabled");
		});
		ajax.fail(function (jqXHR, textStatus) {
			$("#form-link-msg").html("Request failed :" + textStatus);
			$("#form-link-submit").removeAttr("disabled");
			$("#form-link-remove").removeAttr("disabled");
		});
	});

	$("#form-link-remove").click(function (e) {
		$("#form-link-submit").attr("disabled", "disabled");
		$("#form-link-remove").attr("disabled", "disabled");
		var remark = $("#form-link-remark").val();
		var token = $("#form-link-token").val();
		if (!confirm('确认删除' + remark + '吗（操作不可逆）？')) {
			return;
		}
		var ajax = $.ajax({
			url: "/service?action=remove",
			type: 'POST',
			data: {token: token}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				$("#modal-link").modal('hide');
				$('#table-link').bootstrapTable("refresh");
			} else {
				$("#form-link-msg").html(res["msg"]);
			}
			$("#form-link-submit").removeAttr("disabled");
			$("#form-link-remove").removeAttr("disabled");
		});
		ajax.fail(function (jqXHR, textStatus) {
			$("#form-link-msg").html("Request failed :" + textStatus);
			$("#form-link-submit").removeAttr("disabled");
			$("#form-link-remove").removeAttr("disabled");
		});
	});

	$("#modal-analytics-interval").on('click', 'li a', function (e) {
		if($(this).parent().hasClass('disabled')){
			return ;
		}
		var interval = parseInt($(this).attr('data-interval'));
		var token = $("#modal-analytics-interval").attr('data-token');
		$('#modal-analytics').modal('hide');
		$("#modal-msg").modal('show');
		$("#modal-msg-content").html('Fetching data, please wait...');
		var ajax = $.ajax({
			url: "/service?action=analyze",
			type: 'GET',
			data: {
				token: token,
				interval: interval
			}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				$("#modal-msg").modal('hide');
				var rs = res['hist'];
				show_analytics(token, rs, interval);
			} else {
				$("#modal-msg-content").html(res["msg"]);
			}
		});
		ajax.fail(function (jqXHR, textStatus) {
			$("#modal-msg-content").html("Request failed :" + textStatus);
		});
	});
}

function load_links(scope) {
	var $table = $("#table-link");
	$table.bootstrapTable({
		url: '/service?action=list&who=' + scope,
		responseHandler: linkResponseHandler,
		cache: false,
		striped: true,
		pagination: true,
		pageSize: 10,
		pageList: [10, 25, 50, 100, 200],
		search: true,
		showColumns: true,
		showRefresh: true,
		showToggle: false,
		showPaginationSwitch: true,
		minimumCountColumns: 2,
		clickToSelect: false,
		sortName: 'nobody',
		sortOrder: 'desc',
		smartDisplay: true,
		mobileResponsive: true,
		showExport: true,
		columns: [{
			field: 'selected',
			title: 'Select',
			checkbox: true
		}, {
			field: 'owner',
			title: 'Owner',
			align: 'center',
			valign: 'middle',
			sortable: true,
			visible: scope === 'all'
		}, {
			field: 'remark',
			title: '备注',
			align: 'center',
			valign: 'middle',
			sortable: false
		}, {
			field: 'token',
			title: '短链接',
			align: 'center',
			valign: 'middle',
			visible: scope === 'self'
		}, {
			field: 'url',
			title: '原始链接',
			align: 'center',
			valign: 'middle'
		}, {
			field: 'status',
			title: '当前状态',
			align: 'center',
			valign: 'middle',
			sortable: true,
			formatter: statusFormatter,
			visible: true
		}, {
			field: 'time',
			title: '创建时间',
			align: 'center',
			valign: 'middle',
			sortable: true,
			formatter: timeFormatter,
			visible: scope === 'all'
		}, {
			field: 'valid_from',
			title: '有效期自',
			align: 'center',
			valign: 'middle',
			sortable: true,
			formatter: timeFormatter,
			visible: scope === 'self'
		}, {
			field: 'valid_to',
			title: '有效期至',
			align: 'center',
			valign: 'middle',
			sortable: true,
			formatter: timeFormatter,
			visible: scope === 'self'
		}, {
			field: 'operate',
			title: 'Operate',
			align: 'center',
			events: linkOperateEvents,
			formatter: linkOperateFormatter
		}]
	});
}

var statusFormatter = function (status) {
	switch (status) {
		case '0':
			return '正常';
		case '1':
			return '已暂停';
		case '2':
			return '已禁用';
		case '3':
			return '已删除';
	}
	return '未知状态(' + status + ')';
};

function linkResponseHandler(res) {
	if (res['errno'] === 0) {
		return res['links'];
	}
	$("#modal-msg-content").html(res["msg"]);
	$("#modal-msg").modal('show');
	return [];
}

function linkOperateFormatter(value, row, index) {
	var div = '<div class="btn-group" role="group" aria-label="...">';
	div += '<button class="btn btn-default stats"><i class="glyphicon glyphicon-stats"></i>&nbsp;</button>';
	if (page_type === 'links')
		div += '<button class="btn btn-default edit"><i class="glyphicon glyphicon-edit"></i>&nbsp;</button>';
	if (page_type === 'links' && row.status === '1')
		div += '<button class="btn btn-default resume"><i class="glyphicon glyphicon-play"></i>&nbsp;</button>';
	if (page_type === 'links' && row.status === '0')
		div += '<button class="btn btn-default pause"><i class="glyphicon glyphicon-pause"></i>&nbsp;</button>';
	if (page_type === 'links_all' && row.status === '0')
		div += '<button class="btn btn-default block"><i class="glyphicon glyphicon-thumbs-down"></i>&nbsp;</button>';
	if (page_type === 'links_all' && row.status === '2')
		div += '<button class="btn btn-default unblock"><i class="glyphicon glyphicon-thumbs-up"></i>&nbsp;</button>';
	div += '</div>';
	return div;
}

window.linkOperateEvents = {
	'click .stats': function (e, value, row, index) {
		$("#modal-msg").modal('show');
		$("#modal-msg-content").html('Fetching data, please wait...');
		var interval = 1;
		var ajax = $.ajax({
			url: "/service?action=analyze",
			type: 'GET',
			data: {
				token: row.token,
				interval: interval
			}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				$("#modal-msg").modal('hide');
				var rs = res['hist'];
				show_analytics(row.token, rs, interval);
			} else {
				$("#modal-msg-content").html(res["msg"]);
			}
		});
		ajax.fail(function (jqXHR, textStatus) {
			$("#modal-msg-content").html("Request failed :" + textStatus);
		});
	},
	'click .edit': function (e, value, row, index) {
		show_link_modal(row);
	},
	'click .block': function (e, value, row, index) {
		if (!confirm('确认禁止' + row.remark + '吗？')) {
			return;
		}
		var ajax = $.ajax({
			url: "/service?action=block",
			type: 'POST',
			data: {token: row.token}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				$('#table-link').bootstrapTable("refresh");
			} else {
				$("#modal-msg-content").html(res["msg"]);
				$("#modal-msg").modal('show');
			}
		});
	},
	'click .unblock': function (e, value, row, index) {
		if (!confirm('确认解禁' + row.remark + '吗？')) {
			return;
		}
		var ajax = $.ajax({
			url: "/service?action=unblock",
			type: 'POST',
			data: {token: row.token}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				$('#table-link').bootstrapTable("refresh");
			} else {
				$("#modal-msg-content").html(res["msg"]);
				$("#modal-msg").modal('show');
			}
		});
	},
	'click .pause': function (e, value, row, index) {
		if (!confirm('确认暂停' + row.remark + '吗？')) {
			return;
		}
		var ajax = $.ajax({
			url: "/service?action=pause",
			type: 'POST',
			data: {token: row.token}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				$('#table-link').bootstrapTable("refresh");
			} else {
				$("#modal-msg-content").html(res["msg"]);
				$("#modal-msg").modal('show');
			}
		});
	},
	'click .resume': function (e, value, row, index) {
		if (!confirm('确认恢复' + row.remark + '吗？')) {
			return;
		}
		var ajax = $.ajax({
			url: "/service?action=resume",
			type: 'POST',
			data: {token: row.token}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				$('#table-link').bootstrapTable("refresh");
			} else {
				$("#modal-msg-content").html(res["msg"]);
				$("#modal-msg").modal('show');
			}
		});
	}
};

function show_link_modal(link) {
	$('#modal-link-title').html('编辑短链接');
	$('#form-link-url').val(link.url);
	$('#form-link-token').val(link.token);
	$("#form-link-token").attr("disabled", "disabled");
	$('#form-link-remark').val(link.remark);
	$('#form-link-valid-from').val(link.valid_from);
	$('#form-link-valid-to').val(link.valid_to);
	$('#form-link-submit-type').val('update');
	$('#form-link-remove').removeClass('hidden');
	$("#form-link-msg").html('');
	$('#modal-link').modal('show');
}

function show_analytics(token, rs, interval) {
	$('#modal-analytics-title').html('访问量统计-' + token);
	$("#modal-analytics-interval").attr('data-token', token);
	var labels = [];
	var data = [];
	rs.forEach(function (entry) {
		labels.push(timeFormatter(entry['time']));
		data.push(entry['cnt']);
	});
	var options = {
		legend: {
			display: false
		},
		scales: {
			yAxes: [{
				ticks: {
					beginAtZero: true
				}
			}]
		}
	};
	/* recreate canvas to make a new graph */
	var parent = $('#modal-analytics-chart').parent();
	$('#modal-analytics-chart').remove();
	parent.append('<canvas id="modal-analytics-chart"></canvas>');

	var ctx = $('#modal-analytics-chart');
	new Chart(ctx, {
		type: 'line',
		data: {
			labels: labels,
			datasets: [
				{
					data: data, "fill": false, "borderColor": "rgb(75, 192, 192)"
				}
			]
		},
		options: options
	});

	var intervals = {'1 Hr': 1, '24 Hrs': 30, '7 Days': 210, '1 Mon': 900, '1 Yr': 43200};
	var div = $('#modal-analytics-interval');
	div.empty();
	$.each(intervals, function (key, value) {
		var li = '<li role="presentation" class="' + (interval === value ? 'disabled' : '') + '">'
			+ '<a href="javascript:void(0)" data-interval="' + value + '">' + key + '</a>'
			+ '</li>';
		div.append(li);
	});
	$('#modal-analytics').modal('show');
}