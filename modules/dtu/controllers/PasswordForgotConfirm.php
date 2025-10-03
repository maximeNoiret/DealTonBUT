<?php

namespace controllers;

use controllers\Controller;
use views\ForgotPasswordView;

class PasswordForgotConfirm implements Controller
{

  const string PATH = '/user/forgot';
  const string METH = 'POST';

  function control(): void
  {
    echo new ForgotPasswordView('message')->render();
  }

  static function resolve(string $path, string $meth): bool
  {
    return $path === self::PATH && $meth === self::METH;
  }
}