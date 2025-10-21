<?php

namespace controllers;

use controllers\Controller;
use views\User\ForgotPasswordView;

class PasswordForgot implements Controller
{

  const string PATH = '/user/forgot';
  const string METH = 'GET';
    const array STYLESHEET = [
        '/_assets/styles/loginSingnin.css',
        '/_assets/styles/style.css'
    ];

  function control(): void
  {
    echo (new ForgotPasswordView())->render("Forgot Password - DealTonBUT", self::STYLESHEET);
  }

  static function resolve(string $path, string $meth): bool
  {
    return $path === self::PATH && $meth === self::METH;
  }
}
