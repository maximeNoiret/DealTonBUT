<?php

namespace controllers\User\Login;

use views\User\LoginForm\LoginFormView;
//use views\User\LoginFormView;
class Login
{
  const string PATH = '/user/login';
  const string METH = 'GET';

    const array STYLESHEET = [
        '/_assets/styles/loginSingnin.css',
        '/_assets/styles/style.css',
        '/_assets/styles/navbar.css'
        ];

  /**
   * @return void
   * @description Control the login page view rendering based on user login status.
   */
  function control(): void
  {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      echo (new LoginFormView())->render("Login - DealTonBUT", self::STYLESHEET);
    } else {
      header('Location: /marketplace');
    }
  }

  /**
   * @description Resolve the path and method to access the Login page
   * @param string $path
   * @param string $meth
   * @return bool
   */
  static function resolve(string $path, string $meth): bool
  {
    return $path === self::PATH && $meth === self::METH;
  }

}