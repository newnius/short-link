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
  $rc = new RateController();
  $rc->increaseFatigue(FATIGUE_GET);
  $freezeTime = $rc->getFreezeTime();
  if($freezeTime > 0)
  {
    die('Yoo are too fast, slow down');
  }
  if(isset($_GET['token']))
  {
    $res = (new LinkShortener())->toUrl($_GET['token']);
    if($res['errno'] ==0 ){
      header('Location: '.$res['url']);
    }else
    {
      header('HTTP/1.1 404 Not Found');
      echo 'Url is not exist';
    }
  }else
  {
    header('Location: http://s.newnius.com');
  }

?>
