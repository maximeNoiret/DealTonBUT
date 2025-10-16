<?php
session_start();

use controllers\Account;
use controllers\DeleteAccount;
use controllers\Settings;
use controllers\Register;
use controllers\RegisterConfirm;
use controllers\Main;
use controllers\PasswordForgot;
use controllers\PasswordForgotConfirm;
use controllers\Login;
use controllers\LoginConfirm;
use controllers\Logout;
use controllers\MarketPlace;


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
  new LoginConfirm(),
  new Logout(),
  new MarketPlace(),
  new Account(),
  new Settings()
];

foreach ($controllers as $controller) {
  if ($controller::resolve($path, $meth)) {
    $controller->control();
    exit();
  }
}

// Ajoutez cette ligne avec les autres résolutions de contrôleurs
if (DeleteAccount::resolve($path, $meth)) {
  (new DeleteAccount())->control();
  exit;
}

echo 'path: ' . $path . ' | meth: ' . $meth . '<br>';
echo '404 NOT FOUND';
exit();
