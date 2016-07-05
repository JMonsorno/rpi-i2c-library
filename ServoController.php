<?php
require_once("PwmController.php");

class ServoController extends PwmController {

  function __construct($bus = 1, $chipAddress = 0x40, $debug = False){
    parent::__construct($bus, $chipAddress, $debug);
    $this->setPwmFrequency(60);
  }

  public function disableServos() {
    $this->turnOffServos();
  }

  public function turnOffServos() {
    if ($this->debug) {
      echo "--Turning off all servos" . PHP_EOL;
    }
    $this->setAllPwm(0,0);
  }

  public function setServoPositionAbs($index, $position) {
    if ($this->debug) {
      echo "--Setting servo $index to $position" . PHP_EOL;
    }
    $this->setPwmOff($index, $position);
  }

  /*
   * $index    0-15
   * $position 0-450
   */
  public function setServoPosition($index, $position) {
    $this->setServoPositionAbs($index, $position+150);
  }

  public function setServoPositionDegree($index, $position) {
    $this->setServoPosition($index, $position * 2.5);
  }

  public function getServoPosition($index) {
    $loRegister = self::LED0_OFF_L + 4 * $index;
    $hiRegister = self::LED0_OFF_H + 4 * $index;
    return ($this->readU8($loRegister) + ($this->readU8($hiRegister) << 8)) - 150;
  }
}
?>
