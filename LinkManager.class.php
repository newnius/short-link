<?php

require_once('util4p/CRObject.class.php');
require_once('util4p/MysqlPDO.class.php');
require_once('util4p/SQLBuilder.class.php');

class LinkManager
{
	/*
	 * do add contact
	 */
	public static function add(CRObject $link)
	{
		$token = $link->get('token', '');
		$url = $link->get('url', '');
		$remark = $link->get('remark', '');
		$limit = $link->getInt('limit', -1);
		$valid_from = $link->getInt('valid_from', -1);
		$valid_to = $link->getInt('valid_to', -1);
		$owner = $link->get('owner');

		$key_values = array(
			'token' => '?', 'url' => '?', 'remark' => '?', 'limit' => '?',
			'valid_from' => '?', 'valid_to' => '?', 'time' => '?', 'owner' => '?'
		);
		$builder = new SQLBuilder();
		$builder->insert('ls_link', $key_values);
		$sql = $builder->build();
		$params = array($token, $url, $remark, $limit, $valid_from, $valid_to, time(), $owner);
		$count = (new MysqlPDO())->execute($sql, $params);
		return $count === 1;
	}

	/* */
	public static function gets(CRObject $rule)
	{
		$owner = $rule->get('owner', '');
		$offset = $rule->getInt('offset', 0);
		$limit = $rule->getInt('limit', -1);
		$selected_rows = array();
		$where = array();
		$params = array();
		if ($owner) {
			$where['owner'] = '?';
			$params[] = $owner;
		}
		$builder = new SQLBuilder();
		$builder->select('ls_link', $selected_rows);
		$builder->where($where);
		$builder->limit($offset, $limit);
		$sql = $builder->build();
		$contacts = (new MysqlPDO())->executeQuery($sql, $params);
		return $contacts;
	}

	/* */
	public static function get(CRObject $rule)
	{
		$token = $rule->get('token');
		$selected_rows = array();
		$where = array('token' => '?');
		$params = array($token);
		$builder = new SQLBuilder();
		$builder->select('ls_link', $selected_rows);
		$builder->where($where);
		$sql = $builder->build();
		$links = (new MysqlPDO())->executeQuery($sql, $params);
		return count($links) == 1 ? $links[0] : null;
	}

	/* */
	public static function remove(CRObject $contact)
	{
		$id = $contact->getInt('id', 0);
		$where = array('id' => '?');
		$builder = new SQLBuilder();
		$builder->delete('tel_contact');
		$builder->where($where);
		$sql = $builder->build();
		$params = array($id);
		$count = (new MysqlPDO())->execute($sql, $params);
		return $count > 0;
	}

	/* */
	public static function update(CRObject $link)
	{
		$token = $link->get('token', '');
		$url = $link->get('url', '');
		$remark = $link->get('remark', '');
		$limit = $link->getInt('limit', -1);
		$valid_from = $link->getInt('valid_from', -1);
		$valid_to = $link->getInt('valid_to', -1);
		$status = $link->getInt('status', 0);

		$key_values = array(
			'url' => '?', 'remark' => '?', 'limit' => '?', 'valid_from' => '?', 'valid_to' => '?', 'status' => '?'
		);
		$where = array('token' => '?');
		$builder = new SQLBuilder();
		$builder->update('ls_link', $key_values);
		$builder->where($where);
		$sql = $builder->build();
		$params = array($url, $remark, $limit, $valid_from, $valid_to, $status, $token);
		$count = (new MysqlPDO())->execute($sql, $params);
		return $count !== null;
	}

}
