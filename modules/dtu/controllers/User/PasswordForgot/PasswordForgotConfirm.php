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
    DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'style.css',
    DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'navbar.css',
    DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'loginSingnin.css'
  ];

  function control(): void {
    /**
     * @var array<string, string> $_POST
     */
    echo (new ForgotPasswordView(Account::forgotPassword($_POST['email'])))->render('Forgot Password - DealTonBUT', self::STYLESHEET);
  }

  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }
}
