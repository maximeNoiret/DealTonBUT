<?php

namespace controllers;

use views\RegisterFormView;
class Register implements Controller{

  const string path = '/user/register';
  const string meth = 'GET';
  
  function bonjour(): void {
    echo 'Hello World!';
  }

  function control(): void {
    echo new RegisterFormView()->render();
  }

  static function resolve(string $path, string $meth): bool {
    return $path === self::path && $meth === self::meth;
  }
}
