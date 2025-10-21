<?php

namespace controllers\Trade\MarketPlace;

use core\controllers\Controller;
use views\Trade\MarketPlace\MarketPlaceView;
//use views\MarketPlaceView;

class MarketPlace implements Controller {
  
  private const PATH = '/marketplace';
  private const METH = 'GET';

    const array STYLESHEET = [
        '/_assets/styles/MarketPlace.css',
        '/_assets/styles/style.css',
        '/_assets/styles/navbar.css'
    ];
  
  function control(): void {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      header('Location: /user/login');
    } else {
      echo (new MarketPlaceView())->render("Place de Marché - DealTonBUT", static::STYLESHEET);
    }
  }
  public static function resolve(string $path, string $meth): bool {
    return $path === static::PATH && $meth === static::METH;
  }
}
