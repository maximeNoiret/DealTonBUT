<?php

namespace controllers;

use views\RegisterFormView;
class Register implements Controller{

  const string PATH = '/user/register';
  const string METH = 'GET';

  function control(): void {
    echo new RegisterFormView()->render();
  }

  static function resolve(string $path, string $meth): bool {
    return $path === explode("&", self::PATH)[0] && $meth === self::METH;
  }
}
