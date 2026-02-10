<?php
namespace controllers\Trade\SeeOtherAccount;
use core\controllers\Controller;
use views\Trade\SeeOtherAccount\SeeOtherAccountView;
use views\User\AccountPage\AccountPageView;
use views\User\LoginForm\LoginFormView;

class SeeOtherAccount implements Controller
{
  private $email;
  Const string PATH = '/account/see';
  Const string METH = 'GET';

  const array STYLESHEET = [
    '/_assets/styles/Account.css',
    '/_assets/styles/style.css',
    '/_assets/styles/navbar.css',
    '/_assets/styles/offer.css',
  ];

  static function resolve(string $path, string $meth): bool{
    return strtok($path, '?') === static::PATH && $meth === static::METH;
  }

  function control(): void
  {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      echo (new LoginFormView())->render("Login - DealTonBUT", self::STYLESHEET);
    } else {
      if ($_GET['email'] == $_SESSION['email']) {
        echo (new AccountPageView())->render("Account - DealTonBUT", self::STYLESHEET);
        return;
      }
      echo (new SeeOtherAccountView())->render("Account - DealTonBUT", self::STYLESHEET);
    }
  }
}