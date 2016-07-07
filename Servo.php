<?php
require_once("ServoController.php");

class Servo {
  private $servoController;
  private $servoNumber;

  function __construct($servoController, $servoNumber){
    $this->servoController = $servoController;
    $this->servoNumber = $servoNumber;
  }

  public function setPosition($position) {
    $this->servoController->setPosition($this->servoNumber, $position);
  }

  public function setDegree($degrees) {
    $this->servoController->setDegree($this->servoNumber, $degrees);
  }

  public function setAbs($position) {
    $this->servoController->setPositionAbs($this->servoNumber, $position);
  }

  public function getPosition() {
    return $this->servoController->getPosition($this->servoNumber);
  }

  public function getDegree() {
    return $this->servoController->getDegree($this->servoNumber);
  }
  
  public function getAbs() {
    return $this->servoController->getAbs($this->servoNumber);
  }
}
?>
