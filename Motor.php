<?php
require_once("MotorController.php");

class Motor {
  private $motorController;
  private $motorNumber;
  public $pwm;
  public $in1;
  public $in2;
  public $command = 0;

  function __construct($motorController, $motorNumber, $pwm, $in1, $in2){
    $this->motorController = $motorController;
    $this->motorNumber = $motorNumber;
    $this->pwm = $pwm;
    $this->in1 = $in1;
    $this->in2 = $in2;
  }

  public function setSpeed($speed) {
    $this->motorController->setSpeed($this->motorNumber, $speed);
  }

  public function run($command) {
    $this->motorController->run($this->motorNumber, $command);
  }
}
?>
