<?php
  if(file_exists('util.php'))
  {
    require_once('util.php');
  }else
  {
    exit("file util.php not exist");
  }

  cr_require_file('config.inc.php');
  cr_require_file('LinkShortener.php');
  cr_require_file('RateController.php');


  $action = '';
  if(isset($_GET['action']))
  {
    $action = $_GET['action'];
  }

  $res['errno'] = 0;
  $res['msg'] = '';
  switch($action)
  {
    case 'set':
      if(isset($_GET['url'])){
        $url = $_GET['url'];
        $rc = new RateController();
        $rc->increaseFatigue(FATIGUE_SET);
        $freezeTime = $rc->getFreezeTime();
        if($freezeTime > 0)
        {
          $res['errno'] = -1;
          $res['msg'] = '你太快了，服务器都跟不上节奏了。';
          break;
        }
        if(isset($_GET['token']) && strlen($_GET['token']) > 0)
        {
          $res = (new LinkShortener())->shortenUrlWithToken($url, $_GET['token']);
        }
        else
        {
          $res = (new LinkShortener())->shortenUrl($url);
        }
      }else
      {
        $res['errno'] = 1;
        $res['msg'] = '网址不能为空';
      }
      break;
    case 'get':
      if(isset($_GET['token']))
      {
        $token = $_GET['token'];
        $rc = new RateController();
        $rc->increaseFatigue(FATIGUE_GET);
        $freezeTime = $rc->getFreezeTime();
        if($freezeTime > 0)
        {
          $res['errno'] = -1;
          $res['msg'] = '你太快了，服务器都跟不上节奏了。';
          break;
        }
        $res = (new LinkShortener())->toUrl($token);
      }else
      {
        $res['errno'] = 1;
        $res['msg'] = '短网址不能为空';
      }
      break;
    case 'count':
      if(isset($_GET['token']))
      {
        $token = $_GET['token'];
        if(strlen($_GET['token']) <= 10)
        {
          $rc = new RateController();
          $rc->increaseFatigue(FATIGUE_ANALYZE);
          $freezeTime = $rc->getFreezeTime();
          if($freezeTime > 0)
          {
            $res['errno'] = -1;
            $res['msg'] = '你太快了，服务器都跟不上节奏了。';
            break;
          }
          $res = (new LinkShortener())->getTokenViews($token);
        }else{
          $res['errno'] = 1;
          $res['msg'] = '短网址长度在5-10';
        }
      }else
      {
        $res['errno'] = 1;
        $res['msg'] = 'token undefined';
      }
      break;
    default:
      $res['errno'] = 1;
      $res['msg'] = 'Unknown command';
      break;
  }
  $json = json_encode($res);
  if(isset($_GET['callback']))
    $json = $_GET['callback'].'('.$json.')';
  else
    $json =  'Void('.$json.')';
  echo $json;
?>
