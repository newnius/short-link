<?php

require_once('CRObject.class.php');
if (!class_exists('Predis\Client')) {
	header('HTTP/1.1 500 Internal Server Error');
	var_dump('predis (github.com/nrk/predis.git) required');
	exit;
}

class RedisDAO
{
	private static $scheme = 'tcp';
	private static $host = 'localhost';
	private static $port = 6379;
	private static $show_error = false;

	public static function configure(CRObject $config)
	{
		self::$scheme = $config->get('scheme', self::$scheme);
		self::$host = $config->get('host', self::$host);
		self::$port = $config->getInt('port', self::$port);
		self::$show_error = $config->getBool('show_error', self::$show_error);
	}

	public static function instance()
	{
		try {
			$redis = new Predis\Client(
				array(
					'scheme' => RedisDAO::$scheme,
					'host' => RedisDAO::$host,
					'port' => RedisDAO::$port
				)
			);
			$redis->connect();
			return $redis;
		} catch (Exception $e) {
			if (self::$show_error)
				var_dump($e->getMessage());
			return null;
		}
	}

}
