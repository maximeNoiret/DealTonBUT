<?php

use controllers\Register;
use controllers\RegisterConfirm;
use controllers\Main;
use controllers\PasswordForgot;
use controllers\PasswordForgotConfirm;
use controllers\Login;
use controllers\LoginConfirm;


include __DIR__ . '/_assets/includes/Autoloader.php';


$path = $_SERVER['REQUEST_URI'];
$meth = $_SERVER['REQUEST_METHOD'];


/** @var $controllers /Controller[] $controllers */
$controllers = [
  new Register(),
  new RegisterConfirm(),
  new Main(),
  new PasswordForgot(),
  new PasswordForgotConfirm(),
  new Login(),
  new LoginConfirm()
];

foreach ($controllers as $controller) {
  if ($controller::resolve($path, $meth)) {
    $controller->control();
    exit();
  }
}
echo 'path: ' . $path . ' | meth: ' . $meth . '<br>';
echo '404 NOT FOUND';
exit();
