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
    private static $interval = 60;
    private static $keyPrefix = 'rc:';
    private static $punishmentRuleKeys = array('minDegree', 'freezeTime');
    private static $autoIncrease = true;


    /*
     * @param $key customize your own key, default is ip2long(ip)
     */
    public function __construct($key = ''){
      $this->key = $key==''?ip2long(cr_get_client_ip()):$key;
      $this->redis = RedisDAO::getConnection();
    }

    /*
     * @param
     *
     */
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
      $this->degree = $redis->eval($lua_script, 1, self::$keyPrefix.'degree:'.$this->key, $increaseByDegree, self::$interval);
      $rule = $this->whichRuleToPunish();
      if($rule != null)
      {
        $this->punish($rule);
      }
      return true;
    }

    /*
     *
     * punish directly
     */
    public function punish($punishmentRule){
      if(!self::validatePunishmentRule($punishmentRule))
      {
        return false;
      }
      $redis = RedisDAO::getConnection();
      if($redis == null)
      {
        return false;
      }
      $lua_script = <<<LUA
      local minDegree = redis.call('get', KEYS[1])
      if(tonumber(minDegree) == tonumber(ARGV[1])) then
        return 0
      else
        redis.call('set', KEYS[1], ARGV[1])
        redis.call('expire', KEYS[1], ARGV[2]) 
      end
      return 1
LUA;
      $count = $redis->eval($lua_script, 1, self::$keyPrefix.'punishing:'.$this->key, $punishmentRule['minDegree'], $punishmentRule['freezeTime']);
      return $count == 1;
    }

    /*
     * judge if is fatigued/being punished
     * note: if you call getFreezeTime() later, do not call this, as it will increase one request
     */
    public function isFatigued()
    {
      return $this->getFreezeTime() > 0;
    }

    /*
     * get current fatigue degree
     */
    public function getFatigueDegree()
    {
      if($this->degree != -1)
      {
        return $this->degree;
      }
      $redis = RedisDAO::getConnection();
      if($redis == null)
      {
        return 0;
      }
      $this->degree = (int)$redis->get(self::$keyPrefix.'degree:'.$this->key);
      if($this->degree == null)
      {
        $this->degree = 0;
      }
      return $this->degree;
    }

    /*
     *
     * get punish time left, negative means not being punished
     *
     */
    public function getFreezeTime()
    {
      $redis = RedisDAO::getConnection();
      if($redis == null)
      {
        return -1;
      }
      $freezeTime = (int)$redis->ttl(self::$keyPrefix.'punishing:'.$this->key);
      return $freezeTime;
    }

    /*
     * get which rule to punish current user
     * mostly of the time, you dont have to call this, as it is called automatically
     */
    public function whichRuleToPunish()
    {
      $redis = RedisDAO::getConnection();
      if($redis == null)
      {
        return null;
      }
      $rules = $redis->zrevrangebyscore(self::$keyPrefix.'rules', $this->degree, 0,'withscores', 'limit', 0, 1);
      $rule = null;
      if(count($rules) == 1)
      {
        $rule['minDegree'] = array_values($rules)[0];
        $rule['freezeTime'] = array_keys($rules)[0];
      }
      return $rule;
    }


    /*
     * set counting interval, dafault is 60(s)
     */
    public static function setInterval($interval)
    {
      if(!is_int($interval) || $interval < 0)
      {
        return false;
      }
      self::$interval = $interval;
      return true;
    }

    /*
     * set key prefix stored in redis, change this to if you have multi apps using RateController module to avlid conflicts
     */
    public static function setKeyPrefix($keyPrefix)
    {
      if(!is_string($keyPrefix))
      {
        return false;
      }
      self::$keyPrefix = $keyPrefix;
      return true;
    }


    /*
     * auto increase fatigue when in punishment status
     * not support yet
     */
    public static function setAutoIncrease($isAutoIncrease){
      if(!is_bool($isAutoIncrease))
      {
        return false;
      }
      self::$autoIncrease = $isAutoIncrease;
      return true;
    }

    /*
     * add an punishment rule
     * rules will be stored in db, call once only
     */
    public static function addPunishmentRule($punishmentRule){
      if(!self::validatePunishmentRule($punishmentRule))
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

    /*
     * remove an rulementRule
     */
    public static function removePunishmentRule($punishmentRule)
    {
      if(!self::validatePunishmentRule($punishmentRule))
      {
        return false;
      }
      $redis = RedisDAO::getConnection();
      if($redis == null)
      {
        return false;
      }
      $count = $redis->zrem(self::$keyPrefix.'rules', $punishmentRule['freezeTime']);
      return $count == 1;
    }
    
    /*
     * get all rules
     */
    public static function getAllPunishmentRules(){
      $redis = RedisDAO::getConnection();
      if($redis == null)
      {
        return null;
      }
      $rules = $redis->zrange(self::$keyPrefix.'rules', 0 ,-1, 'withscores');
      $readableRules = array();
      foreach($rules as $freezeTime => $minDegree)
      {
        $readableRules[] = array('minDegree'=>(int)$minDegree, 'freezeTime'=>$freezeTime);
      }
      return $readableRules;
    }

    /*
     * remove all rules
     * 
     */
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

    /*
     * check if punishmentRule is in right format
     */
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
