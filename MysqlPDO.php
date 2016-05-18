<?php
class MysqlPDO
{
  private $dbh;
  private static $isDebug = false;

  public static function enableDebug(){
    MysqlPDO::$isDebug = true;
  }

  public function MysqlPDO()
  {
    $this->connect();
  }

  private function connect()
  {
    try {
      $this->dbh = new PDO('mysql:host='.DB_HOST.';charset=utf8;port='.DB_PORT.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
      return true;
    } catch (PDOException $e) {
      $this->dbh = null;
      if(MysqlPDO::$isDebug)
      {
        echo $e->getMessage();
      }
      return false;
    }
  }

  public function execute($sql, $a_params)
  {
    if($this->dbh == null){
      return 0;
    }
    $stmt = $this->dbh->prepare($sql);
    $stmt->execute($a_params);
    $affected_rows = $stmt->rowCount();
    $this->dbh = null;
    return $affected_rows;
  }


  function executeQuery($sql, $a_params)
  {
    if($this->dbh == null){
      return array();
    }
    $stmt = $this->dbh->prepare($sql);
    $result = null;
    if($stmt->execute($a_params)){
      $result = $stmt->fetchAll();
    }
    $this->dbh = null;
    return $result;
  }
}
