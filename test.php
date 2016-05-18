<?php
  require_once('RateController.php');

  $rule1 = array(
    'minDegree'=> 100,
    'freezeTime' => '10'
  );
  $rule2 = array(
    'minDegree'=> 50,
    'freezeTime' => '30'
  );
  $rule3 = array(
    'minDegree'=> 200,
    'freezeTime' => '20'
  );

  var_dump((new RateController())->increaseFatigue(5));
  var_dump((new RateController())->getFatigueDegree());

  //var_dump((new RateController())->punish($rule1));

  //var_dump((new RateController())->setKeyPrefix('sl:rc:'));

  //var_dump((new RateController())->setAutoIncrease(false));

  //var_dump((new RateController())->addPunishmentRule($rule1));
  //var_dump((new RateController())->addPunishmentRule($rule2));
  //var_dump((new RateController())->addPunishmentRule($rule3));

  //var_dump((new RateController())->removePunishmentRule($punishmentRule));
  var_dump((new RateController())->getAllPunishmentRules());
  //var_dump((new RateController())->cleanPunishmentRules());
  //var_dump((new RateController())->isFatigued());
  //var_dump((new RateController())->getFatigueDegree());
  //var_dump((new RateController())->getFreezeTime());
?>
