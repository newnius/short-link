<?php

require_once('predis/autoload.php');

require_once('util4p/util.php');
require_once('util4p/CRObject.class.php');
require_once('util4p/ReSession.class.php');
require_once('util4p/CRLogger.class.php');

require_once('Code.class.php');
require_once('Spider.class.php');

require_once('user.logic.php');

require_once('config.inc.php');
require_once('init.inc.php');


//check state
$state = cr_get_GET('state');
if ($state === null || $state !== Session::get('oauth:state')) {
	echo 'Auth failed, state check failed!';
	exit;
}

$client_id = OAUTH_CLIENT_ID;
$client_secret = OAUTH_CLIENT_SECRET;

$url = OAUTH_SITE . '/api?action=get_token';
$fields = array(
	'grant_type' => 'authorization_code',
	'client_id' => $client_id,
	'client_secret' => $client_secret,
	'code' => $_GET['code'],
	'redirect_uri' => BASE_URL . '/auth',
);

$spider = new Spider();
$spider->doPost($url, $fields);
$result = json_decode($spider->getBody(), true);
$token = $result['token'];

$url = OAUTH_SITE . '/api?action=get_info';
$fields = array(
	'api_name' => 'basic',
	'client_id' => $client_id,
	'client_secret' => $client_secret,
	'token' => $token
);
$spider = new Spider();
$spider->doPost($url, $fields);
$response = json_decode($spider->getBody(), true);


if ($response['errno'] === 0) {
	$info = $response['info'];
	$open_id = ($info !== null && isset($info['open_id'])) ? $info['open_id'] : null;
	$email = ($info !== null && isset($info['email'])) ? $info['email'] : null;
	$role = ($info !== null && isset($info['role'])) ? $info['role'] : 'normal';
	$nickname = ($info !== null && isset($info['nickname'])) ? $info['nickname'] : 'u2913';

	$user = new CRObject();
	$user->set('open_id', $open_id);
	$user->set('email', $email);
	$user->set('role', $role);
	$res = user_get($user);

	if ($res['errno'] === 0) {
		$user = $res['user'];
		Session::put('uid', $user['uid']);
		Session::put('role', $user['role']);
		Session::put('nickname', $nickname);

		$log = new CRObject();
		$log->set('scope', $user['uid']);
		$log->set('tag', 'user.login');
		$content = array('uid' => $user['uid'], 'response' => $res['errno']);
		$log->set('content', json_encode($content));
		CRLogger::log($log);

		header('location:' . BASE_URL . '/ucenter');
		exit;
	} else {
		echo Code::getErrorMsg($res['errno']);
		exit;
	}
}

echo $response['msg'];
exit;
