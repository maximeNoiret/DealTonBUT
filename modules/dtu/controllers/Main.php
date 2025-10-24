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

  /**
   * @return void
   * @description Control the main page view rendering.
   */
  function control(): void {
    echo (new MainPageView())->render('DealTonBUT', self::STYLESHEET);

  }

  /**
   * @param string $path
   * @param string $meth
   * @return bool
   * @description Resolve the path and method to access the Main page
   */
  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }
}
