<?php

require_once('util4p/util.php');
require_once('util4p/CRObject.class.php');
require_once('util4p/RedisDAO.class.php');

class Cache
{
	private static $time_out = 300; // 5 min
	private static $prefix = 'cache';

	/* configuration && initialization */
	public static function configure(CRObject $config)
	{
		self::$time_out = (int)$config->get('time_out', self::$time_out);
		self::$prefix = $config->get('prefix', self::$prefix);
	}

	/**/
	public static function put($key, $value)
	{
		$redis = RedisDAO::instance();
		if ($redis === null) {
			return false;
		}
		$redis_key = self::$prefix . ':' . $key;
		$redis->set($redis_key, $value, 'EX', self::$time_out);
		$redis->disconnect();
		return true;
	}

	/**/
	public static function get($key, $default = null)
	{
		$redis = RedisDAO::instance();
		if ($redis === null) {
			return $default;
		}
		$redis_key = self::$prefix . ':' . $key;
		$value = $redis->get($redis_key);
		if (!is_null($value)) { //hit
			$redis->expire($redis_key, self::$time_out); // reset time out
		} else { //miss
			$value = $default;
		}
		$redis->disconnect();
		return $value;
	}

	/* expire by key */
	public static function expire($key)
	{
		$redis = RedisDAO::instance();
		if ($redis === null) {
			return false;
		}
		$redis_key = self::$prefix . ':' . $key;
		$redis->del(array($redis_key));
		$redis->disconnect();
		return true;
	}

}
