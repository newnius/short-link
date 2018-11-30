<?php

require_once('util4p/util.php');
require_once('util4p/CRObject.class.php');
require_once('util4p/Random.class.php');
require_once('util4p/AccessController.class.php');
require_once('util4p/CRLogger.class.php');

require_once('Code.class.php');
require_once('LinkManager.class.php');
require_once('Spider.class.php');

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
	} else {
		$link->set('owner', Session::get('uid', -1));
		$res['token'] = $link->get('token');
		$res['errno'] = LinkManager::add($link) ? Code::SUCCESS : Code::UNKNOWN_ERROR;
	}
	$log = new CRObject();
	$log->set('scope', Session::get('uid', '[null]'));
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
	} else if ($origin['owner'] !== Session::get('uid')) {
		$res['errno'] = Code::NO_PRIVILEGE;
	} else {
		$res['errno'] = LinkManager::update($link) ? Code::SUCCESS : Code::UNKNOWN_ERROR;
	}
	$log = new CRObject();
	$log->set('scope', Session::get('uid'));
	$log->set('tag', 'link.remove');
	$content = array('link' => $link, 'response' => $res['errno']);
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
	} else if (strlen($link->get('url', '') < URL_MIN_LENGTH || strlen($link->get('url', '') > URL_MAX_LENGTH))) {
		$res['errno'] = Code::URL_LENGTH_INVALID;
	} else {
		$link->set('status', $origin['status']);
		$res['errno'] = LinkManager::update($link) ? Code::SUCCESS : Code::UNKNOWN_ERROR;
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
	$link = LinkManager::get($rule);
	$res['errno'] = Code::RECORD_NOT_EXIST;
	if ($link !== null) {
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
	return $res;
}

function link_claim(CRObject $rule)
{
	$res['errno'] = Code::IN_DEVELOP;
	return $res;
}

function link_gets(CRObject $rule)
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'contact.get')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$rule->set('owner', Session::get('uid'));
	$res['contacts'] = ContactManager::gets($rule);
	$res['errno'] = $res['contacts'] === null ? Code::FAIL : Code::SUCCESS;
	return $res;
}

function link_analyze(CRObject $rule)
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'contact.get')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$rule->set('owner', Session::get('uid'));
	$res['contacts'] = ContactManager::gets($rule);
	$res['errno'] = $res['contacts'] === null ? Code::FAIL : Code::SUCCESS;
	return $res;
}

function link_report(CRObject $rule)
{
	if (!AccessController::hasAccess(Session::get('role', 'visitor'), 'contact.get')) {
		$res['errno'] = Code::NO_PRIVILEGE;
		return $res;
	}
	$rule->set('owner', Session::get('uid'));
	$res['contacts'] = ContactManager::gets($rule);
	$res['errno'] = $res['contacts'] === null ? Code::FAIL : Code::SUCCESS;
	return $res;
}