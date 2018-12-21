<?php

require_once('util4p/util.php');
require_once('util4p/CRObject.class.php');

require_once('Code.class.php');
require_once('Securer.class.php');

require_once('user.logic.php');
require_once('link.logic.php');

require_once('config.inc.php');
require_once('init.inc.php');


function csrf_check($action)
{
	/* check referer, just in case I forget to add the method to $post_methods */
	$referer = cr_get_SERVER('HTTP_REFERER', '');
	$url = parse_url($referer);
	$host = isset($url['host']) ? $url['host'] : '';
	$host .= isset($url['port']) && $url['port'] !== 80 ? ':' . $url['port'] : '';
	if ($host !== cr_get_SERVER('HTTP_HOST')) {
		return false;
	}
	$post_methods = array(
		'set',
		'claim',
		'update',
		'remove',
		'pause',
		'resume',
		'block',
		'unblock',
		'report',
		'signout',
		'oauth_get_url'
	);
	if (in_array($action, $post_methods)) {
		return Securer::validate_csrf_token();
	}
	return true;
}

function print_response($res)
{
	if (!isset($res['msg']))
		$res['msg'] = Code::getErrorMsg($res['errno']);
	$json = json_encode($res);
	header('Content-type: application/json');
	echo $json;
}


$res = array('errno' => Code::UNKNOWN_REQUEST);

$action = cr_get_GET('action');

if (!csrf_check($action)) {
	$res['errno'] = 99;
	$res['msg'] = 'invalid csrf_token';
	print_response($res);
	exit(0);
}

switch ($action) {
	case 'set':
		$link = new CRObject();
		$link->set('token', cr_get_POST('token'));
		$link->set('url', cr_get_POST('url'));
		$link->set('remark', cr_get_POST('remark'));
		$link->set('valid_from', cr_get_POST('valid_from'));
		$link->set('valid_to', cr_get_POST('valid_to'));
		$res = link_add($link);
		break;

	case 'get':
		$link = new CRObject();
		$link->set('token', cr_get_GET('token'));
		$res = link_get($link);
		break;

	case 'update':
		$link = new CRObject();
		$link->set('token', cr_get_POST('token'));
		$link->set('url', cr_get_POST('url'));
		$link->set('remark', cr_get_POST('remark'));
		$link->set('valid_from', cr_get_POST('valid_from'));
		$link->set('valid_to', cr_get_POST('valid_to'));
		$res = link_update($link);
		break;

	case 'remove':
		$link = new CRObject();
		$link->set('token', cr_get_POST('token'));
		$res = link_remove($link);
		break;

	case 'block':
		$link = new CRObject();
		$link->set('token', cr_get_POST('token'));
		$res = link_block($link);
		break;

	case 'unblock':
		$link = new CRObject();
		$link->set('token', cr_get_POST('token'));
		$res = link_unblock($link);
		break;

	case 'pause':
		$link = new CRObject();
		$link->set('token', cr_get_POST('token'));
		$res = link_pause($link);
		break;

	case 'resume':
		$link = new CRObject();
		$link->set('token', cr_get_POST('token'));
		$res = link_resume($link);
		break;

	case 'list':
		$rule = new CRObject();
		$rule->set('who', cr_get_GET('who', 'self'));
		$rule->set('offset', cr_get_GET('offset'));
		$rule->set('limit', cr_get_GET('limit'));
		$rule->set('order', 'latest');
		$res = link_gets($rule);
		break;

	case 'analyze':
		$link = new CRObject();
		$link->set('token', cr_get_GET('token'));
		$link->set('interval', cr_get_GET('interval'));
		$res = link_analyze($link);
		break;

	case 'user_signout':
		$res = user_signout();
		break;

	case 'log_gets':
		$rule = new CRObject();
		$rule->set('who', cr_get_GET('who', 'self'));
		$rule->set('offset', cr_get_GET('offset'));
		$rule->set('limit', cr_get_GET('limit'));
		$rule->set('order', 'latest');
		$res = log_gets($rule);
		break;

	case 'oauth_get_url':
		$res = oauth_get_url();
		break;

	default:
		break;
}

print_response($res);
