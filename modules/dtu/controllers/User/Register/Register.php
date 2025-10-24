<?php

namespace controllers\User\Register;

use core\controllers\Controller;
use views\User\RegisterForm\RegisterFormView;
//use views\User\RegisterFormView;
class Register implements Controller{

  const string PATH = '/user/register';
  const string METH = 'GET';

    const array STYLESHEET = [
        '/_assets/styles/loginSingnin.css',
        '/_assets/styles/style.css',
        '/_assets/styles/navbar.css'
        ];

  /**
   * @return void
   * @description Control the register page view rendering.
   */
  function control(): void {
    echo (new RegisterFormView())->render("Register - DealTonBUT", self::STYLESHEET);
  }

  /**
   * @description Resolve the path and method to access the Register page
   * @param string $path
   * @param string $meth
   * @return bool
   */
  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }
}
