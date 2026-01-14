<?php

namespace controllers\User\PasswordForgot;

use controllers\User\Login\Login;
use core\controllers\Controller;
use models\AccountDB;
use views\User\ForgotPassword\PasswordResetView;
use views\User\LoginForm\LoginFormView;

class PasswordResetConfirm implements Controller
{

  const PATH = '/user/validate';
  const METH = 'POST';

  function control(): void {
    /**
     * @var array<string, string> $_POST
     */
    $hashedPassword = password_hash($_POST['new_password'] ?? '', PASSWORD_BCRYPT);

    /**
     * @var array<string, string> $_SESSION
     */
    if (AccountDB::getInstance()->updatePassword($_SESSION['reset_email'] ?? '', $hashedPassword)) {
      echo (new LoginFormView('password_changed')->render('Login - DealTonBUT', Login::STYLESHEET));
    } else {
      echo (new LoginFormView('unknownerroroccured')->render('Login - DealTonBUT', Login::STYLESHEET));
    }
    // - else: display "invalid link" and quit
  }

  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }
}