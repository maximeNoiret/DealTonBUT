<?php

namespace controllers;

use controllers\Controller;
use views\ForgotPasswordView;
use models\Account;

class PasswordForgotConfirm implements Controller {

  const string PATH = '/user/forgot';
  const string METH = 'POST';

  function control(): void {
    echo new ForgotPasswordView(Account::forgotPassword($_POST['email']))->render();
  }

  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }
}