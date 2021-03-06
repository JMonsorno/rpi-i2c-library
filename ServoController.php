<?php
require_once("PwmController.php");
require_once("Servo.php");

class ServoController extends PwmController {

  function __construct($bus = 1, $chipAddress = 0x40, $debug = False){
    parent::__construct($bus, $chipAddress, $debug);
    $this->setPwmFrequency(60);
  }

  public function shutdown() {
    if ($this->debug) {
      echo "Shutting down ServoController" . PHP_EOL;
    }
    $this->disableServos();
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

  public function getServo($index) {
    return new Servo($this, $index);
  }

  /*
   * $index    0-15
   * $position 150-600
   */
  public function setPositionAbs($index, $position) {
    if ($this->debug) {
      echo "--Setting servo $index to $position" . PHP_EOL;
    }
    $this->setPwmOff($index, $position);
  }

  /*
   * $index    0-15
   * $position 0-450
   */
  public function setPosition($index, $position) {
    $this->setPositionAbs($index, $position+150);
  }

  /*
   * $index    0-15
   * $position 0-180
   */
  public function setDegree($index, $position) {
    $this->setPosition($index, $position * 2.5);
  }

  public function getPositionAbs($index) {
    $loRegister = self::LED0_OFF_L + 4 * $index;
    $hiRegister = self::LED0_OFF_H + 4 * $index;
    return ($this->readU8($loRegister) + ($this->readU8($hiRegister) << 8));
  }

  public function getPosition($index) {
    return $this->getPositionAbs($index) - 150;
  }

  public function getDegree($index) {
    return  (int)($this->getPosition($index) / 2.5);
  }
}
?>
