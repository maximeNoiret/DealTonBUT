<?php

namespace controllers;

use views\LoginFormView;
class Login
{
  const string PATH = '/user/login';
  const string METH = 'GET';

  const string STYLESHEET = DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'style.css';

  function control(): void
  {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      echo (new LoginFormView())->render("Login - DealTonBUT", self::STYLESHEET);
    } else {
      header('Location: /marketplace');
    }
  }

  static function resolve(string $path, string $meth): bool
  {
    return $path === self::PATH && $meth === self::METH;
  }

}