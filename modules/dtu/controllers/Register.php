<?php

namespace controllers;

use views\User\RegisterFormView;
class Register implements Controller{

  const string PATH = '/user/register';
  const string METH = 'GET';

    const array STYLESHEET = [
        DIRECTORY_SEPARATOR . '_asset' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'loginSingin.css',
        DIRECTORY_SEPARATOR . '_asset' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'styles.css'
    ];
  function control(): void {
    echo (new RegisterFormView())->render("Register - DealTonBUT", self::STYLESHEET);
  }

  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }
}
