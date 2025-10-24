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

  /**
   * @return void
   * @description Control the logout process and redirect to login page.
   */
  function control(): void
  {
    // if logged in
    if (isset($_SESSION['logged-in']) && $_SESSION['logged-in'] === true) {
      session_destroy();
    }
    header('Location: /user/login');
  }

  /**
   * @description Resolve the path and method to access the Logout page
   * @param string $path
   * @param string $meth
   * @return bool
   */
  static function resolve(string $path, string $meth): bool
  {
    return $path === self::PATH && $meth === self::METH;
  }

}