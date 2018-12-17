<?php

require_once('util4p/util.php');
require_once('util4p/CRObject.class.php');
require_once('util4p/SQLBuilder.class.php');
require_once('util4p/MysqlPDO.class.php');
require_once('util4p/Validator.class.php');

class UserManager
{

	/**/
	public static function add(CRObject $user)
	{
		$open_id = $user->get('open_id');
		$email = $user->get('email');
		$role = $user->get('role');
		$level = $user->getInt('level', 0);
		if ($email !== null && !Validator::isEmail($email)) {
			return false;
		}
		$key_values = array('open_id' => '?', 'email' => '?', 'role' => '?', 'time' => '?', 'level' => '?');
		$builder = new SQLBuilder();
		$builder->insert('ls_user', $key_values);
		$sql = $builder->build();
		$params = array($open_id, $email, $role, time(), $level);
		return (new MysqlPDO())->execute($sql, $params);
	}

	/**/
	public static function getByUID($uid)
	{
		$selected_rows = array();
		$where_arr = array('uid' => '?');
		$builder = new SQLBuilder();
		$builder->select('ls_user', $selected_rows);
		$builder->where($where_arr);
		$sql = $builder->build();
		$params = array($uid);
		$users = (new MysqlPDO())->executeQuery($sql, $params);
		return count($users) === 1 ? $users[0] : null;
	}

	/**/
	public static function getByOpenID($open_id)
	{
		$selected_rows = array();
		$where_arr = array('open_id' => '?');
		$builder = new SQLBuilder();
		$builder->select('ls_user', $selected_rows);
		$builder->where($where_arr);
		$sql = $builder->build();
		$params = array($open_id);
		$users = (new MysqlPDO())->executeQuery($sql, $params);
		return count($users) === 1 ? $users[0] : null;
	}
}