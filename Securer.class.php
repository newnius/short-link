<?php

require_once('util4p/util.php');
require_once('util4p/CRObject.class.php');
require_once('util4p/Random.class.php');

class Securer
{

	/* configuration && initialization */
	public static function configure(CRObject $config)
	{
	}

	/**/
	public static function set_csrf_token()
	{
		if (!isset($_COOKIE['csrf_token'])) {
			setcookie('csrf_token', Random::randomString(32));
		}
	}

	/**/
	public static function validate_csrf_token()
	{
		$csrf_token = null;
		if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
			$csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'];
		}
		$success = $csrf_token !== null && isset($_COOKIE['csrf_token']) && $csrf_token === $_COOKIE['csrf_token'];
		/* whatever, refresh csrf_token to expire current token */
		setcookie('csrf_token', Random::randomString(32));
		return $success;
	}
}
