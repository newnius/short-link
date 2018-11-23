<?php

class AccessController
{
	private static $rules_array = array();

	/*
	 * set privilege bitmap
	 * sample $map = array(
	 *	'post.add' => array('Admin', 'Moderator', 'User'),
	 *	'post.comment' => array'Admin', 'Moderator', 'User'),
	 *	'post.pin' => array('Admin', 'Moderator'),
	 *	'user.block' => array('Admin')
	 * );
	 */
	public static function setMap(array $map)
	{
		if (is_array($map)) {
			self::$rules_array = $map;
		}
	}

	/*
	 * AccessController::hasAccess('Moderator', 'user.block');
	 */
	public static function hasAccess($role, $operation)
	{
		if (array_key_exists($operation, self::$rules_array)) {
			return in_array($role, self::$rules_array[$operation]);
		}
		return false;
	}

}
