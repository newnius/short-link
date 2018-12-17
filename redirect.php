<?php

require_once('util4p/util.php');
require_once('util4p/CRObject.class.php');

require_once('Code.class.php');
require_once('Counter.class.php');
require_once('link.logic.php');
require_once('config.inc.php');
require_once('init.inc.php');

if (time() - (int)$_COOKIE['last_visit_time'] < 2) { // too fast, seems like an endless loop
	$code = Code::TOO_FAST;
	require_once('404.php');
	exit;
}
setcookie('last_visit_time', time());

$link = new CRObject();
$link->set('token', cr_get_GET('token'));
$res = link_get($link);

if (ENABLE_LOG_QUERY) {
	$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
	$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
	$lang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : null;

	$log = new CRObject();
	$log->set('token', cr_get_GET('token', ''));
	$log->set('referer', $referer);
	$log->set('ua', $ua);
	$log->set('lang', $lang);
	$log->set('ip', cr_get_client_ip(false));
	$log->set('time', time());
	Counter::log($log);
}

if ($res['errno'] === Code::SUCCESS) {
	header('HTTP/1.1 307 Temporary Redirect');
	$url = $res['url'];
	if (strpos($url, '//') === false) {
		$url = 'http://' . $url;
	}
	header('Location: ' . $url);
} else {
	$code = $res['errno'];
	require_once('404.php');
}