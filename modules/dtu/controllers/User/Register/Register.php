<?php

namespace controllers\User\Register;

use core\controllers\Controller;
use Exception;
use views\User\RegisterForm\RegisterFormView;

class Register implements Controller{

  const string PATH = '/user/register';
  const string METH = 'GET';

    const array STYLESHEET = [
        '/_assets/styles/loginSingnin.css',
        '/_assets/styles/style.css',
        '/_assets/styles/navbar.css'
        ];

    /**
     * @throws Exception
     */
    function control(): void {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      echo new RegisterFormView()->render("Register - DealTonBUT", self::STYLESHEET);
    } else {
      header('Location: /marketplace');
    }
  }

  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }
}
