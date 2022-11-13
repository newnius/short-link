$(function () {
	switch (page_type) {
		case "logs":
			load_logs('self');
			break;
		case "logs_all":
			load_logs('all');
			break;
		case "links":
			register_events_link();
			load_links('self');
			break;
		case "links_all":
			register_events_link();
			load_links('all');
			break;
		default:
			break;
	}
});

function load_logs(scope) {
	$("#table-log").bootstrapTable({
		url: window.config.BASE_URL + '/service?action=log_gets&who=' + scope,
		responseHandler: logResponseHandler,
		sidePagination: 'server',
		cache: true,
		striped: true,
		pagination: true,
		pageSize: 10,
		pageList: [10, 25, 50, 100, 200],
		search: false,
		showColumns: false,
		showRefresh: false,
		showToggle: false,
		showPaginationSwitch: false,
		minimumCountColumns: 2,
		clickToSelect: false,
		sortName: 'default',
		sortOrder: 'desc',
		smartDisplay: true,
		mobileResponsive: true,
		showExport: false,
		columns: [{
			field: 'scope',
			title: 'UID',
			align: 'center',
			valign: 'middle',
			sortable: false,
			visible: scope === 'all'
		}, {
			field: 'tag',
			title: 'Tag',
			align: 'center',
			valign: 'middle',
			sortable: false,
			visible: scope === 'all'
		}, {
			field: 'time',
			title: 'Time',
			align: 'center',
			valign: 'middle',
			sortable: false,
			formatter: timeFormatter
		}, {
			field: 'ip',
			title: 'IP',
			align: 'center',
			valign: 'middle',
			sortable: false,
			formatter: long2ip
		}, {
			field: 'content',
			title: 'Result',
			align: 'center',
			valign: 'middle',
			sortable: false,
			formatter: resultFormatter
		}, {
			field: 'content',
			title: 'Content',
			align: 'center',
			valign: 'middle',
			sortable: false,
			visible: scope === 'all',
			escape: true
		}]
	});
}

var logResponseHandler = function (res) {
	if (res['errno'] === 0) {
		var tmp = {};
		tmp["total"] = res["count"];
		tmp["rows"] = res["logs"];
		return tmp;
	}
	$("#modal-msg-content").html(res["msg"]);
	$("#modal-msg").modal('show');
	return [];
};

var resultFormatter = function (json) {
	var res = JSON.parse(json);
	if (res['response'] === 0) {
		return '<span class="text-success">成功</span>';
	}
	return '<span class="text-dander">失败</span>';
};