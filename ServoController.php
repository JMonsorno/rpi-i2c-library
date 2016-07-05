<?php
require_once("I2C.php");

class ServoController extends I2C {
  // Registers/etc.
  const MODE1              = 0x00;
  const MODE2              = 0x01;
  const SUBADR1            = 0x02;
  const SUBADR2            = 0x03;
  const SUBADR3            = 0x04;
  const PRESCALE           = 0xFE;
  const LED0_ON_L          = 0x06;
  const LED0_ON_H          = 0x07;
  const LED0_OFF_L         = 0x08;
  const LED0_OFF_H         = 0x09;
  const ALL_LED_ON_L       = 0xFA;
  const ALL_LED_ON_H       = 0xFB;
  const ALL_LED_OFF_L      = 0xFC;
  const ALL_LED_OFF_H      = 0xFD;

  // Bits
  const RESTART            = 0x80;
  const SLEEP              = 0x10;
  const ALLCALL            = 0x01;
  const INVRT              = 0x10;
  const OUTDRV             = 0x04;

  function __construct($bus = 1, $chipAddress = 0x40, $debug = False){
    $this->bus = $bus;
    $this->chipAddress = $chipAddress;
    $this->debug = $debug;
    $this->initializeChip();
    $this->setFrequency();
  }

  private function initializeChip() {
    if ($this->debug) {
      echo "--Begin Initialize" . PHP_EOL;
    }

    $this->turnOffServos();

    if ($this->debug) {
      echo "--Reseting PCA9685 MODE1 (without SLEEP) and MODE2" . PHP_EOL;
    }

    $this->write8(self::MODE2, self::OUTDRV);
    $this->write8(self::MODE1, self::ALLCALL);
    usleep(5000);
    $mode1 = $this->readU8(self::MODE1);
    $mode1 = $mode1 & ~self::SLEEP;
    $this->write8(self::MODE1, $mode1);
    if ($this->debug) {
      echo "--End Initialize" . PHP_EOL;
    }
  }

  private function setFrequency() {
    if ($this->debug) {
      echo "--Begin Set Frequency" . PHP_EOL;
    }
    usleep(5000);
    $oldmode = $this->readU8(self::MODE1);
    $newmode = ($oldmode & 0x7F) | 0x10;
    $this->write8(self::MODE1, $newmode);
    $this->write8(self::PRESCALE, 0x65); //60Hz
    $this->write8(self::MODE1, $oldmode);
    $this->write8(self::MODE1, $oldmode | 0x80);
    if ($this->debug) {
      echo "--End Set Frequency" . PHP_EOL;
    }
  }

  public function disableServos() {
    $this->turnOffServos();
  }

  public function turnOffServos() {
    if ($this->debug) {
      echo "--Turning off all servos" . PHP_EOL;
    }
    $this->write8(self::ALL_LED_ON_L, 0x00);
    $this->write8(self::ALL_LED_ON_H, 0x00);
    $this->write8(self::ALL_LED_OFF_L, 0x00);
    $this->write8(self::ALL_LED_OFF_H, 0x00);
  }

  /*
   * $index    0-15
   * $position 0-450
   */
  public function setServoPositionAbs($index, $position) {
    if ($this->debug) {
      echo "--Setting servo $index to $position" . PHP_EOL;
    }
    $loRegister = self::LED0_OFF_L + 4 * $index;
    $hiRegister = self::LED0_OFF_H + 4 * $index;
    $this->write8($loRegister, $position & 0xFF);
    $this->write8($hiRegister, $position >> 8);
  }

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
