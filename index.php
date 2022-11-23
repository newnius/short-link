<?php
require_once('config.inc.php');
require_once('secure.inc.php');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<?php require('head.php'); ?>
	<title>短网址生成器 | LinkShortener</title>
</head>

<body>
<div class="wrapper">
	<?php require('header.php'); ?>
	<?php require('modals.php'); ?>
	<div class="container">

		<div id="main" class="form ui-widget load-overlay container">
			<div id="main-tabs">
				<ul class="nav nav-tabs">
					<li role="presentation" class="active"><a role="tab" href="#main-link-set">缩短</a></li>
					<li role="presentation"><a role="tab" href="#main-link-get">还原</a></li>
					<li role="presentation"><a role="tab" href="#main-link-multiset">批量</a></li>
				</ul>
				<p>&nbsp;</p>
				<div id="main-tab-content" class="tab-content">
					<div id="main-link-set" class="tab-pane fade active in">
						<form class="form" action="javascript:void(0)">
							<label>原始网址</label>
							<div class="form-group input-group input-group-lg">
								<label for="form-set-url" class="sr-only">Shorten</label>
								<input type="text" id="form-set-url" class="form-control"
								       maxlength="500" placeholder="在此输入想要缩短的网址"
								       required autofocus autocomplete="off"/>
								<span class="input-group-btn">
                                    <button id="form-set-submit" type="submit" class="btn btn-default">缩短</button>
                                </span>
							</div>
							<label>自定义短链（可选）</label>
							<div class="form-group input-group input-group-lg">
								<div class="input-group-addon">
									<span><?= BASE_URL ?>/</span>
								</div>
								<label for="form-set-token" class="sr-only">Custom Token</label>
								<input type="text" id="form-set-token" class="form-control"
								       minlength="5" maxlength="15"
								       placeholder="字母、数字，5-15位" autocomplete="off"/>
							</div>
							<label>备注（可选）</label>
							<div class="form-group form-group-lg">
								<label for="form-set-remark" class="sr-only">Custom Token</label>
								<input type="text" id="form-set-remark" class="form-control"
								       placeholder="短链接备注" autocomplete="off"/>
							</div>
							<label>有效期自（可选）</label>
							<div class="form-group form-group-lg">
								<div class='input-group date date-picker'>
									<label for="form-set-valid-from" class="sr-only">Valid From</label>
									<input type='text' class="form-control" placeholder="留空表示不限制"
									       id="form-set-valid-from" autocomplete="off"/>
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</div>
								</div>
							</div>
							<label>有效期至（可选）</label>
							<div class="form-group form-group-lg">
								<div class='input-group date date-picker'>
									<label for="form-set-valid-to" class="sr-only">Valid To</label>
									<input type='text' class="form-control" placeholder="留空表示不限制"
									       id="form-set-valid-to" autocomplete="off"/>
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div id="main-link-get" class="tab-pane fade">
						<form class="form" action="javascript:void(0)">
							<div class="input-group input-group-lg">
								<div class="input-group-addon">
									<span><?= BASE_URL ?>/</span>
								</div>
								<label for="form-get-token" class="sr-only">Token</label>
								<input type="text" id="form-get-token" class="form-control" placeholder="补全短网址"
								       required/>
								<span class="input-group-btn">
                                    <button id="form-get-submit" type="submit" class="btn btn-default">还原</button>
                                </span>
							</div>
						</form>
					</div>
					<div id="main-link-multiset" class="tab-pane fade">
						<form class="form" action="javascript:void(0)">
							<div class="input-group-lg">
								<label for="form-multiset-text" class="sr-only">Token</label>
								<textarea type="text" id="form-multiset-text"
								class="form-control"
								rows="10" autocomplete="off"
								placeholder="一行一个链接，不超过50个" required></textarea>
							</div>
							<label>备注（可选）</label>
							<div class="form-group form-group-lg">
								<label for="form-multiset-remark" class="sr-only">Custom Token</label>
								<input type="text" id="form-multiset-remark" class="form-control"
								       placeholder="短链接备注" autocomplete="off"/>
							</div>
							<label>有效期自（可选）</label>
							<div class="form-group form-group-lg">
								<div class='input-group date date-picker'>
									<label for="form-multiset-valid-from" class="sr-only">Valid From</label>
									<input type='text' class="form-control" placeholder="留空表示不限制"
									       id="form-multiset-valid-from" autocomplete="off"/>
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</div>
								</div>
							</div>
							<label>有效期至（可选）</label>
							<div class="form-group form-group-lg">
								<div class='input-group date date-picker'>
									<label for="form-multiset-valid-to" class="sr-only">Valid To</label>
									<input type='text' class="form-control" placeholder="留空表示不限制"
									       id="form-multiset-valid-to" autocomplete="off"/>
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</div>
								</div>
							</div>
							<div><span class="text-infp">* 仅对部分用户开放</span></div>
							<button id="form-multiset-submit" type="submit" class="btn btn-lg btn-default">批量生成</button>
						</form>
					</div>
				</div>
			</div>

		</div>
	</div> <!-- /container -->
	<!--This div exists to avoid footer from covering main body-->
	<div class="push"></div>
</div>
<?php require('footer.php'); ?>
<script src="static/main.js"></script>
</body>
</html>