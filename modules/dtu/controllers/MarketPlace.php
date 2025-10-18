<?php

namespace controllers;

use controllers\Controller;
use views\MarketPlaceView;

class MarketPlace implements Controller {
  
  private const PATH = '/marketplace';
  private const METH = 'GET';
  private const STYLESHEET = DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'style.css';

  private const STYLESHEET = DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'style.css';
  
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
