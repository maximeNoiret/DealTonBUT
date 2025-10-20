<?php

namespace controllers;

use core\controllers\Controller;
use views\MainPageView;

class Main implements Controller
{

  const string PATH = '/';
  const string METH = 'GET';

  const string STYLESHEET = DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'style.css';

  function control(): void {
    // TODO: check if logged in and stuff lol
    echo (new MainPageView())->render('DealTonBUT', self::STYLESHEET);
  }

  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }
}
