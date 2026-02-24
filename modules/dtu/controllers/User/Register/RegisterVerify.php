<?php

namespace controllers\User\Register;

use core\controllers\Controller;
use controllers\User\Register\Register;
use core\models\Mailer;
use dtu\views\User\RegisterForm\RegisterFormPasswordView;
use exceptions\AccountAlreadyExists;
use models\Account;
use models\AccountDB;
use Random\RandomException;
use views\User\RegisterForm\RegisterFormView;

class RegisterVerify implements Controller
{

  const string PATH = '/user/register/verify';
  const string METH = 'GET';
  const array STYLESHEET = Register::STYLESHEET;

  function control(): void
  {
    $db = AccountDB::getInstance();
    /**
     * @var array<string, string> $_GET
     */
    $_SESSION['email'] = $db->getEmailFromToken($_GET['token']);

    /**
     * @var array<string, string> $_GET
     */
    if ($db->checkToken($_SESSION['email'], $_GET['token'])) {
      /**
       * @var array<string, string> $_SESSION
       */
      echo (new RegisterFormPasswordView($_SESSION['email']))->render("Register - DealTonBUT", self::STYLESHEET);
    } else {
      echo (new RegisterFormView('verification_link_expired'))->render("Register - DealTonBUT", self::STYLESHEET);
    }
  }

  static function resolve(string $path, string $meth): bool
  {
    return strtok($path, '?') === self::PATH && $meth === self::METH;
  }
}