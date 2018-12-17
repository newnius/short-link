<?php

/* include this file (only) in every front page */

require_once('Securer.class.php');

/* set csrf token */
Securer::set_csrf_token();

/* set no iframe */
header('X-FRAME-OPTIONS:DENY');
