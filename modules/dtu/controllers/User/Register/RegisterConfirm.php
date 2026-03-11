<?php

namespace controllers\User\Register;
use core\controllers\Controller;
use exceptions\AccountAlreadyExists;
use models\Account;
use Random\RandomException;
use views\User\RegisterForm\RegisterFormView;
//use views\User\RegisterFormView;

class RegisterConfirm implements Controller {
  
  const string PATH = '/user/register';
  const string METH = 'POST';
  const array STYLESHEET = Register::STYLESHEET;

  /**
   * @throws RandomException
   */
  function control(): void {


  try {
    /**
     * @var array<string, string> $_POST
     */
    echo new RegisterFormView(Account::registerAccount($_POST['username'], $_POST['email']))->render("Register - DealTonBUT", self::STYLESHEET);
  } catch (AccountAlreadyExists $e) {
    echo new RegisterFormView('account_already_exists')->render("Register - DealTonBUT", self::STYLESHEET);
  }
  }

  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }
}
