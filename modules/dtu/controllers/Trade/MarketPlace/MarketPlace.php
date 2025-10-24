<?php

namespace controllers\Trade\MarketPlace;

use core\controllers\Controller;
use views\Trade\MarketPlace\MarketPlaceView;
//use views\MarketPlaceView;

class MarketPlace implements Controller {
  
  public const string PATH = '/marketplace';
  public const
  string METH = 'GET';
  /**
   * @description Store all the different stylesheet used
   * @var array<string> STYLESHEET
   */
    const array STYLESHEET = [
        '/_assets/styles/MarketPlace.css',
        '/_assets/styles/style.css',
        '/_assets/styles/navbar.css',
        '/_assets/styles/offer.css'
    ];
  /**
   * @description Control the access to the MarketPlace page
   * @return void
   */
  function control(): void {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      header('Location: /user/login');
    } else {
      echo (new MarketPlaceView())->render("Place de Marché - DealTonBUT", static::STYLESHEET);
    }
  }

  /**
   * @description Resolve the path and method to access the MarketPlace page
   * @param string $path
   * @param string $meth
   * @return bool
   */
  public static function resolve(string $path, string $meth): bool {
    return $path === static::PATH && $meth === static::METH;
  }
}
