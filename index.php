<?php
session_start();
/* Old version, before factorisation of the project */
/*use controllers\Account;
use controllers\DeleteAccount;
use controllers\Settings;

use controllers\AddOffer;
use controllers\AddOfferConfirm;
use controllers\Register;
use controllers\RegisterConfirm;
use controllers\Main;
use controllers\PasswordForgot;
use controllers\PasswordForgotConfirm;
use controllers\Login;
use controllers\LoginConfirm;
use controllers\Logout;
use controllers\MarketPlace;*/

use controllers\Trade\SeeOffer\DeleteOffer;
use controllers\Trade\SeeOffer\SeeOffer;
use controllers\User\AccountPage\Account;
use controllers\User\AccountPage\DeleteAccount;
use controllers\User\PasswordForgot\PasswordReset;
use controllers\User\PasswordForgot\PasswordResetConfirm;
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
use controllers\Trade\AddOffer\AddOffer;
use controllers\Trade\AddOffer\AddOfferConfirm;
use controllers\Trade\TradeSubjectPoint\TradeSubjectPoint;

use models\DataBase;

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
  new Settings(),
  new MarketPlace(),
  new DeleteAccount(),
  new AddOffer(),
  new AddOfferConfirm(),
  new SeeOffer(),
  new DeleteOffer(),
  new TradeSubjectPoint(),
  new PasswordReset(),
  new PasswordResetConfirm()
];

foreach ($controllers as $controller) {
  if ($controller::resolve($path, $meth)) {
    if (isset($_SESSION['email']) && $_SESSION['logged-in'] === true) {
      DataBase::getInstance()->updateBalance($_SESSION['email']);
    }
    $controller->control();
    exit();
  }
}

echo 'path: ' . $path . ' | meth: ' . $meth . '<br>';
echo '404 NOT FOUND';
exit();

// code externe par clé sans mdp
// how to use google smtp on server (dev account?)
