<?php

namespace controllers;

use controllers\Controller;
use views\Trade\MarketPlace\MarketPlaceView;
//use views\MarketPlaceView;

class MarketPlace implements Controller {
  
  private const PATH = '/marketplace';
  private const METH = 'GET';

  const string STYLESHEET = DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'Account.css';
  
  function control(): void {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      header('Location: /user/login');
    } else {
      echo (new MarketPlaceView())->render("Place de Marché - DealTonBUT", self::STYLESHEET);
    }
  } 

  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }


}
