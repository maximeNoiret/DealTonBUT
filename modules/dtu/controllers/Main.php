<?php

namespace controllers;

use controllers\Controller;
use views\MainPageView;

class Main implements Controller
{

  const string PATH = '';
  const string METH = 'GET';

  function control(): void {
    // TODO: check if logged in and stuff lol
    echo new MainPageView()->render();
  }

  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }
}