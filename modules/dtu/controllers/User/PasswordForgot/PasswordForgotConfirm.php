<?php

namespace controllers\User\PasswordForgot;

use core\controllers\Controller;
use views\User\ForgotPassword\ForgotPasswordView;
//use views\User\ForgotPasswordView;
use models\Account;

class PasswordForgotConfirm implements Controller {

  const string PATH = '/user/forgot';
  const string METH = 'POST';
  const array STYLESHEET = [
    DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'style.css'
  ];

  /**
   * @return void
   * @description Control the forgot password confirmation process.
   */
  function control(): void {
    /**
     * @var array<string, string> $_POST
     */
    echo (new ForgotPasswordView(Account::forgotPassword($_POST['email'])))->render('Forgot Password - DealTonBUT', self::STYLESHEET);
  }

  /**
   * @description Resolve the path and method to access the Forgot Password confirmation
   * @param string $path
   * @param string $meth
   * @return bool
   */
  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }
}
