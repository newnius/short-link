<?php

require_once('util4p/CRObject.class.php');
require_once('util4p/MysqlPDO.class.php');
require_once('util4p/SQLBuilder.class.php');

class LinkManager
{
	/*
	 * do add link
	 */
	public static function add(CRObject $link)
	{
		$token = $link->get('token', '');
		$url = $link->get('url', '');
		$remark = $link->get('remark');
		$valid_from = $link->getInt('valid_from');
		$valid_to = $link->getInt('valid_to');
		$owner = $link->get('owner');

		$key_values = array(
			'token' => '?', 'url' => '?', 'remark' => '?',
			'valid_from' => '?', 'valid_to' => '?', 'time' => '?', 'owner' => '?'
		);
		$builder = new SQLBuilder();
		$builder->insert('ls_link', $key_values);
		$sql = $builder->build();
		$params = array($token, $url, $remark, $valid_from, $valid_to, time(), $owner);
		return (new MysqlPDO())->execute($sql, $params);
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
		$opts = array();
		if ($owner) {
			$where['owner'] = '?';
			$params[] = $owner;
			$where['status'] = '3';
			$opts['status'] = '!=';
		}
		$order_by = array('time' => 'DESC');
		$builder = new SQLBuilder();
		$builder->select('ls_link', $selected_rows);
		$builder->where($where, $opts);
		$builder->order($order_by);
		$builder->limit($offset, $limit);
		$sql = $builder->build();
		$links = (new MysqlPDO())->executeQuery($sql, $params);
		return $links;
	}

	/* */
	public static function count(CRObject $rule)
	{
		$owner = $rule->get('owner', '');
		$selected_rows = array('COUNT(1) as cnt');
		$where = array();
		$params = array();
		$opts = array();
		if ($owner) {
			$where['owner'] = '?';
			$params[] = $owner;
			$where['status'] = '3';
			$opts['status'] = '!=';
		}
		$builder = new SQLBuilder();
		$builder->select('ls_link', $selected_rows);
		$builder->where($where, $opts);
		$sql = $builder->build();
		$res = (new MysqlPDO())->executeQuery($sql, $params);
		return $res === null ? 0 : intval($res[0]['cnt']);
	}

	/* get link by token */
	public static function get(CRObject $rule)
	{
		$token = $rule->get('token', '');
		$selected_rows = array();
		$where = array('token' => '?');
		$params = array($token);
		$builder = new SQLBuilder();
		$builder->select('ls_link', $selected_rows);
		$builder->where($where);
		$sql = $builder->build();
		$links = (new MysqlPDO())->executeQuery($sql, $params);
		return $links !== null && count($links) === 1 ? $links[0] : null;
	}

	/* */
	public static function update(CRObject $link)
	{
		$token = $link->get('token', '');
		$url = $link->get('url', '');
		$remark = $link->get('remark');
		$valid_from = $link->getInt('valid_from');
		$valid_to = $link->getInt('valid_to');
		$status = $link->getInt('status', 0);

		$key_values = array(
			'url' => '?', 'remark' => '?', 'valid_from' => '?', 'valid_to' => '?', 'status' => '?'
		);
		$where = array('token' => '?');
		$builder = new SQLBuilder();
		$builder->update('ls_link', $key_values);
		$builder->where($where);
		$sql = $builder->build();
		$params = array($url, $remark, $valid_from, $valid_to, $status, $token);
		return (new MysqlPDO())->execute($sql, $params);
	}
}
