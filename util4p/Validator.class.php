<?php

class Validator
{
	/**/
	public static function isIP($str)
	{
		$ip = explode('.', $str);
		for ($i = 0; $i < count($ip); $i++) {
			if ($ip[$i] < 0 || $ip[$i] > 255) {
				return false;
			}
		}
		return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $str);
	}

	/**/
	public static function isEmail($str)
	{
		if ($str === null) {
			return false;
		}
		return preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $str) === 1;
	}


	/*TODO*/
	public static function isURL($url)
	{
		if (is_null($url) || empty($url)) {
			return false;
		}
		return true;
	}

}
