<?php
require_once("PwmController.php");
require_once("Motor.php");

class MotorController extends PwmController {
  private $motors=[];

  const FORWARD = 1;
  const REVERSE = 2;
  const RELEASE = 4;
  const BRAKE   = 8;

  function __construct($bus = 1, $chipAddress = 0x60, $debug = False){
    parent::__construct($bus, $chipAddress, $debug);
    $this->setPwmFrequency(1600);
    $this->initMotors();
  }

  private function initMotors() {
   $this->motors = [];
   $this->motors[] = new Motor($this, 1, 8, 10, 9);
   $this->motors[] = new Motor($this, 2, 13, 11, 12);
   $this->motors[] = new Motor($this, 3, 2, 4, 3);
   $this->motors[] = new Motor($this, 4, 7, 5, 6);
  }

  public function getMotor($index, $direction = 0) {
    $motor = $this->motors[$index - 1];
    if ($direction == 1) {
      $temp = $motor->in1;
      $motor->in1 = $motor->in2;
      $motor->in2 = $temp;
    }
    return $motor;
  }

  public function setSpeed($motorNum, $speed) {
    if ($speed < 0) {
      $speed = 0;
    } elseif ($speed > 255) {
      $speed = 255;
    }
    $speed *= 16;
    $this->setPwmOff($this->motors[$motorNum-1]->pwm, $speed);
  }

  public function run($motorNum, $command) {
    $in1 = $this->motors[$motorNum-1]->in1;
    $in2 = $this->motors[$motorNum-1]->in2;
    switch ($command) {
      case self::FORWARD:
        $this->setPin($in1, 1);
        $this->setPin($in2, 0);
        break;
      case self::REVERSE:
        $this->setPin($in1, 0);
        $this->setPin($in2, 1);
        break;
      case self::RELEASE:
        $this->setPin($in1, 0);
        $this->setPin($in2, 0);
        break;
    }
  }

  private function setPin($pin, $value) {
    if ($value == 0)
      $this->setPwm($pin, 0, 4096);
    else
      $this->setPwm($pin, 4096, 0);
  }
}
?>
