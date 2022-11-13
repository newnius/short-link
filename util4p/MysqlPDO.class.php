<?php

require_once('CRObject.class.php');

class MysqlPDO
{
	private static $host = 'localhost';
	private static $port = '3307';
	private static $db = '';
	private static $user = 'root';
	private static $password = '';
	private static $charset = 'utf8';
	private static $show_error = false;
	private $dbh;

	/**/
	public static function configure(CRObject $config)
	{
		self::$host = $config->get('host', self::$host);
		self::$port = $config->getInt('port', self::$port);
		self::$db = $config->get('db', self::$db);
		self::$user = $config->get('user', self::$user);
		self::$password = $config->get('password', self::$password);
		self::$charset = $config->get('charset', self::$charset);
		self::$show_error = $config->getBool('show_error', self::$show_error);
	}

	/**/
	public function __construct()
	{
		$this->connect();
	}

	/**/
	private function connect()
	{
		try {
			$this->dbh = new PDO('mysql:host=' . (self::$host) . ';port=' . (self::$port) . ';dbname=' . (self::$db), self::$user, self::$password);
			$this->dbh->exec('SET names ' . self::$charset);
			return true;
		} catch (PDOException $e) {
			$this->dbh = null;
			if (self::$show_error)
				var_dump($e->getMessage());
			return false;
		}
	}

	/*
	 * @return bool
	 * */
	public function execute($sql, $a_params, $need_inserted_id = false)
	{
		if ($this->dbh === null) {
			return false;
		}
		try {
			$stmt = $this->dbh->prepare($sql);
			$result = $stmt->execute($a_params);
			if (self::$show_error && !$result) {
				var_dump($stmt->errorInfo()[2]);
			}
			if ($need_inserted_id) {
				$result = $result ? $this->dbh->lastInsertId() : false;
			}
			$this->dbh = null;
			return $result;
		} catch (Exception $e) {
			if (self::$show_error)
				var_dump($e->getMessage());
			return false;
		}
	}

	/*
	 * @param $a_params: key-value array
	 */
	function executeQuery($sql, $a_params)
	{
		if ($this->dbh === null) {
			return null;
		}
		try {
			$stmt = $this->dbh->prepare($sql);
			$stmt->setFetchMode(PDO::FETCH_ASSOC);
			$result = null;
			if ($stmt->execute($a_params)) {
				$result = $stmt->fetchAll();
			}
			$this->dbh = null;
			return $result;
		} catch (Exception $e) {
			if (self::$show_error)
				var_dump($e->getMessage());
			return null;
		}
	}

}
