<?php
session_start();
/* Old version, before factorisation of the project */
/*use controllers\Account;
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
use controllers\MarketPlace;*/
use controllers\User\AccountPage\Account;
use controllers\User\AccountPage\DeleteAccount;
use controllers\User\Settings\Settings;
use controllers\User\Register\Register;
use controllers\User\Register\RegisterConfirm;
use controllers\Main;
use controllers\User\PasswordForgot\PasswordForgot;
use controllers\User\PasswordForgot\PasswordForgotConfirm;
use controllers\User\Login\Login;
use controllers\User\Login\LoginConfirm;
use controllers\User\Login\Logout;
use controllers\Trade\MarketPlace\MarketPlace;

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
