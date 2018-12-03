<?php

require_once('util4p/util.php');
require_once('util4p/CRObject.class.php');
require_once('util4p/RedisDAO.class.php');
require_once('util4p/Random.class.php');

class Cache
{
	private static $time_out = 0; // 0-never expire
	private static $bind_ip = false; // bind session with ip, when client ip changes, previous session will be unavailable
	private static $sid = '';

	/* configuration && initialization */
	public static function configure(CRObject $config)
	{
		self::$time_out = $config->get('time_out', self::$time_out);
		self::$bind_ip = $config->getBool('bind_ip', self::$bind_ip);
	}

	/**/
	public static function put($key, $value)
	{
		$redis = RedisDAO::instance();
		if ($redis === null) {
			return false;
		}
		$redis_key = 'session:' . self::$sid;
		$redis->hset($redis_key, $key, $value);
		$redis->hset($redis_key, '_ip', cr_get_client_ip());
		self::get('_ip');//renew expiration
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
		$redis_key = 'session:' . self::$sid;
		$list = $redis->hgetall($redis_key);
		if (self::$bind_ip) {
			if (!(isset($list['_ip']) && $list['_ip'] === cr_get_client_ip())) {
				return $default;
			}
		}
		if ($redis->ttl($redis_key) < self::$time_out) {
			$redis->expire($redis_key, self::$time_out);
		}
		$redis->disconnect();
		if (isset($list[$key])) {
			return $list[$key];
		}
		return $default;
	}

	/* expire current session */
	public static function expire()
	{
		$redis = RedisDAO::instance();
		if ($redis === null) {
			return false;
		}
		$redis_key = 'session:' . self::$sid;
		$redis->del(array($redis_key));
		$redis->disconnect();
		return true;
	}

}
