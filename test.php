<?php
  require_once('RateController.php');

  $rule1 = array(
    'minDegree'=> 50,
    'freezeTime' => 6
  );
  $rule2 = array(
    'minDegree'=> 100,
    'freezeTime' => 5*6
  );
  $rule3 = array(
    'minDegree'=> 200,
    'freezeTime' => 30*6
  );


  var_dump(RateController::setKeyPrefix('sl:rc:'));
  var_dump(RateController::setInterval(5*60));//5 min
  var_dump(RateController::setAutoIncrease(false));

  var_dump(RateController::addPunishmentRule($rule1));
  var_dump(RateController::addPunishmentRule($rule2));
  var_dump(RateController::addPunishmentRule($rule3));
  //var_dump(RateController::removePunishmentRule($rule2));
  //var_dump(RateController::getAllPunishmentRules());
  //var_dump(RateController::cleanPunishmentRules());
  var_dump(RateController::getAllPunishmentRules());

  $key = 'uid';
  $rc = new RateController($key);

  var_dump($rc->increaseFatigue(5));
  //var_dump($rc->punish($rule1));
  var_dump($rc->getFatigueDegree());
  var_dump($rc->isFatigued());
  var_dump($rc->getFreezeTime());
?>
