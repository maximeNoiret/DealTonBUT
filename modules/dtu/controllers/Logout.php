<?php

namespace controllers;

class Logout
{
  const string PATH = '/user/logout';
  const string METH = 'GET';

  const string STYLESHEET = DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'style.css';

  function control(): void
  {
    // if logged in
    if (isset($_SESSION['logged-in']) && $_SESSION['logged-in'] === true) {
      session_destroy();
    }
    header('Location: /user/login');
  }

  static function resolve(string $path, string $meth): bool
  {
    return $path === self::PATH && $meth === self::METH;
  }

}