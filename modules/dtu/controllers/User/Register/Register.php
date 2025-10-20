<?php

namespace controllers\User\Register;

use core\controllers\Controller;
use views\User\RegisterForm\RegisterFormView;
//use views\User\RegisterFormView;
class Register implements Controller{

  const string PATH = '/user/register';
  const string METH = 'GET';

  const string STYLESHEET = DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'style.css';

  function control(): void {
    echo (new RegisterFormView())->render("Register - DealTonBUT", self::STYLESHEET);
  }

  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }
}
