<?php
require_once("I2C.php");

abstract class PwmController extends I2C {
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
  }

  private function initializeChip() {
    if ($this->debug) {
      echo "--Begin Initialize" . PHP_EOL;
    }

    $this->setAllPwm(0,0);

    if ($this->debug) {
      echo "--Reseting PCA9685 MODE1 (without SLEEP) and MODE2" . PHP_EOL;
    }

    $this->write8(self::MODE2, self::OUTDRV);
    $this->write8(self::MODE1, self::ALLCALL);
    usleep(5000);
    $mode1 = $this->readU8(self::MODE1);
    $mode1 = $mode1 & ~self::SLEEP;
    $this->write8(self::MODE1, $mode1);
    usleep(5000);
    if ($this->debug) {
      echo "--End Initialize" . PHP_EOL;
    }
  }

  protected function setPwmFrequency($freq) {
    if ($this->debug) {
      echo "--Begin Set Frequency" . PHP_EOL;
    }
    $preScaleVal = 25000000.0; //25MHz
    $preScaleVal /= 4096.0;    //12-bit
    $preScaleVal /= $freq;
    $preScaleVal -= 1.0;
    $preScale = (int)($preScaleVal + .5);

    if ($this->debug) {
      echo "Setting PWM frequency to $freq Hz" . PHP_EOL;
      echo "Estimate pre-scale: $preScaleVal" . PHP_EOL;
      echo "Final pre-scale: $preScale" . PHP_EOL;
    }

    $oldmode = $this->readU8(self::MODE1);
    $newmode = ($oldmode & 0x7F) | 0x10;
    $this->write8(self::MODE1, $newmode);
    $this->write8(self::PRESCALE, $preScale);
    $this->write8(self::MODE1, $oldmode);
    usleep(5000);
    $this->write8(self::MODE1, $oldmode | 0x80);
    if ($this->debug) {
      echo "--End Set Frequency" . PHP_EOL;
    }
  }

  protected function setPwm($channel, $on, $off) {
    $this->setPwmOn($channel, $on);
    $this->setPwmOff($channel, $off);
  }

  protected function setPwmOn($channel, $on) {
    $this->write8(self::ALL_LED_ON_L+4*$channel, $on & 0xFF);
    $this->write8(self::ALL_LED_ON_H+4*$channel, $on >> 8);
  }

  protected function setPwmOff($channel, $off) {
    $this->write8(self::ALL_LED_OFF_L+4*$channel, $off & 0xFF);
    $this->write8(self::ALL_LED_OFF_H+4*$channel, $off >> 8);
  }

  protected function setAllPwm($on, $off) {
    $this->write8(self::ALL_LED_ON_L, $on & 0xFF);
    $this->write8(self::ALL_LED_ON_H, $on >> 8);
    $this->write8(self::ALL_LED_OFF_L, $off & 0xFF);
    $this->write8(self::ALL_LED_OFF_H, $off >> 8);
  }
}
?>
