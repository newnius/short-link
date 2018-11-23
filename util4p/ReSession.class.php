<?php

require_once('util.php');
require_once('CRObject.class.php');
require_once('RedisDAO.class.php');
require_once('Random.class.php');

class Session
{
	private static $time_out = 0; // 0-never expire
	private static $bind_ip = false; // bind session with ip, when client ip changes, previous session will be unavailable
	private static $sid = '';
	private static $guid_key = '_session_id';
	private static $cache = array();

	/* configuration && initialization */
	public static function configure(CRObject $config)
	{
		self::$time_out = $config->get('time_out', self::$time_out);
		self::$bind_ip = $config->getBool('bind_ip', self::$bind_ip);
		self::$guid_key = $config->get('guid_key', self::$guid_key);
		/* assign id from new sessions */
		if (!isset($_COOKIE[self::$guid_key])) {
			$redis = RedisDAO::instance();
			if ($redis === null) {
				return false;
			}
			do { // generate an unique session id
				self::$sid = Random::randomString(64);
			} while ($redis->exists('session:' . self::$sid) === 1);
			$redis->disconnect();
			setcookie(self::$guid_key, self::$sid);
		} else {
			self::$sid = $_COOKIE[self::$guid_key];
		}
	}

	/* Ask browser to remember session id even if browser restarts */
	public static function persist($duration)
	{
		$redis = RedisDAO::instance();
		if ($redis === null) {
			return false;
		}
		$redis_key = 'session:' . self::$sid;
		$redis->expire($redis_key, $duration);
		$redis->disconnect();
		setcookie(self::$guid_key, self::$sid, time() + $duration);
		return true;
	}

	/* attach session to $group to avoid wild sessions */
	public static function attach($group)
	{
		$redis = RedisDAO::instance();
		if ($redis === null) {
			return false;
		}
		$redis_key = 'session:' . self::$sid;
		$key = 'session-group:' . $group;
		$redis->sadd($key, array($redis_key));
		$redis->disconnect();
		return true;
	}

	/* detach session from $group */
	public static function detach($group)
	{
		$redis = RedisDAO::instance();
		if ($redis === null) {
			return false;
		}
		$key = 'session-group:' . $group;
		$redis_key = 'session:' . self::$sid;
		$redis->srem($key, $redis_key);
		$redis->disconnect();
		return true;
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
		self::$cache[$key] = $value;//update cache
		self::get('_ip');//renew expiration
		$redis->disconnect();
		return true;
	}

	/**/
	public static function get($key, $default = null)
	{
		if (isset(self::$cache[$key])) {
			return self::$cache[$key];
		}
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
		self::$cache = $list;
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
		setcookie(self::$guid_key, self::$sid, time() - 3600);
		return true;
	}

	/**/
	public static function expireByGroup($group, $index = null)
	{
		$redis = RedisDAO::instance();
		if ($redis === null) {
			return false;
		}
		$group_key = "session-group:$group";
		$keys = $redis->smembers($group_key);
		foreach ($keys as $i => $key) {
			if ($index === null || $i === $index) {
				$redis->srem($group_key, $key);
				$redis->del($key);
			}
		}
		$redis->disconnect();
		return true;
	}

	/* Low Performance, Not recommended */
	public static function listGroup(CRObject $rule)
	{
		$redis = RedisDAO::instance();
		if ($redis === null) {
			return false;
		}
		$prefix = 'session-group:';
		$keys = $redis->keys($prefix . '*');
		$offset = $rule->getInt('offset', 0);
		$limit = $rule->getInt('limit', -1);
		$groups = array();
		$len = strlen($prefix);
		foreach ($keys as $index => $key) {
			if ($index < $offset) {
				continue;
			}
			if ($limit !== -1 && $offset + $limit <= $index) {
				break;
			}
			$groups[] = array('index' => $index, 'group' => substr($key, $len));
		}
		$redis->disconnect();
		return $groups;
	}

	/* Low Performance, Not recommended */
	public static function listSession(CRObject $rule)
	{
		$redis = RedisDAO::instance();
		if ($redis === null) {
			return false;
		}
		$group = $rule->get('group', '');
		$redis_key = "session-group:$group";
		$keys = $redis->smembers($redis_key);
		$sessions = array();
		foreach ($keys as $index => $key) {
			$session = $redis->hgetall($key);
			if (count($session) === 0) {
				$redis->srem($redis_key, $key);
				continue;
			}
			$session['_index'] = $index;
			$session['_current'] = $key === 'session:' . self::$sid;
			$sessions[] = $session;
		}
		return $sessions;
	}

}
