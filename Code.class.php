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
	const EMAIL_IS_NOT_VERIFIED = 23;

	const USERNAME_MISMATCH_EMAIL = 24;

	const CODE_EXPIRED = 25;
	const EMAIL_ALREADY_VERIFIED = 26;
	const INVALID_COOKIE = 27;

	/* site */
	const INVALID_DOMAIN = 28;
	const NEED_VERIFY = 29;
	const INVALID_PATTERN = 30;

	/* auth */
	const TOKEN_EXPIRED = 31;
	const SITE_NOT_EXIST = 32;
	const INVALID_URL = 33;
	const INVALID_PARAM = 34;
	const DOMAIN_MISMATCH = 35;

	const TOKEN_LENGTH_INVALID = 36;
	const URL_LENGTH_INVALID = 37;

	const RECORD_PAUSED = 38;
	const RECORD_REMOVED = 39;
	const RECORD_DISABLED = 40;
	const RECORD_NOT_IN_VALID_TIME = 41;

	/* rate limit */
	const TOO_FAST = 30;

	public static function getErrorMsg($errno)
	{
		switch ($errno) {
			case Code::SUCCESS:
				return '成功！';

			case Code::USERNAME_OCCUPIED:
				return '用户名已存在！';

			case Code::EMAIL_OCCUPIED:
				return '邮箱已存在！';

			case Code::NO_PRIVILEGE:
				return '没有权限执行此项操作，请检查您的登陆状态！';

			case Code::INVALID_USERNAME:
				return '无效的用户名！';

			case Code::INVALID_EMAIL:
				return '无效的邮箱！';

			case Code::UNKNOWN_ERROR:
				return '未知错误！';

			case Code::WRONG_PASSWORD:
				return '密码错误！';

			case Code::IN_DEVELOP:
				return '正在开发中 ^_^ ！';

			case Code::UNABLE_TO_CONNECT_REDIS:
				return '连接 Redis 失败！';

			case Code::UNABLE_TO_CONNECT_MYSQL:
				return '连接 Mysql 失败！';

			case Code::NOT_LOGED:
				return '您尚未登陆！';

			case Code::USER_NOT_EXIST:
				return '用户不存在！';

			case Code::INVALID_REQUEST:
				return '无效的请求参数！';

			case Code::UNKNOWN_REQUEST:
				return '未知请求！';

			case Code::CAN_NOT_BE_EMPTY:
				return '必填项不可为空！';

			case Code::FAIL:
				return '失败！';

			case Code::INCOMPLETE_CONTENT:
				return '参数不能为空！';

			case Code::FILE_NOT_UPLOADED:
				return '上传文件失败！';

			case Code::RECORD_NOT_EXIST:
				return '未找到该条记录！';

			case Code::RECORD_ALREADY_EXIST:
				return '该记录已存在！';

			case Code::USER_IS_BLOCKED:
				return '账号已被锁定！';

			case Code::USER_IS_REMOVED:
				return '账号已被删除！';

			case Code::INVALID_PASSWORD:
				return '无效的密码！';

			case Code::USERNAME_MISMATCH_EMAIL:
				return '账号或密码错误！';

			case Code::CODE_EXPIRED:
				return 'Code 错误或已失效';

			case Code::EMAIL_ALREADY_VERIFIED:
				return 'Email 已经验证！';

			case Code::TOO_FAST:
				return '系统繁忙，请稍后再试！';

			case Code::INVALID_COOKIE:
				return '无效的 Cookie ！';

			case Code::TOKEN_EXPIRED:
				return 'Token 已失效！';

			case Code::SITE_NOT_EXIST:
				return '该站点不存在！';

			case Code::INVALID_URL:
				return '无效的链接！';

			case Code::INVALID_PARAM:
				return '无效的参数！';

			case Code::DOMAIN_MISMATCH:
				return 'redirect_uri 不在允许列表！';

			case Code::EMAIL_IS_NOT_VERIFIED:
				return '请验证您的邮箱！';

			case Code::NEED_VERIFY:
				return '请先验证您的站点！';

			case Code::TOKEN_LENGTH_INVALID:
				return '自定义短链长度不符合要求！';

			case Code::URL_LENGTH_INVALID:
				return '链接长度不符合要求！';

			case Code::RECORD_DISABLED:
				return '该条记录已被禁用！';

			case Code::RECORD_NOT_IN_VALID_TIME:
				return '不在有效期内！';

			default:
				return '未知错误，错误代码(' . $errno . ') !';
		}
	}
}
