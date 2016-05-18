<?php
  if(file_exists('util.php'))
  {
    require_once('util.php');
  }else
  {
    exit("file util.php not exist");
  }

  cr_require_file('config.inc.php');
  cr_require_file('Random.php');
  cr_require_file('MysqlPDO.php');

  class LinkShortener
  {
    public function shortenUrl($url)
    {
      $msg['errno'] = 0;
      $msg['msg'] = '';
      if($url == null || strlen($url) < URL_MIN_LENGTH 
        || strlen($url) > URL_MAX_LENGTH)
      {
        $msg['errno'] = -1;
        $msg['msg'] = '网址长度在'.URL_MIN_LENGTH.'-'.URL_MAX_LENGTH;
        return $msg;
      }
      $count = 0;
      while($count ==0 )
      {
        $token = (new Random())->randomString(5);
        $sql = 'INSERT INTO `short_links` (`url`, `token`, `time`, `ip`) VALUES(?, ?, ?, ?)';
        $params = array($url, $token, time(), ip2long(cr_get_client_ip()));
        $count = (new MysqlPDO())->execute($sql, $params);
      }
      $msg['token'] = $token;
      return $msg;
    }

    public function toUrl($token)
    {
      $msg['errno'] = 0;
      $msg['msg'] = '';
      if($token==null || strlen($token) < TOKEN_MIN_LENGTH 
        || strlen($token) > TOKEN_MAX_LENGTH)
      {
        $msg['errno'] = -1;
        $msg['msg'] = '网址不存在';
        return $msg;
      }
      $sql = 'SELECT `url` from `short_links` WHERE `token` = ?';
      $params = array($token);
      $urls = (new MysqlPDO())->executeQuery($sql, $params);
      if(count($urls) > 0)
      {
        $msg['url'] = $urls[0]['url'];
        return $msg;
      }else
      {
        $msg['errno'] = -1;
        $msg['msg'] = '网址不存在';
        return $msg;
      }
    }

    public function shortenUrlWithToken($url, $token)
    {
      $msg['errno'] = 0;
      $msg['msg'] = '';
      if($url == null || strlen($url) < URL_MIN_LENGTH 
        || strlen($url) > URL_MAX_LENGTH)
      {
        $msg['errno'] = -1;
        $msg['msg'] = '网址长度在'.URL_MIN_LENGTH.'-'.URL_MAX_LENGTH;
        return $msg;
      }
      if($token==null || strlen($token) < TOKEN_MIN_LENGTH 
        || strlen($token) > TOKEN_MAX_LENGTH)
      {
        $msg['errno'] = -1;
        $msg['msg'] = '自定义串长度在'.TOKEN_MIN_LENGTH.'-'.TOKEN_MAX_LENGTH;
        return $msg;
      }
      $sql = 'INSERT INTO `short_links` (`url`, `token`, `time`, `ip`) VALUES(?, ?, ?, ?)';
      $params = array($url, $token, time(), ip2long(cr_get_client_ip()));
      MysqlPDO::enableDebug();
      $count = (new MysqlPDO())->execute($sql, $params);
      if($count==1)
      {
        $msg['token'] = $token;
      }else
      {
        $msg['errno'] = -1;
        $msg['msg'] = '该短网址已被占用';
      }
      return $msg;
    }

    public function getTokenViews($token)
    {
      $msg['errno'] = -1;
      $msg['msg'] = '暂不支持';
      return $msg;
    }
  }

?>
