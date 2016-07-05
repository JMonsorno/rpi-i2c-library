<?php

abstract class I2C {
  protected $bus;
  protected $chipAddress;
  protected $debug = False;

  protected function write8($dataAddress, $value) {
    $hexChipAddress = $this->dec2hex($this->chipAddress);
    $hexDataAddress = $this->dec2hex($dataAddress);
    $hexValue = $this->dec2hex($value);
    exec("i2cset -y $this->bus $hexChipAddress $hexDataAddress $hexValue");
    if ($this->debug) {
      echo "I2C: Wrote $hexValue to register $hexDataAddress" . PHP_EOL;
    }
  }

  protected function readU8($dataAddress) {
    $hexChipAddress = $this->dec2hex($this->chipAddress);
    $hexDataAddress = $this->dec2hex($dataAddress);
    $result = exec("i2cget -y $this->bus $hexChipAddress $hexDataAddress");
    if ($this->debug) {
      echo "I2C: Device $hexChipAddress return $result from reg $hexDataAddress" . PHP_EOL;
    }
    return hexdec($result);
  }

  public function dec2hex($dec) {
    return "0x" . str_pad(strtoupper(dechex($dec)), 2, "0", STR_PAD_LEFT);
  }
}
?>
