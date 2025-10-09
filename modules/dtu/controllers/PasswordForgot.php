<?php

namespace controllers;

use controllers\Controller;
use views\ForgotPasswordView;

class PasswordForgot implements Controller
{

  const string PATH = '/user/forgot';
  const string METH = 'GET';

  function control(): void
  {
    echo new ForgotPasswordView()->render();
  }

  static function resolve(string $path, string $meth): bool
  {
    return $path === self::PATH && $meth === self::METH;
  }
}