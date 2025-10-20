<?php

namespace controllers;

use controllers\Controller;
use views\MarketPlaceView;

class MarketPlace implements Controller {
  
  private const PATH = '/marketplace';
  private const METH = 'GET';

    const array STYLESHEET = [
        DIRECTORY_SEPARATOR . '_asset' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'MarketPlace.css',
        DIRECTORY_SEPARATOR . '_asset' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'styles.css'
    ];
  
  function control(): void {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      header('Location: /user/login');
    } else {
      echo (new MarketPlaceView())->render("Place de Marché - DealTonBUT", static::STYLESHEET);
    }
  } 
      echo (new MarketPlaceView())->render("Place de Marché - DealTonBUT", static::STYLESHEET);
  } 
  }

  public static function resolve(string $path, string $meth): bool {
    return $path === static::PATH && $meth === static::METH;
  }
}
