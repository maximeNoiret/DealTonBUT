<?php

namespace controllers;

use controllers\Controller;
use views\MainPageView;

class Main implements Controller
{

  const string PATH = '/';
  const string METH = 'GET';

    const array STYLESHEET = [
        DIRECTORY_SEPARATOR . '_asset' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'loginSingnin.css',
        DIRECTORY_SEPARATOR . '_asset' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'styles.css'
    ];
  function control(): void {
    // TODO: check if logged in and stuff lol
    echo (new MainPageView())->render('DealTonBUT', self::STYLESHEET);
  }

  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }
}
