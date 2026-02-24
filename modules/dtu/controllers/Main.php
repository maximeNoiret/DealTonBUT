<?php

namespace controllers;

use core\controllers\Controller;
use views\MainPageView;

class Main implements Controller
{

  const string PATH = '/';
  const string METH = 'GET';

    const array STYLESHEET = [
        '/_assets/styles/loginSingnin.css',
        '/_assets/styles/style.css'
    ];
  function control(): void {
    echo (new MainPageView())->render('DealTonBUT', self::STYLESHEET);

  }

  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }
}
