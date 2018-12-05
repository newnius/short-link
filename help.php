<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<?php require_once('head.php'); ?>
	<title>帮助中心 | 短链接生成器</title>
</head>
<body>
<div class="wrapper">
	<?php require_once('header.php'); ?>
	<div class="container">
		<div class="row">
			<div class="col-sm-4 col-md-3 hidden-xs">
				<div id="help-nav" class="panel panel-default">
					<div class="panel-heading">列表</div>
					<ul class="nav nav-pills nav-stacked panel-body">
						<li role="presentation">
							<a href="#introduction">介绍</a>
						</li>
						<li role="presentation">
							<a href="#about">关于</a>
						</li>
						<li role="TOS">
							<a href="#about">TOS</a>
						</li>
						<li role="presentation">
							<a href="#privacy">隐私</a>
						</li>
						<li role="presentation">
							<a href="#feedback">反馈</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-xs-12 col-sm-8 col-md-8 col-md-offset-1 ">
				<div id="introduction" class="panel panel-default">
					<div class="panel-heading">短链接生成器</div>
					<div class="panel-body">
						<p>云通讯录是基于web的在线联系人管理中心。只需一次导入或添加，即可随时随地，在任意设备上使用，避免了更换设备所带来的联系人信息丢失或转移通讯录的麻烦。并且，这一切都是免费的。</p>
					</div>
				</div>
				<div id="about" class="panel panel-default">
					<div class="panel-heading">关于</div>
					<div class="panel-body">
						<p>云通讯录,电话本,联系人,同步,备份,网络备份,联系人去重,号码归属地查询,骚扰电话查询</p>
					</div>
				</div>
				<div id="privacy" class="panel panel-default">
					<div class="panel-heading">TOS</div>
					<div class="panel-body">
						<p>用户使用限制</p>
					</div>
				</div>
				<div id="privacy" class="panel panel-default">
					<div class="panel-heading">隐私保护</div>
					<div class="panel-body">
						<p>隐私保护</p>
					</div>
				</div>
				<div id="feedback" class="panel panel-default">
					<div class="panel-heading">反馈</div>
					<div class="panel-body">
						<p>感谢使用短链接生成器，如果您在使用过程中遇到任何问题，请通过邮箱
							<a href="mailto:support@newnius.com?subject=From LS">support@newnius.com</a>
							联系。
						</p>
					</div>
				</div>

			</div>
		</div>
	</div> <!-- /container -->
	<!--This div exists to avoid footer from covering main body-->
	<div class="push"></div>
</div>
<?php require_once('footer.php'); ?>
</body>
</html>
