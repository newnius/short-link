<?php

require_once('util4p/util.php');
require_once('util4p/CRObject.class.php');

require_once('Code.class.php');
require_once('Counter.class.php');
require_once('link.logic.php');
require_once('config.inc.php');
require_once('init.inc.php');

$link = new CRObject();
$link->set('token', cr_get_GET('token'));
$res = link_get($link);

if (ENABLE_LOG_QUERY) {
	$log = new CRObject();
	$log->set('token', cr_get_GET('token', ''));
	$log->set('referer', $_SERVER['HTTP_REFERER']);
	$log->set('ua', $_SERVER['HTTP_USER_AGENT']);
	$log->set('lang', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
	$log->set('ip', cr_get_client_ip());
	$log->set('time', time());
	Counter::log($log);
}

if ($res['errno'] === Code::SUCCESS) {
	header('HTTP/1.1 307 Temporary Redirect');
	header('Location: ' . $link['url']);
} else {
	$code = $res['errno'];
	require_once('404.php');
}