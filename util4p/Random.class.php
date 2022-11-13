<?php

class Random
{
	const LEVEL_LOW = 1;
	const LEVEL_MIDDLE = 2;
	const LEVEL_HIGH = 3;

	/*
	 * generate a digit in range of [$min, $max]
	 * origin: stackoverflow
	 * notice: this method tends to generate unique numbers compared with rand()
	 */
	public static function randomInt($min, $max)
	{
		$range = intval($max) - intval($min);
		if ($range < 1) return $min; // not so random...
		$log = ceil(log($range, 2));
		$bytes = (int)($log / 8) + 1; // length in bytes
		$bits = (int)$log + 1; // length in bits
		$filter = (int)(1 << $bits) - 1; // set all lower bits to 1
		do {
			$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
			$rnd = $rnd & $filter; // discard irrelevant bits
		} while ($rnd >= $range);
		return $min + $rnd;
	}

	/*
	 * generate random string of length $length
	 * level: LOW - only numbers, MIDDLE - plus letters(upper and lower), HIGH - plus special chars
	 */
	public static function randomString($strlen, $level = self::LEVEL_MIDDLE)
	{
		$alphabet = '0123456789';
		if ($level > self::LEVEL_LOW) {
			$alphabet .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$alphabet .= 'abcdefghijklmnopqrstuvwxyz';
		}
		if ($level > self::LEVEL_MIDDLE)
			$alphabet .= '+-*/?!%`~@#^&(){}';

		$length = strlen($alphabet);
		$token = '';
		for ($i = 0; $i < $strlen; $i++) {
			$token .= $alphabet[self::randomInt(0, $length - 1)];
		}
		return $token;
	}

}
