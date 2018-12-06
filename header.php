<?php
require_once('predis/autoload.php');
require_once('util4p/ReSession.class.php');
require_once('util4p/Random.class.php');
require_once('config.inc.php');
require_once('init.inc.php');

$state = Random::randomString(16);
Session::put('oauth_state', $state);

$oauth_login_url = OAUTH_SITE . '/login?response_type=code&client_id=' . OAUTH_CLIENT_ID . '&redirect_uri=' . BASE_URL . '/auth&state=' . $state . '&scope=role';
$oauth_register_url = OAUTH_SITE . '/register?response_type=code&client_id=' . OAUTH_CLIENT_ID . '&redirect_uri=' . BASE_URL . '/auth&state=' . $state . '&scope=role';
?>
<header id="header" class="navbar navbar-default">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
			        data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?= BASE_URL ?>">短网址生成器</a>
		</div>
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav navbar-right">
				<?php if (!Session::get('uid')) { ?>
					<li><a href="<?= $oauth_login_url ?>">登陆</a>
					</li>
					<li><a href="<?= $oauth_register_url ?>">注册</a></li>
				<?php } else { ?>
					<li><a href="<?= BASE_URL ?>/ucenter"><?= htmlspecialchars(Session::get('nickname')) ?></a></li>
				<?php } ?>
				<li class="dropdown">
					<a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button"
					   aria-haspopup="true" aria-expanded="false">更多<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="/help">帮助</a></li>
						<li role="separator" class="divider"></li>
						<?php if (Session::get('uid')) { ?>
							<li><a href="javascript:void(0)" id="btn-signout-header">退出</a></li>
						<?php } ?>
					</ul>
				</li>
			</ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container -->
</header>