<?php
  if(file_exists('util.php'))
  {
    require_once('util.php');
  }else
  {
    exit("file util.php not exist");
  }
  
  define('TOKEN_MIN_LENGTH', 5);
  define('TOKEN_MAX_LENGTH', 15);
  define('URL_MIN_LENGTH', 5);
  define('URL_MAX_LENGTH', 500);

  /* confihure mysql */
  define("DB_HOST","localhost");
  define("DB_NAME","shortlink");
  define("DB_PORT","3306");
  define("DB_USER","root");
  define("DB_PASSWORD","123456");

  /* configure rate controller module */
  cr_require_file('RateController.php');
  define('FATIGUE_SET', 5);
  define('FATIGUE_GET', 1);
  define('FATIGUE_ANALYZE', 10);
  $rule1 = array(
    'minDegree'=> 50,
    'freezeTime' => 30
  );
  $rule2 = array(
    'minDegree'=> 200,
    'freezeTime' => 5*60
  );
  $rule3 = array(
    'minDegree'=> 500,
    'freezeTime' => 30*60
  );
  RateController::setKeyPrefix('sl:rc:');
  RateController::setInterval(60);//1 min

  RateController::addPunishmentRule($rule1);
  RateController::addPunishmentRule($rule2);
  RateController::addPunishmentRule($rule3);
?>
