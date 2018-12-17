<?php

require_once('util.php');
require_once('CRObject.class.php');
require_once('SQLBuilder.class.php');
require_once('MysqlPDO.class.php');

class CRLogger
{
	const LEVEL_DEBUG = 1;
	const LEVEL_INFO = 2;
	const LEVEL_WARN = 3;
	const LEVEL_ERROR = 4;

	private static $db_table = 'cr_log';

	public static function configure(CRObject $config)
	{
		self::$db_table = $config->get('db_table', self::$db_table);
	}

	public static function log(CRObject $log)
	{
		$scope = $log->get('scope', '');
		$tag = $log->get('tag');
		$level = $log->getInt('level', self::LEVEL_INFO);
		$ip = $log->get('ip', cr_get_client_ip(false));
		$time = $log->getInt('time', time());
		$content = $log->get('content');

		$key_values = array(
			'scope' => '?', 'tag' => '?', 'level' => '?', 'ip' => '?', 'time' => '?', 'content' => '?'
		);
		$builder = new SQLBuilder();
		$builder->insert(self::$db_table, $key_values);
		$sql = $builder->build();
		$params = array($scope, $tag, $level, ip2long($ip), $time, $content);
		return (new MysqlPDO())->execute($sql, $params);
	}

	public static function search(CRObject $filter)
	{
		$scope = $filter->get('scope');
		$tag = $filter->get('tag');
		$level_min = $filter->getInt('level_min');
		$ip = $filter->get('ip');
		$time_begin = $filter->getInt('time_begin');
		$time_end = $filter->getInt('time_end');
		$offset = $filter->getInt('offset', 0);
		$limit = $filter->getInt('limit', -1);
		$order = $filter->get('order');

		$selected_rows = array('id', 'scope', 'tag', 'level', 'ip', 'time', 'content');
		$where_arr = array();
		$opt_arr = array();
		$order_arr = array();
		$params = array();

		if (!empty($scope)) {
			$where_arr['scope'] = '?';
			$params[] = $scope;
		}
		if (!empty($tag)) {
			$where_arr['tag'] = '?';
			$params[] = $tag;
		}
		if (!is_null($level_min)) {
			$where_arr['level'] = '?';
			$opt_arr['level'] = '>=';
			$params[] = $level_min;
		}
		if (!empty($ip)) {
			$where_arr['ip'] = '?';
			$params[] = ip2long($ip);
		}
		if (!is_null($time_begin) && !is_null($time_end)) {
			$where_arr['time'] = '? AND ?';
			$opt_arr['time'] = 'BETWEEN';
			$params[] = $time_begin;
			$params[] = $time_end;
		} else if (!is_null($time_begin)) {
			$where_arr['time'] = '?';
			$opt_arr['time'] = '>=';
			$params[] = $time_begin;
		} else if (!is_null($time_end)) {
			$where_arr['time'] = '?';
			$opt_arr['time'] = '<=';
			$params[] = $time_end;
		}

		switch ($order) {
			case 'latest':
				$order_arr['time'] = 'desc';
				break;
			default:
				$order_arr['id'] = 'desc';
				break;
		}
		$builder = new SQLBuilder();
		$builder->select(self::$db_table, $selected_rows);
		$builder->where($where_arr, $opt_arr);
		$builder->order($order_arr);
		$builder->limit($offset, $limit);
		$sql = $builder->build();
		$logs = (new MysqlPDO())->executeQuery($sql, $params);
		return $logs;
	}

	public static function getCount(CRObject $filter)
	{
		$scope = $filter->get('scope');
		$tag = $filter->get('tag');
		$level_min = $filter->getInt('level_min');
		$ip = $filter->get('ip');
		$time_begin = $filter->getInt('time_begin');
		$time_end = $filter->getInt('time_end');

		$selected_rows = array('id');
		$where_arr = array();
		$opt_arr = array();
		$params = array();

		if (!empty($scope)) {
			$where_arr['scope'] = '?';
			$params[] = $scope;
		}
		if (!empty($tag)) {
			$where_arr['tag'] = '?';
			$params[] = $tag;
		}
		if (!is_null($level_min)) {
			$where_arr['level'] = '?';
			$opt_arr['level'] = '>=';
			$params[] = $level_min;
		}
		if (!empty($ip)) {
			$where_arr['ip'] = '?';
			$params[] = ip2long($ip);
		}
		if (!is_null($time_begin) && !is_null($time_end)) {
			$where_arr['time'] = '? AND ?';
			$opt_arr['time'] = 'BETWEEN';
			$params[] = $time_begin;
			$params[] = $time_end;
		} else if (!is_null($time_begin)) {
			$where_arr['time'] = '?';
			$opt_arr['time'] = '>=';
			$params[] = $time_begin;
		} else if (!is_null($time_end)) {
			$where_arr['time'] = '?';
			$opt_arr['time'] = '<=';
			$params[] = $time_end;
		}

		$builder = new SQLBuilder();
		$builder->select(self::$db_table, $selected_rows);
		$builder->where($where_arr, $opt_arr);
		$builder->limit(0, 1000);
		$sql = $builder->build();
		$sql = "SELECT COUNT(1) AS `count` FROM ( $sql ) as tmp";
		$res = (new MysqlPDO())->executeQuery($sql, $params);
		return intval($res[0]['count']);
	}

}
