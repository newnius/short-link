<?php

require_once('util4p/util.php');
require_once('util4p/CRObject.class.php');
require_once('util4p/Random.class.php');
require_once('util4p/AccessController.class.php');
require_once('util4p/CRLogger.class.php');

require_once('Code.class.php');
require_once('LinkManager.class.php');
require_once('Spider.class.php');
require_once('Counter.class.php');
require_once('Cache.class.php');

require_once('config.inc.php');
require_once('init.inc.php');

function link_add(CRObject $link)
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'link.set')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	if ($link->get('token') === null) {
		while (true) {
			$token = Random::randomString(TOKEN_MIN_LENGTH);
			$link->set('token', $token);
			if (LinkManager::get($link) === null) {
				break;
			}
		}
	}
	if (strlen($link->get('token', '')) < TOKEN_MIN_LENGTH || strlen($link->get('token', '')) > TOKEN_MAX_LENGTH) {
		$res['errno'] = Code::TOKEN_LENGTH_INVALID;
	} else if (strlen($link->get('url', '')) < URL_MIN_LENGTH || strlen($link->get('url', '')) > URL_MAX_LENGTH) {
		$res['errno'] = Code::URL_LENGTH_INVALID;
	} else if (LinkManager::get($link) !== null) {
		$res['errno'] = Code::RECORD_ALREADY_EXIST;
	} else {
		$link->set('owner', Session::get('uid'));
		$res['token'] = $link->get('token');
		$res['errno'] = LinkManager::add($link) ? Code::SUCCESS : Code::UNKNOWN_ERROR;
	}
	$log = new CRObject();
	$log->set('scope', Session::get('uid'));
	$log->set('tag', 'link.add');
	$content = array('link' => $link, 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

function link_remove(CRObject $link)
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'link.remove')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$origin = LinkManager::get($link);
	if ($origin === null) {
		$res['errno'] = Code::RECORD_NOT_EXIST;
	} else if ($origin['owner'] !== Session::get('uid') && !AccessController::hasAccess(Session::get('role', 'visitor'), 'link.remove_others')) {
		$res['errno'] = Code::NO_PRIVILEGE;
	} else if ($origin['status'] === '3') {
		$res['errno'] = Code::RECORD_REMOVED;
	} else {
		$origin['status'] = 3;
		$res['errno'] = LinkManager::update(new CRObject($origin)) ? Code::SUCCESS : Code::UNKNOWN_ERROR;
		Cache::expire($link->get('token', '')); // expire cache
	}
	$log = new CRObject();
	$log->set('scope', Session::get('uid'));
	$log->set('tag', 'link.remove');
	$content = array('token' => $link->get('token'), 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

function link_block(CRObject $link)
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'link.block')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$origin = LinkManager::get($link);
	if ($origin === null) {
		$res['errno'] = Code::RECORD_NOT_EXIST;
	} else if ($origin['status'] === '3') {
		$res['errno'] = Code::RECORD_REMOVED;
	} else {
		$origin['status'] = 2;
		$res['errno'] = LinkManager::update(new CRObject($origin)) ? Code::SUCCESS : Code::UNKNOWN_ERROR;
		Cache::expire($link->get('token', '')); // expire cache
	}
	$log = new CRObject();
	$log->set('scope', Session::get('uid'));
	$log->set('tag', 'link.block');
	$content = array('token' => $link->get('token'), 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}


function link_unblock(CRObject $link)
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'link.unblock')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$origin = LinkManager::get($link);
	if ($origin === null) {
		$res['errno'] = Code::RECORD_NOT_EXIST;
	} else if ($origin['status'] === '3') {
		$res['errno'] = Code::RECORD_REMOVED;
	} else {
		$origin['status'] = 0;
		$res['errno'] = LinkManager::update(new CRObject($origin)) ? Code::SUCCESS : Code::UNKNOWN_ERROR;
		Cache::expire($link->get('token', '')); // expire cache
	}
	$log = new CRObject();
	$log->set('scope', Session::get('uid'));
	$log->set('tag', 'link.unblock');
	$content = array('token' => $link->get('token'), 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

function link_pause(CRObject $link)
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'link.update')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$origin = LinkManager::get($link);
	if ($origin === null) {
		$res['errno'] = Code::RECORD_NOT_EXIST;
	} else if ($origin['status'] === '3') {
		$res['errno'] = Code::RECORD_REMOVED;
	} else if ($origin['status'] === '2') {
		$res['errno'] = Code::RECORD_DISABLED;
	} else {
		$origin['status'] = 1;
		$res['errno'] = LinkManager::update(new CRObject($origin)) ? Code::SUCCESS : Code::UNKNOWN_ERROR;
		Cache::expire($link->get('token', '')); // expire cache
	}
	$log = new CRObject();
	$log->set('scope', Session::get('uid'));
	$log->set('tag', 'link.pause');
	$content = array('token' => $link->get('token'), 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

function link_resume(CRObject $link)
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'link.update')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$origin = LinkManager::get($link);
	if ($origin === null) {
		$res['errno'] = Code::RECORD_NOT_EXIST;
	} else if ($origin['status'] === '3') {
		$res['errno'] = Code::RECORD_REMOVED;
	} else if ($origin['status'] === '2') {
		$res['errno'] = Code::RECORD_DISABLED;
	} else {
		$origin['status'] = 0;
		$res['errno'] = LinkManager::update(new CRObject($origin)) ? Code::SUCCESS : Code::UNKNOWN_ERROR;
		Cache::expire($link->get('token', '')); // expire cache
	}
	$log = new CRObject();
	$log->set('scope', Session::get('uid'));
	$log->set('tag', 'link.pause');
	$content = array('token' => $link->get('token'), 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

function link_update(CRObject $link)
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'link.update')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$origin = LinkManager::get($link);
	if ($origin === null) {
		$res['errno'] = Code::RECORD_NOT_EXIST;
	} else if ($origin['owner'] !== Session::get('uid')) {
		$res['errno'] = Code::NO_PRIVILEGE;
	} else if ($origin['status'] === '2') {
		$res['errno'] = Code::RECORD_DISABLED;
	} else if ($origin['status'] === '3') {
		$res['errno'] = Code::RECORD_REMOVED;
	} else if (strlen($link->get('url', '')) < URL_MIN_LENGTH || strlen($link->get('url', '') > URL_MAX_LENGTH)) {
		$res['errno'] = Code::URL_LENGTH_INVALID;
	} else {
		$origin['url'] = $link->get('url');
		$origin['remark'] = $link->get('remark');
		$origin['valid_from'] = $link->getInt('valid_form');
		$origin['valid_to'] = $link->getInt('valid_to');
		$res['errno'] = LinkManager::update(new CRObject($origin)) ? Code::SUCCESS : Code::UNKNOWN_ERROR;
		Cache::expire($link->get('token', '')); // expire cache
	}
	$log = new CRObject();
	$log->set('scope', Session::get('uid'));
	$log->set('tag', 'link.update');
	$content = array('link' => $link, 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}

function link_get(CRObject $rule)
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'link.get')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$link_str = Cache::get($rule->get('token', ''));
	if ($link_str === null) {
		$link = LinkManager::get($rule);
	} else {
		$link = json_decode($link_str, true);
	}
	$res['errno'] = Code::RECORD_NOT_EXIST;
	if ($link !== null) {
		if ($link_str === null) {
			Cache::put($rule->get('token', ''), json_encode($link));
		}
		switch ($link['status']) {
			case 0:
				$res['errno'] = Code::SUCCESS;
				$res['token'] = $link['token'];
				$res['url'] = $link['url'];
				break;
			case 1:
				$res['errno'] = Code::RECORD_PAUSED;
				break;
			case 2:
				$res['errno'] = Code::RECORD_DISABLED;
				break;
			case 3:
				$res['errno'] = Code::RECORD_REMOVED;
				break;
		}
	}
	if ($res['errno'] === Code::SUCCESS) {
		$valid_from = (int)$link['valid_from'];
		$valid_to = (int)$link['valid_to'];
		if (($link['valid_from'] !== null && time() < $valid_from) || ($link['valid_to'] !== null && time() > $valid_to)) {
			$res['errno'] = Code::RECORD_NOT_IN_VALID_TIME;
			unset($res['token']);
			unset($res['url']);
		}

	}
	return $res;
}

function link_gets(CRObject $rule)
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'link.gets')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	if ($rule->get('who') !== 'all') {
		$rule->set('who', 'self');
		$rule->set('owner', Session::get('uid'));
	}
	if ($rule->get('who') === 'all' && !AccessController::hasAccess(Session::get('role', 'visitor'), 'link.get_others')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$res['links'] = LinkManager::gets($rule);
	$res['errno'] = $res['links'] === null ? Code::FAIL : Code::SUCCESS;
	return $res;
}

function link_analyze(CRObject $rule)
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'link.analyze')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	if (!ENABLE_LOG_QUERY) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$origin = LinkManager::get($rule);
	if ($origin === null) {
		$res['errno'] = Code::RECORD_NOT_EXIST;
	} else if ($origin['owner'] !== Session::get('uid') && !AccessController::hasAccess(Session::get('role', 'visitor'), 'link.analyze_others')) {
		$res['errno'] = Code::NO_PRIVILEGE;
	} else {
		$res['hist'] = Counter::query($rule);
		$res['errno'] = $res['hist'] === null ? Code::FAIL : Code::SUCCESS;
	}
	$log = new CRObject();
	$log->set('scope', Session::get('uid'));
	$log->set('tag', 'link.analyze');
	$content = array('token' => $rule->get('token'), 'response' => $res['errno']);
	$log->set('content', json_encode($content));
	CRLogger::log($log);
	return $res;
}
