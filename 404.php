<?php
require_once('Code.class.php');
require_once('secure.inc.php');

$error = '404 Not Found';
if (isset($code)) {
	switch ($code) {
		case Code::RECORD_NOT_EXIST:
			header('HTTP/1.1 404 Not Found');
			break;
		case Code::RECORD_DISABLED:
			header('HTTP/1.1 403 Forbidden');
			break;
		case Code::RECORD_REMOVED:
			header('HTTP/1.1 404 Not Found');
			break;
		case Code::RECORD_NOT_IN_VALID_TIME:
			header('HTTP/1.1 403 Forbidden');
			break;
		case Code::RECORD_PAUSED:
			header('HTTP/1.1 403 Forbidden');
			break;
		case Code::TOO_FAST:
			header('HTTP/1.1 429 Too Many Requests');
			break;
		default:
			header('HTTP/1.1 520 Unknown Error');
			break;
	}
	$error = Code::getErrorMsg($code);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
	<?php require_once('head.php'); ?>
	<title>错误页 | 短网址生成器</title>
</head>

<body>
	<div class="wrapper">
		<?php require_once('header.php'); ?>
		<div class="container">
			<div class="container">
				<h2 style="text-align: center"><?= $error ?></h2>
			</div>
		</div> <!-- /container -->
		<!--This div exists to avoid footer from covering main body-->
		<div class="push"></div>
	</div>
	<?php require_once('footer.php'); ?>
</body>

</html>