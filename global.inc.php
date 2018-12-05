<?php

require_once('predis/autoload.php');
require_once('util4p/util.php');
require_once('util4p/Random.class.php');
require_once('util4p/ReSession.class.php');
require_once('util4p/AccessController.class.php');

require_once('Code.class.php');
require_once('config.inc.php');
require_once('init.inc.php');

/* set csrf token */
if (!isset($_COOKIE['csrf_token'])) {
	setcookie('csrf_token', Random::randomString(32));
}

/* set no iframe */
header('X-FRAME-OPTIONS:DENY');