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
        $res['msg'] = 'url is not set';
      }
      break;
    case 'get':
      if(isset($_GET['token']))
      {
        $token = $_GET['token'];
        $res = (new LinkShortener())->toUrl($token);
      }else
      {
        $res['errno'] = 1;
        $res['msg'] = 'token is undefined';
      }
      break;
    case 'count':
      if(isset($_GET['token']))
      {
        $token = $_GET['token'];
        if(strlen($_GET['token']) <= 10)
        {
          $res = (new LinkShortener())->getTokenViews($token);
        }else{
          $res['errno'] = 1;
          $res['msg'] = 'token.length <= 10';
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
