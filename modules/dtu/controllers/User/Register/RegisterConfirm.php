<?php

namespace controllers\User\Register;
use core\controllers\Controller;
use exceptions\AccountAlreadyExists;
use models\Account;
use views\User\RegisterForm\RegisterFormView;
//use views\User\RegisterFormView;

class RegisterConfirm implements Controller {
  
  const string PATH = '/user/register';
  const string METH = 'POST';
  const string STYLESHEET = DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'style.css';

  /**
   */
  function control(): void {
    $account = new Account();
    try {
      $account->registerAccount(
        $_POST['username'],
        $_POST['email'],
        $_POST['password']);
    } catch (AccountAlreadyExists $e) {
      echo (new RegisterFormView('account_already_exists'))->render("Register - DealTonBUT", self::STYLESHEET);
    }
    // At this point, account has been created.
    session_regenerate_id(true);
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['logged-in'] = true;
    header('Location: /marketplace');
  }

  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }
}
