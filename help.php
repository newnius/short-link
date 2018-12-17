<?php
require_once('config.inc.php');
require_once('secure.inc.php');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<?php require_once('head.php'); ?>
	<title>帮助中心 | 短网址生成器</title>
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
					<div class="panel-heading">短网址生成器</div>
					<div class="panel-body">
						<p>短网址生成器</p>
					</div>
				</div>
				<div id="about" class="panel panel-default">
					<div class="panel-heading">关于</div>
					<div class="panel-body">
						<ul>
							<li>支持自定义短网址</li>
							<li>同时生成对应的二维码</li>
							<li>支持短网址有效期设置</li>
							<li>支持对短网址的二次编辑</li>
							<li>支持暂时停止短网址的跳转</li>
							<li>支持形式丰富的访问统计分析</li>
							<li>支持添加短网址备注，便于查看</li>
							<li>支持导出短网址列表</li>
							<li>去除了容易混淆的字符</li>
							<li>采用 307 状态码，保留原始请求方法</li>
							<li>启用 HTTPS 加密通信，降低个人隐私泄漏的风险</li>
							<li>没有恶心人的域名白名单限制</li>
							<li>完全免费，且无数量限制</li>
						</ul>
					</div>
				</div>
				<div id="privacy" class="panel panel-default">
					<div class="panel-heading">TOS</div>
					<div class="panel-body">
						<p>本在线短网址生成器免费对外开放。在使用本服务时，请知晓以下几点：</p>
						<p>1. 网站有权停止或删除违反国际通行法律的短网址，如儿童色情等</p>
						<p>2. 因不可抗力等因素导致的数据丢失等情况，网站不对此负责</p>
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
						<p>感谢使用在线短网址生成器，如果您在使用过程中遇到任何问题，请通过邮箱
							<a href="mailto:<?= FEEDBACK_EMAIL ?>?subject=From LS"><?= FEEDBACK_EMAIL ?></a>
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
