<?php

use controllers\Register;


include __DIR__ . '/_assets/includes/Autoloader.php';


$path = $_SERVER['REQUEST_URI'];
$meth = $_SERVER['REQUEST_METHOD'];


/** @var controllers/Controller[] $controllers */
$controllers = [new Register()];

foreach ($controllers as $controller) {
  if ($controller::resolve($path, $meth)) {
    $controller->control();
    exit();
  }
}

echo '404 NOT FOUND';
exit();
