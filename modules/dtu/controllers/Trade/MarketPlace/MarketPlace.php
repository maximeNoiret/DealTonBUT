<?php

namespace controllers\Trade\MarketPlace;

use core\controllers\Controller;
use models\DataBase;
use views\Trade\MarketPlace\MarketPlaceView;
use views\Trade\Offer\Offer;

//use views\MarketPlaceView;

class MarketPlace implements Controller {
  
  public const PATH = '/marketplace';
  public const METH = 'GET';
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
  
  function control(): void {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      header('Location: /user/login');
    } else {
      echo (new MarketPlaceView())->render("Place de Marché - DealTonBUT", static::STYLESHEET);
    }
  }

  /**
   * @description Show all offers from the database
   * @return string
   */
  public static function getOffers(): string {
    $sort = $_GET['sort'] ?? '';
    switch ($sort) {
      case 'price-asc':
        $offers = DataBase::getInstance()->getOffers('price', 'ASC');
        break;
      case 'price-desc':
        $offers = DataBase::getInstance()->getOffers('price', 'DESC');
        break;
      case 'date':
        $offers = DataBase::getInstance()->getOffers('creation_time', 'DESC');
        break;
      case 'alphabetic':
        $offers = DataBase::getInstance()->getOffers('title', 'ASC');
        break;
      default:
        $offers = DataBase::getInstance()->getOffers('', '');
        break;
    }
    if ($offers) {
      $ret = '<section class="offer-grid">' . "\n";
      foreach ($offers as $offer) {
        /**
         * @var array<string, string> $offer
         */
        $ret = $ret . (new Offer($offer))->render('article', 'offer-card');
      }
      return $ret . '</section>';
    }
    return '<h1 class="description-text">There are no offers!</h1>';
  }
  public static function resolve(string $path, string $meth): bool {
    return $path === static::PATH && $meth === static::METH;
  }
}
