<?php

class Code
{
	/* common */
	const SUCCESS = 0;
	const FAIL = 1;
	const NO_PRIVILEGE = 2;
	const UNKNOWN_ERROR = 3;
	const IN_DEVELOP = 4;
	const INVALID_REQUEST = 5;
	const UNKNOWN_REQUEST = 6;
	const CAN_NOT_BE_EMPTY = 7;
	const INCOMPLETE_CONTENT = 8;
	const FILE_NOT_UPLOADED = 9;
	const RECORD_NOT_EXIST = 10;
	const RECORD_ALREADY_EXIST = 34;
	const INVALID_PASSWORD = 11;
	const UNABLE_TO_CONNECT_REDIS = 12;
	const UNABLE_TO_CONNECT_MYSQL = 13;

	/* user */
	const USERNAME_OCCUPIED = 14;
	const EMAIL_OCCUPIED = 15;
	const INVALID_USERNAME = 16;
	const INVALID_EMAIL = 17;
	const WRONG_PASSWORD = 18;
	const NOT_LOGED = 19;
	const USER_NOT_EXIST = 20;
	const USER_IS_BLOCKED = 21;
	const USER_IS_REMOVED = 22;
	const EMAIL_IS_NOT_VERIFIED = 33;

	const USERNAME_MISMATCH_EMAIL = 23;

	const CODE_EXPIRED = 24;
	const EMAIL_ALREADY_VERIFIED = 25;
	const INVALID_COOKIE = 26;

	/* site */
	const INVALID_DOMAIN = 33;
	const NEED_VERIFY = 35;
	const INVALID_PATTERN = 36;

	/* auth */
	const TOKEN_EXPIRED = 27;
	const SITE_NOT_EXIST = 28;
	const INVALID_URL = 29;
	const INVALID_PARAM = 31;
	const DOMAIN_MISMATCH = 32;

	const TOKEN_LENGTH_INVALID = 35;
	const URL_LENGTH_INVALID = 36;

	const RECORD_PAUSED = 37;
	const RECORD_REMOVED = 38;
	const RECORD_DISABLED = 39;
	const RECORD_NOT_IN_VALID_TIME = 40;

	/* rate limit */
	const TOO_FAST = 30;

	public static function getErrorMsg($errno)
	{
		switch ($errno) {
			case Code::SUCCESS:
				return 'Success !';

			case Code::USERNAME_OCCUPIED:
				return 'Username exists !';

			case Code::EMAIL_OCCUPIED:
				return 'Email exists !';

			case Code::NO_PRIVILEGE:
				return 'You don\'t have permission to do this !';

			case Code::INVALID_USERNAME:
				return 'Invalid username !';

			case Code::INVALID_EMAIL:
				return 'Invalid email !';

			case Code::UNKNOWN_ERROR:
				return 'Unknown error !';

			case Code::WRONG_PASSWORD:
				return 'Wrong password !';

			case Code::IN_DEVELOP:
				return 'In develop ^_^ !';

			case Code::UNABLE_TO_CONNECT_REDIS:
				return 'Unable to connect Redis !';

			case Code::UNABLE_TO_CONNECT_MYSQL:
				return 'Unable to connect Mysql !';

			case Code::NOT_LOGED:
				return 'You haven\'t loged !';

			case Code::USER_NOT_EXIST:
				return 'User not exist !';

			case Code::INVALID_REQUEST:
				return 'Invalid request !';

			case Code::UNKNOWN_REQUEST:
				return 'Unknown request !';

			case Code::CAN_NOT_BE_EMPTY:
				return 'Input is empty !';

			case Code::FAIL:
				return 'Failed !';

			case Code::INCOMPLETE_CONTENT:
				return 'Cannot be empty !';

			case Code::FILE_NOT_UPLOADED:
				return 'Upload failed !';

			case Code::RECORD_NOT_EXIST:
				return 'Record not found !';

			case Code::RECORD_ALREADY_EXIST:
				return 'Record already exists !';

			case Code::USER_IS_BLOCKED:
				return 'Account is blocked !';

			case Code::USER_IS_REMOVED:
				return 'Account is removed !';

			case Code::INVALID_PASSWORD:
				return 'Invalid password !';

			case Code::USERNAME_MISMATCH_EMAIL:
				return 'Username or email not match !';

			case Code::CODE_EXPIRED:
				return 'Code is wrong or expires !';

			case Code::EMAIL_ALREADY_VERIFIED:
				return 'Email is already verified !';

			case Code::TOO_FAST:
				return 'System busy !';

			case Code::INVALID_COOKIE:
				return 'Invalid Cookie !';

			case Code::TOKEN_EXPIRED:
				return 'Token expired !';

			case Code::SITE_NOT_EXIST:
				return 'Site not exist !';

			case Code::INVALID_URL:
				return 'Invalid url !';

			case Code::INVALID_PARAM:
				return 'Invalid param !';

			case Code::DOMAIN_MISMATCH:
				return 'redirect_uri not in allowed hosts !';

			case Code::EMAIL_IS_NOT_VERIFIED:
				return 'Verify your email first !';

			case Code::NEED_VERIFY:
				return 'You have to verify your domain first !';

			case Code::TOKEN_LENGTH_INVALID:
				return 'token length invalid';

			case Code::URL_LENGTH_INVALID:
				return 'url length invalid';

			case Code::RECORD_NOT_IN_VALID_TIME:
				return 'Not in valid time !';

			default:
				return 'Unknown error(' . $errno . ') !';
		}
	}
}
