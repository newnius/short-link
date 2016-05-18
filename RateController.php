<?php
  if(file_exists('util.php'))
  {
    require_once('util.php');
  }else
  {
    die('util functions is required, but file util.php not found');
  }
  cr_require_file('RedisDAO.php');
  
  /*
   * by ip address
   * control user visit rate, prevent them from requesting too frequently
   * normally used in field such as login, register
   * based on fatigue degree
   * 
   * fatigue degree grow algorithm rule, call increaseFatigue($increaseByDegree)
   * punish rule, call addPunishmentRule($arr)
   * format 
   * array(
   *    'minDegree'=>100, //min point to reach this punishment
   *    'freezeTime' => 200  // time before unlock, (s)
   *  )
   * 
   *  rules are stored in redis db, you have to 
   *  will auto grow by fatigue degree, call setAutoIncrease($bool)
   *  punish directlly, call punish($arr)
   */
  class RateController
  {
    private $key = '';
    private $degree = -1;
    private static $keyPrefix = 'rc:';
    private static $punishmentRuleKeys = array('minDegree', 'freezeTime');
    private static $autoIncrease = true;


    public function __construct($key = ''){
      $this->key = $key==''?ip2long(cr_get_client_ip()):$key;
      $this->redis = RedisDAO::getConnection();
    }

    public function increaseFatigue($increaseByDegree)
    {
      if(!is_numeric($increaseByDegree))
      {
        return false;
      }
      $redis = RedisDAO::getConnection();
      $lua_script = <<<LUA
      local degree = redis.call('incrby', KEYS[1], ARGV[1])
      if degree == tonumber(ARGV[1]) then
        redis.call('expire', KEYS[1], ARGV[2]) 
      end
      return degree
LUA;
      if($redis == null)
      {
        return false;
      }
      $this->degree = $redis->eval($lua_script, 1, self::$keyPrefix.'degree:'.$this->key, $increaseByDegree, 60);
      $rule = $this->whichRuleToPunish();
      var_dump($rule);
      if($rule != null)
      {
        $this->punish($rule);
      }
      return true;
    }

    public function punish($punishmentRule){
      if(!RateController::validatePunishmentRule($punishmentRule))
      {
        return false;
      }
      $redis = RedisDAO::getConnection();
      if($redis == null)
      {
        return false;
      }
      $count = $redis->set(self::$keyPrefix.'punishing:'.$this->key, $punishmentRule['freezeTime']);
      echo $count;
      return true;
    }

    public function isFatigued()
    {
      return false;
    }

    public function getFatigueDegree()
    {
      if($this->degree != -1)
      {
        return $this->degree;
      }
      $redis = RedisDAO::getConnection();
      if($redis == null)
      {
        return false;
      }
      $this->degree = $redis->get(self::$keyPrefix.'degree:'.$this->key);
      if($this->degree == null)
      {
        $this->degree = 0;
      }
      return $this->degree;
    }

    public function getFreezeTime()
    {
      $redis = RedisDAO::getConnection();
      if($redis == null)
      {
        return false;
      }
      //$count = $redis->zadd(self::$keyPrefix.'rules' ,$punishmentRule['freezeTime'], $punishmentRule['minDegree']);
      return 0;
    }

    public function whichRuleToPunish()
    {
      $redis = RedisDAO::getConnection();
      if($redis == null)
      {
        return false;
      }
      $rules = $redis->zrevrangebyscore(self::$keyPrefix.'rules', 'inf', $this->degree, 'withscores', 'limit', 0, 1);
      $rule = null;
      if(count($rules == 1))
      {
        $rule['minDegree'] = array_keys($rules)[0];
        $rule['freezeTime'] = array_values($rules)[0];
      }
      return $rule;
    }

    public static function setKeyPrefix($keyPrefix)
    {
      if(!is_string($keyPrefix))
      {
        return false;
      }
      RateController::$keyPrefix = $keyPrefix;
      return true;
    }


    public static function setAutoIncrease($isAutoIncrease){
      if(!is_bool($isAutoIncrease))
      {
        return false;
      }
      RateController::$autoIncrease = $isAutoIncrease;
      return true;
    }

    public static function addPunishmentRule($punishmentRule){
      if(!RateController::validatePunishmentRule($punishmentRule))
      {
        return false;
      }
      $redis = RedisDAO::getConnection();
      if($redis == null)
      {
        return false;
      }
      $count = $redis->zadd(self::$keyPrefix.'rules' ,$punishmentRule['minDegree'], $punishmentRule['freezeTime']);
      return $count == 1;
    }

    public static function removePunishmentRule($punishmentRule)
    {
      if(!RateController::validatePunishmentRule($punishmentRule))
      {
        return false;
      }
      $redis = RedisDAO::getConnection();
      if($redis == null)
      {
        return false;
      }
      $count = $redis->zrem(self::$keyPrefix.'rules', $punishmentRule['minDegree']);
      return count == 1;
    }

    public static function getAllPunishmentRules(){
      $redis = RedisDAO::getConnection();
      if($redis == null)
      {
        return false;
      }
      $rules = $redis->zrange(self::$keyPrefix.'rules', 0 ,-1, 'withscores');
      return $rules;
    }

    public static function cleanPunishmentRules(){
      $redis = RedisDAO::getConnection();
      if($redis == null)
      {
        return false;
      }
      $minDegrees = array_keys(self::getAllPunishmentRules());
      $redis->zrem(self::$keyPrefix.'rules', $minDegrees);
      return true;
    }

    private static function validatePunishmentRule($punishmentRule)
    {
      if(!is_array($punishmentRule))
      {
        return false;
      }
      if(!array_key_exists('minDegree', $punishmentRule) || !is_numeric($punishmentRule['minDegree']))
      {
        return false;
      }
      if(!array_key_exists('freezeTime', $punishmentRule) || !is_numeric($punishmentRule['freezeTime']))
      {
        return false;
      }
      return true;
    }
  
  }

?>
