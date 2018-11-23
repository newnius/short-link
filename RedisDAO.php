<?php
  if(file_exists('util.php'))
  {
    require_once('util.php');
  }else
  {
    die('util functions is required, but file util.php not found');
  }
  cr_require_file('./predis/autoload.php');


class RedisDAO
{
  private static $scheme = 'tcp';
  private static $host = '127.0.0.1';
  private static $port = 6379;

  public static function config($configuration)
  {
    
  }

  public static function getConnection()
  {
    try{
      $redis = new Predis\Client(array(
        'scheme' => RedisDAO::$scheme,
        'host'   => RedisDAO::$host,
        'port'   => RedisDAO::$port
      ));
      $redis->connect();
      return $redis;
    } catch (Exception $e){
      //echo "Error: {$e->getMessage()}";
      return null;
    }
  }


}
?>
