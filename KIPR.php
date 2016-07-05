<?php
require_once("ServoController.php");
require_once("MotorController.php");
require_once("Thread.php");

class KIPR {
  function shutdown_in($delay, $controllers){
    $tShutdown = new Thread('sleep');
    $tMain = new Thread('main');

    $tShutdown->start($delay);
    echo "Shutting down in $delay seconds" . PHP_EOL;
    $tMain->start();
    while($tShutdown->isAlive()) {
      sleep(1);
    }
    $tMain->stop();
    echo "Program Shutdown, shutting down controllers" . PHP_EOL;
    foreach($controllers as $controller) {
      $controller->shutdown();
    }
  }

}
?>
