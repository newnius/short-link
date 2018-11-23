<?php

session_start();

class Session
{
	private static $time_out = 0; // 0-never expire
	private static $bind_ip = false; // bind session with ip, when client ip changes, previous session will be unavailable

	/**/
	public static function configure(CRObject $config)
	{
		self::$time_out = $config->get('time_out', self::$time_out);
		self::$bind_ip = $config->getBool('bind_ip', self::$bind_ip);
	}

	/**/
	public static function put($key, $value)
	{
		$_SESSION[$key] = $value;
		$_SESSION['_SELF']['LAST_ACTIVE'] = time();
		return true;
	}

	/**/
	public static function get($key, $default = null)
	{
		if (!isset($_SESSION['_SELF']['LAST_ACTIVE'])) {
			$_SESSION['_SELF']['LAST_ACTIVE'] = 0;
		}
		if (self::$time_out > 0 && time() - $_SESSION['_SELF']['LAST_ACTIVE'] > self::$time_out) {
			return $default;
		}
		$_SESSION['_SELF']['LAST_ACTIVE'] = time();
		if (isset($_SESSION[$key]) && !is_null($_SESSION[$key])) {
			return $_SESSION[$key];
		}
		return $default;
	}

	/* expire current session */
	public static function expire()
	{
		$_SESSION = array();
		session_destroy();
		return true;
	}

}
