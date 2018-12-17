<?php

require_once('util4p/util.php');
require_once('util4p/CRObject.class.php');
require_once('util4p/SQLBuilder.class.php');
require_once('util4p/MysqlPDO.class.php');

class Counter
{
	private static $db_table = 'ls_query_log';

	public static function configure(CRObject $config)
	{
		self::$db_table = $config->get('db_table', self::$db_table);
	}

	public static function log(CRObject $log)
	{
		$token = $log->get('token', '');
		$ip = $log->get('ip', cr_get_client_ip(false));
		$referer = $log->getInt('referer');
		$time = $log->getInt('time', time());
		$ua = $log->getInt('ua');
		$lang = $log->getInt('lang');

		$key_values = array(
			'token' => '?', 'ip' => '?', 'referer' => '?', 'time' => '?', 'ua' => '?', 'lang' => '?'
		);
		$builder = new SQLBuilder();
		$builder->insert(self::$db_table, $key_values);
		$sql = $builder->build();
		$params = array($token, ip2long($ip), $referer, $time, $ua, $lang);
		return (new MysqlPDO())->execute($sql, $params);
	}

	public static function query(CRObject $filter)
	{
		$token = $filter->get('token');
		$interval = $filter->getInt('interval', 1);// default 5 min
		if ($interval <= 0) {
			$interval = 1;
		}
		$interval = $interval * 60;
		$time_begin = $filter->getInt('time_begin');
		$time_end = $filter->getInt('time_end');

		$sql = 'SELECT FLOOR(`time` / ?) AS `t`, COUNT(1) AS cnt FROM `ls_query_log` WHERE `token` = ? GROUP BY FLOOR(`time` / ?) ORDER BY `t` DESC LIMIT 60';

		$sql = "SELECT t * ? as `time`, `cnt` FROM ($sql) AS tmp ORDER BY `time` ASC";
		$params = array($interval, $interval, $token, $interval);
		$logs = (new MysqlPDO())->executeQuery($sql, $params);
		return $logs;
	}

}
