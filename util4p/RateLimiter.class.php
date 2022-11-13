<?php

require_once('util.php');
require_once('RedisDAO.class.php');

/*
 * limit request rate, prevent spam
 * normally used in fields such as login, register
 * based on fatigue degree
 *
 * fatigue degree grow algorithm rule, call increase($degree)
 * rule format
 * array(
 *    'degree'=>100, //min point to reach this punishment
 *    'interval'=>300 //count interval, degree will expire after interval
 *  )
 *
 *  rules are stored in redis db
 *  will auto grow by fatigue degree, call setAutoIncrease($bool)
 *  punish directly, call punish($arr)
 */

class RateLimiter
{
	private static $keyPrefix = 'rl';
	private static $id = '';
	private static $rules = array();


	/*
	 * @param $key customize your own key, default is ip2long(ip)
	 */
	public static function configure(CRObject $config)
	{
		self::$keyPrefix = $config->get('key_prefix', self::$keyPrefix);
		self::$id = $config->get('id', cr_get_client_ip(false));
		self::$rules = $config->get('rules', self::$rules);
	}


	/*
	 * @param
	 *
	 */
	public static function increase($degree)
	{
		if (!is_numeric($degree)) {
			return false;
		}
		$lua_script = <<<LUA
				local degree = redis.call('incrby', KEYS[1], ARGV[1])
				if degree == tonumber(ARGV[1]) then
					redis.call('expire', KEYS[1], ARGV[2])
				end
				return degree
LUA;
		$redis = RedisDAO::instance();
		if ($redis === null) {
			return false;
		}
		foreach (self::$rules as $rule) {
			$interval = $rule['interval'];
			$key = self::$keyPrefix . ':degree:' . self::$id . '-' . $interval;
			$redis->eval($lua_script, 1, $key, $degree, $interval);
		}
		$redis->disconnect();
		$rule = self::whichRuleToPunish();
		if ($rule !== null) {
			self::punish($rule);
		}
		return true;
	}


	/**/
	public static function punish($rule, $id = null)
	{
		if ($id === null) {
			$id = self::$id;
		}
		$redis = RedisDAO::instance();
		if ($redis === null) {
			return false;
		}
		$redis->set(self::$keyPrefix . ':punishing:' . $id, $rule['degree'], 'EX', $rule['interval']);
		$redis->disconnect();
		return true;
	}


	/*
	 * get punish time left, negative means not being punished
	 */
	public static function getFreezeTime($id = null)
	{
		if ($id === null) {
			$id = self::$id;
		}
		$redis = RedisDAO::instance();
		if ($redis === null) {
			return 0;
		}
		$freezeTime = (int)$redis->ttl(self::$keyPrefix . ':punishing:' . $id);
		$redis->disconnect();
		return $freezeTime;
	}

	/*
	 * get which rule to punish current user
	 * mostly of the time, you dont have to call this, as it is called automatically
	 */
	private static function whichRuleToPunish()
	{
		$redis = RedisDAO::instance();
		if ($redis === null) {
			return null;
		}
		foreach (self::$rules as $rule) {
			$interval = $rule['interval'];
			$key = self::$keyPrefix . ':degree:' . self::$id . '-' . $interval;
			$degree = (int)$redis->get($key);
			if ($degree > $rule['degree']) {
				$redis->disconnect();
				return $rule;
			}
		}
		$redis->disconnect();
		return null;
	}

	/* clear degree count and punishing state */
	public static function clear($id = null)
	{
		if ($id === null) {
			$id = self::$id;
		}
		$redis = RedisDAO::instance();
		if ($redis === null) {
			return null;
		}
		foreach (self::$rules as $rule) {
			$interval = $rule['interval'];
			$key = self::$keyPrefix . ':degree:' . $id . '-' . $interval;
			$redis->del(array($key));
		}
		$redis->del(array(self::$keyPrefix . ':punishing:' . $id));
		$redis->disconnect();
		return true;
	}

	/* list IDs being punished */
	public static function listPunished()
	{
		$redis = RedisDAO::instance();
		if ($redis === null) {
			return false;
		}
		$redis_key = self::$keyPrefix . ':punishing:*';
		$list = $redis->keys($redis_key);
		$redis->disconnect();
		$len = strlen(self::$keyPrefix . ':punishing:');
		$ids = array();
		foreach ($list as $item) {
			$ids[]['id'] = mb_substr($item, $len);
		}
		return $ids;
	}

}
