<?php
require_once("ServoController.php");
require_once("MotorController.php");
require_once("Thread.php");
$servoController = new ServoController(1, 0x40);
$motorController = new MotorController(1, 0x60);
$controllers = [$servoController, $motorController];
exec("/usr/bin/gpio export 4 in");

function shut_down_in($delay){
  run_for($delay, 'main');
}

function run_for($delay, $function_name){
  global $controllers;
  $tShutdown = new Thread('sleep');
  $tRun = new Thread($function_name);

  $tShutdown->start($delay);
  echo "Shutting down in $delay seconds" . PHP_EOL;
  $tRun->start();
  while($tShutdown->isAlive()) {
    sleep(1);
  }
  $tRun->stop();
  echo "Program Shutdown, shutting down controllers" . PHP_EOL;
  alloff();
  foreach($controllers as $controller) {
    $controller->shutdown();
  }
}

function wait_for_light($cds_port, $light_port) {
  //calibrate
	setServoPosition($light_port, 450);
	while(digital($cds_port));
  $i = 0;
  while(!digital($cds_port)) {
		msleep(500);
		setServoPosition($light_port, $i*450);
		$i = 1 - $i;
	}
	
  while(digital($cds_port)) {
		msleep(250);
		setServoPosition($light_port, $i*450);
		$i = 1 - $i;		
	}
}

function msleep($ms) {
  usleep($ms * 1000);
}

function set_digital_read($pin) {
	exec ("/usr/bin/gpio export $pin in");
}

function digital($pin) {
	return trim(file_get_contents("/sys/class/gpio/gpio$pin/value")) == '1';
}

//Servos
function disable_servo($srv) {
  global $servoController;
  $servoController->setPositionAbs($srv, 0);
}

function getServoPosition($srv) {
  global $servoController;
  $servoController->getPosition($srv);
}

function setServoPosition($srv, $p) {
  global $servoController;
  if ($p > 450) { $p = 450; }
  elseif ($p < 0) { $p = 0; }
  $servoController->setPosition($srv, $p);
}


//Motors
function alloff() {
  for($i=1; $i<=4; ++$i)
    off($i);
}

function ao() { alloff(); }

function mav($m, $vel) { move_at_velocity($m, $vel); }

function move_at_velocity($m, $vel) {
  global $motorController;
  if($vel > 255) { $vel = 255; }
  elseif($vel<-255) { $vel = -255; }
  $motorController->setSpeed($m, abs($vel));
  if($vel > 0) { $motorController->run($m, MotorController::FORWARD); }
  elseif($vel < 0) { $motorController->run($m, MotorController::REVERSE); }
  else { $motorController->run($m, MotorController::RELEASE); }
}

function off($m) {
  move_at_velocity($m, 0);
}
?>
