var newPhoneDiv = '<div class="input-group form-group">'
	+ '<label for="telephone" class="sr-only">电话号码</label>'
	+ '<input type="tel" class="form-group form-control" placeholder="电话号码">'
	+ '<a class="input-group-addon telephone-input-del" href="javascript:void(0)">'
	+ '<span class="glyphicon glyphicon-trash"></span></a>'
	+ '</div>';

function register_events_contact() {
	$('#btn-contact-add').click(function (e) {
		$('#modal-contact-title').html('添加联系人');
		$('#form-contact-name').val('');
		$('#form-contact-remark').val('');
		$('#form-contact-group').val(0);
		$('#form-contact-submit-type').val('add');

		var addPhone = '<a class="telephone-add input-group form-group" href="javascript:void(0)">' +
			'<span class="input-group-addon glyphicon glyphicon-plus"></span></a>';
		$('#form-contact-telephones').empty().append(newPhoneDiv).append(addPhone);

		show_group_options(0);
		$('#modal-contact').modal('show');
	});

	$('#form-contact-telephones').on('click', '.telephone-add', function (e) {
		$(this).before(newPhoneDiv);
	});

	$('#form-contact-telephones').on('click', '.telephone-input-del', function (e) {
		$(this).parent().remove();
	});

	$("#form-contact-submit").click(function (e) {
		var id = $("#form-contact-id").val();
		var name = $("#form-contact-name").val();
		if (name.length === 0) {
			return true;
		}
		$("#form-contact-submit").attr("disabled", "disabled");
		var array = [];
		$('#form-contact-telephones').find('input').each(function () {
			if ($(this).val().trim() !== '')
				array.push($(this).val());
		});
		var telephones = array.join(';');
		var remark = $("#form-contact-remark").val();
		var group_id = $("#form-contact-group").val();
		var type = $("#form-contact-submit-type").val();
		var action = "contact_add";
		if (type === "update")
			action = "contact_update";
		var ajax = $.ajax({
			url: "/service?action=" + action,
			type: 'POST',
			data: {
				id: id,
				name: name,
				telephones: telephones,
				remark: remark,
				group_id: group_id
			}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				$('#modal-contact').modal('hide');
				$('#table-contact').bootstrapTable("refresh");
			} else {
				$("#form-contact-msg").html(res["msg"]);
			}
			$("#form-contact-submit").removeAttr("disabled");
		});
		ajax.fail(function (jqXHR, textStatus) {
			$("#form-contact-msg").html("Request failed :" + textStatus);
			$("#form-contact-submit").removeAttr("disabled");
		});
	});

	$("#form-contact-remove").click(function (e) {
		$("#form-contact-submit").attr("disabled", "disabled");
		var id = $("#form-contact-id").val();
		var ajax = $.ajax({
			url: "/service?action=contact_remove",
			type: 'POST',
			data: {id: id}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				$('#modal-contact').modal('hide');
				$('#table-contact').bootstrapTable("refresh");
			} else {
				$("#form-contact-msg").html(res["msg"]);
				$("#modal-contact").effect("shake");
			}
			$("#form-contact-submit").removeAttr("disabled");
		});
		ajax.fail(function (jqXHR, textStatus) {
			$("#form-contact-msg").html("Request failed :" + textStatus);
			$("#form-contact-submit").removeAttr("disabled");
		});
	});
}

function load_contacts(scope) {
	var $table = $("#table-contact");
	$table.bootstrapTable({
		url: '/service?action=contact_gets&who=' + scope,
		responseHandler: contactResponseHandler,
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
			field: 'name',
			title: '姓名',
			align: 'center',
			valign: 'middle',
			sortable: true
		}, {
			field: 'telephones',
			title: '号码',
			align: 'center',
			valign: 'middle',
			sortable: false,
			formatter: telFormatter
		}, {
			field: 'remark',
			title: '备注',
			align: 'center',
			valign: 'middle',
			sortable: false
		}, {
			field: 'group_id',
			title: '分组',
			align: 'center',
			valign: 'middle',
			sortable: true,
			visible: false,
			formatter: get_group_name_by_id
		}, {
			field: 'operate',
			title: 'Operate',
			align: 'center',
			events: contactOperateEvents,
			formatter: contactOperateFormatter
		}]
	});
}

var telFormatter = function (telephones) {
	return telephones.split(';').join('<br/>');
};

function contactResponseHandler(res) {
	if (res['errno'] === 0) {
		return res['contacts'];
	}
	$("#modal-msg-content").html(res["msg"]);
	$("#modal-msg").modal('show');
	return [];
}

function contactOperateFormatter(value, row, index) {
	return [
		'<div class="btn-group" role="group" aria-label="...">',
		'<button class="btn btn-default edit">',
		'<i class="glyphicon glyphicon-edit"></i>&nbsp;',
		'</button>',
		'<button class="btn btn-default remove">',
		'<i class="glyphicon glyphicon-remove"></i>&nbsp;',
		'</button>&nbsp;',
		'</div>'
	].join('');
}

window.contactOperateEvents = {
	'click .edit': function (e, value, row, index) {
		show_contact_modal(row);
	},
	'click .remove': function (e, value, row, index) {
		if (!confirm('确认删除' + row.name + '吗（操作不可逆）')) {
			return;
		}
		var ajax = $.ajax({
			url: "/service?action=contact_remove",
			type: 'POST',
			data: {id: row.id}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				$('#table-contact').bootstrapTable("refresh");
			} else {
				$("#modal-msg-content").html(res["msg"]);
				$("#modal-msg").modal('show');
			}
		});
	}
};

function show_contact_modal(contact) {
	$('#modal-contact-title').html('编辑联系人');
	$('#form-contact-name').val(contact.name);
	$('#form-contact-remark').val(contact.remark);
	$('#form-contact-submit-type').val('update');
	$('#form-contact-id').val(contact.id);

	$('#form-contact-telephones').children().remove();
	var telephones = contact.telephones.split(";");
	for (var i = 0; i < telephones.length; i++) {
		var newPhone = '<div class="input-group form-group">'
			+ '<label for="telephone" class="sr-only">电话号码</label>'
			+ '<input type="telephone" class="form-group form-control" placeholder="电话号码" value="'
			+ telephones[i]
			+ '">'
			+ '<a class="input-group-addon telephone-input-del" href="javascript:void(0)">'
			+ '<span class="glyphicon glyphicon-trash"></span></a>'
			+ '</div>';
		$('#form-contact-telephones').append(newPhone);
	}
	var addPhone = '<a class="telephone-add input-group form-group" href="javascript:void(0)">' +
		'<span class="input-group-addon glyphicon glyphicon-plus"></span></a>';
	$('#form-contact-telephones').append(addPhone);

	$('#modal-contact').modal('show');
	show_group_options(contact.group_id);
}

var show_group_options = function (selected) {
	$('#form-contact-group').children().remove();
	$.each(window.groups, function (id, group) {
		var newGroupOption = '<option value="' + id + '"'
			+ (id === selected ? ' selected="selected"' : ' ')
			+ '>'
			+ get_group_name_by_id(id)
			+ '</option>';
		$('#form-contact-group').append(newGroupOption);
	});
};