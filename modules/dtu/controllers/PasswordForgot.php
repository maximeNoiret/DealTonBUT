<?php

namespace controllers;

use controllers\Controller;
use views\User\ForgotPassword\ForgotPasswordView;
//use views\User\ForgotPasswordView;

class PasswordForgot implements Controller
{

  const string PATH = '/user/forgot';
  const string METH = 'GET';
  const string STYLESHEET = DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'style.css';

  function control(): void
  {
    echo (new ForgotPasswordView())->render("Forgot Password - DealTonBUT", self::STYLESHEET);
  }

  static function resolve(string $path, string $meth): bool
  {
    return $path === self::PATH && $meth === self::METH;
  }
}
