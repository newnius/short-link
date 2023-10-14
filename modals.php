<!-- result modal -->
<div class="modal fade" id="modal-result" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content panel-success">
			<div class="modal-header panel-heading">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-result" id="modal-result-title">短网址生成成功</h4>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							原网址：<h4 id="modal-result-url"></h4>
							短网址：<h4 id="modal-result-token"></h4>
							<p>你知道吗？登录后可以查看链接访问统计、修改、删除链接</p>
						</div>
						<div class="col-md-6 col-sm-6 hidden-xs">
							<img id="modal-result-qrcode" src="" />
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
			</div>
		</div>
	</div>
</div>

<!-- msg modal -->
<div class="modal fade" id="modal-msg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content panel-warning">
			<div class="modal-header panel-heading">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
				</button>
				<h4 id="modal-msg-title" class="modal-title">通知</h4>
			</div>
			<div class="modal-body">
				<h4 id="modal-msg-content" class="text-msg text-center">Something is wrong!</h4>
			</div>
		</div>
	</div>
</div>

<!-- link modal -->
<div class="modal fade" id="modal-link" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content panel-info">
			<div class="modal-header panel-heading">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
				</button>
				<h4 id="modal-link-title" class="modal-title">添加短链接</h4>
			</div>
			<div class="modal-body">
				<form class="form" action="javascript:void(0)">
					<label>原始网址</label>
					<div class="form-group form-group-lg">
						<label for="form-link-url" class="sr-only">Shorten</label>
						<input type="text" id="form-link-url" class="form-control" maxlength="<?= URL_MAX_LENGTH ?>" placeholder="在此输入想要缩短的网址" required autofocus autocomplete="off" />
					</div>
					<label>自定义短链（可选）</label>
					<div class="form-group input-group input-group-lg">
						<div class="input-group-addon">
							<span><?= BASE_URL ?>/</span>
						</div>
						<label for="form-link-token" class="sr-only">Custom Token</label>
						<input type="text" id="form-link-token" class="form-control" minlength="<?= TOKEN_MIN_LENGTH ?>" maxlength="<?= TOKEN_MAX_LENGTH ?>" placeholder="字母、数字，<?= TOKEN_MIN_LENGTH ?>-<?= TOKEN_MAX_LENGTH ?>位" autocomplete="off" />
					</div>
					<label>备注（可选）</label>
					<div class="form-group form-group-lg">
						<label for="form-link-remark" class="sr-only">Custom Token</label>
						<input type="text" id="form-link-remark" class="form-control" placeholder="短链接备注" autocomplete="off" />
					</div>
					<label>有效期自（可选）</label>
					<div class="form-group form-group-lg">
						<div class='input-group date date-picker'>
							<label for="form-link-valid-from" class="sr-only">Valid From</label>
							<input type='text' class="form-control" placeholder="留空表示不限制" id="form-link-valid-from" autocomplete="off" />
							<div class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</div>
						</div>
					</div>
					<label>有效期至（可选）</label>
					<div class="form-group form-group-lg">
						<div class='input-group date date-picker'>
							<label for="form-link-valid-to" class="sr-only">Valid To</label>
							<input type='text' class="form-control" placeholder="留空表示不限制" id="form-link-valid-to" autocomplete="off" />
							<div class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</div>
						</div>
					</div>
					<div>
						<input type='hidden' class="form-control" id="form-link-submit-type" />
						<button id="form-link-submit" type="submit" class="btn btn-primary btn-lg">保&nbsp;存</button>
						&nbsp;
						<button id="form-link-remove" type="submit" class="btn btn-default btn-lg">删&nbsp;除</button>
						&nbsp;
						<span id="form-link-msg" class="text-danger"></span>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- analytics modal -->
<div class="modal fade" id="modal-analytics" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content panel-default">
			<div class="modal-header panel-heading">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
				</button>
				<h4 id="modal-analytics-title" class="modal-title">访问统计</h4>
			</div>
			<div class="modal-body">
				<div class="pull-right">
					<ul id="modal-analytics-interval" data-token="" class="nav nav-pills nav panel-body">
						<li role="presentation" class="disabled">
							<a href="javascript:void(0)" data-interval="1">1 Hr</a>
						</li>
						<li role="presentation">
							<a href="javascript:void(0)" data-interval="30">24 Hrs</a>
						</li>
						<li role="presentation">
							<a href="javascript:void(0)" data-interval="210">7 Days</a>
						</li>
						<li role="presentation">
							<a href="javascript:void(0)" data-interval="900">1 Mon</a>
						</li>
						<li role="presentation">
							<a href="javascript:void(0)" data-interval="43200">1 Yr</a>
						</li>
					</ul>
				</div>
				<canvas id="modal-analytics-chart"></canvas>
			</div>
		</div>
	</div>
</div>