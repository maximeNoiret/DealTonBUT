<?php

namespace controllers;

use controllers\Controller;
use views\MarketPlaceView;

class MarketPlace implements Controller {
  
  private const PATH = '/marketplace';
  private const METH = 'GET';

<<<<<<< HEAD
  const string STYLESHEET = DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'Account.css';
=======
  private const STYLESHEET = DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'style.css';
>>>>>>> 7bc9c5c (Ajout de la navbar)
  
  function control(): void {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      header('Location: /user/login');
    } else {
<<<<<<< HEAD
      echo (new MarketPlaceView())->render("Place de Marché - DealTonBUT", self::STYLESHEET);
    }
=======
      echo (new MarketPlaceView())->render("Place de Marché - DealTonBUT", static::STYLESHEET);
>>>>>>> 7bc9c5c (Ajout de la navbar)
  } 
  }

  public static function resolve(string $path, string $meth): bool {
    return $path === static::PATH && $meth === static::METH;
  }


}
