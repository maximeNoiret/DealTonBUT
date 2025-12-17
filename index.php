<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors',"On");

use controllers\Forbidden\Env;
use controllers\Main;
use controllers\Trade\AddOffer\AddOffer;
use controllers\Trade\AddOffer\AddOfferConfirm;
use controllers\Trade\DeleteOffer\DeleteOffer;
use controllers\Trade\MarketPlace\MarketPlace;
use controllers\Trade\SeeOffer\SeeOffer;
use controllers\Trade\TradeSubjectPoint\TradeSubjectPoint;
use controllers\User\AccountPage\Account;
use controllers\User\AccountPage\DeleteAccount;
use controllers\User\Login\Login;
use controllers\User\Login\LoginConfirm;
use controllers\User\Login\Logout;
use controllers\User\PasswordForgot\PasswordForgot;
use controllers\User\PasswordForgot\PasswordForgotConfirm;
use controllers\User\PasswordForgot\PasswordReset;
use controllers\User\PasswordForgot\PasswordResetConfirm;
use controllers\User\Register\RegisterVerify;
use controllers\User\Register\RegisterVerifyConfirm;
use controllers\User\Settings\Settings;
use controllers\User\Register\Register;
use controllers\User\Register\RegisterConfirm;
use dtu\views\Forbidden;
use models\DataBase;

include __DIR__ . '/_assets/includes/Autoloader.php';


$path = $_SERVER['REQUEST_URI'];
$meth = $_SERVER['REQUEST_METHOD'];

if (preg_match('/^\/(\.env|\.git|\.htaccess|composer\.(json|lock)|\.php)/', $path)) {
  http_response_code(403);
  echo new Forbidden()->render('Forbidden - DealTonBUT', Main::STYLESHEET);
  exit();
}

$file = __DIR__ . $path;
if (is_file($file)) {
  return false; // Let PHP's built-in server handle it with correct MIME type
}


/** @var $controllers /Controller[] $controllers */
$controllers = [
  new Register(),
  new RegisterConfirm(),
  new RegisterVerify(),
  new RegisterVerifyConfirm(),
  new Main(),
  new PasswordForgot(),
  new PasswordForgotConfirm(),
  new Login(),
  new LoginConfirm(),
  new Logout(),
  new MarketPlace(),
  new Account(),
  new Settings(),
  new MarketPlace(),
  new DeleteAccount(),
  new AddOffer(),
  new AddOfferConfirm(),
  new SeeOffer(),
  new DeleteOffer(),
  new TradeSubjectPoint(),
  new PasswordReset(),
  new PasswordResetConfirm(),

  // Forbidden
  new Env()
];

foreach ($controllers as $controller) {
  if ($controller::resolve($path, $meth)) {
    if (isset($_SESSION['email']) && isset($_SESSION['logged-in']) && $_SESSION['logged-in'] === true) {
      DataBase::getInstance()->updateBalance($_SESSION['email']);
    }
    $controller->control();
    exit();
  }
}
http_response_code(404);
echo 'path: ' . $path . ' | meth: ' . $meth . '<br>';
echo '404 NOT FOUND';
exit();

// code externe par clé sans mdp
// how to use google smtp on server (dev account?)
