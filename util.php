<?php
 function cr_require_file($filename)
 {
   if(file_exists($filename))
   {
     require_once($filename);
   }else
   {
     header('HTTP/1.1 500 Internal Server Error');
     exit("File $filename not exist");
   }
 }

  function cr_get_client_ip()
  {
    if(!empty($_SERVER['HTTP_CLIENT_IP']))
    {
      $cip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
      $cip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    elseif(!empty($_SERVER['REMOTE_ADDR']))
    {
      $cip = $_SERVER['REMOTE_ADDR'];
    }
    else
    {
      $cip = '0.0.0.0';
    }
    return $cip;
  }
?>
