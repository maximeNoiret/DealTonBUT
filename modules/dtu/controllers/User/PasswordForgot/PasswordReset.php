<?php

namespace controllers\User\PasswordForgot;

use controllers\User\Login\Login;
use core\controllers\Controller;
use models\AccountDB;
use views\User\ForgotPassword\PasswordResetView;
use views\User\LoginForm\LoginFormView;

class PasswordReset implements Controller
{

  const string PATH = '/user/validate';
  const string METH = 'GET';

  function control(): void {
    // Someone goes to /user/validate with GET method
    // - if (email, token) in 'token' && ttl not reached: ask new password
    /**
     * @var array<string, string> $_GET
     */
    if (AccountDB::getInstance()->checkToken($_GET['email'] ?? '', $_GET['token'] ?? '')) {
      $_SESSION['reset_email'] = $_GET['email'];
      echo (new PasswordResetView()->render('Reset Password - DealTonBUT', Login::STYLESHEET));
    } else {
      echo (new LoginFormView('reset_link_expired')->render('Login - DealTonBUT', Login::STYLESHEET));
    }
    // - else: display "invalid link" and quit
  }

  static function resolve(string $path, string $meth): bool {
    return strtok($path, '?') === self::PATH && $meth === self::METH;
  }
}