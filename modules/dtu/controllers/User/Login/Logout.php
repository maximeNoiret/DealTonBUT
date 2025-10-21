<?php

namespace controllers\User\Login;

class Logout
{
  const string PATH = '/user/logout';
  const string METH = 'GET';

    const array STYLESHEET = [
        '/_assets/styles/loginSingnin.css',
        '/_assets/styles/style.css',
        '/_assets/styles/navbar.css'
    ];
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