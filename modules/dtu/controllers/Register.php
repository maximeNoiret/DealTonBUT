<?php

namespace controllers;

use views\RegisterFormView;
class Register implements Controller{
  function bonjour(): void {
    echo 'Hello World!';
  }

  function control(): void {
    echo new RegisterFormView()->render();
  }

  static function resolve(string $path): bool {
    return $path === '/user/register';
  }
}
