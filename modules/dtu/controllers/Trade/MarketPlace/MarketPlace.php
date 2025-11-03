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
    $sort = $_GET['sort'];
    $query =
      'SELECT u.username as \'username\', title, description, price, deadline
       FROM offer o
       INNER JOIN user_ u
       ON o.owner = u.email ';
    //  for searching a string in the title of the offers
    if (isset($_GET['search-string']) && !empty($_GET['search-string'])) {
      $query .= 'WHERE title LIKE \'%' . $_GET['search-string'] . '%\'';
    }
    switch ($sort) {
      case 'price-asc':
        $query .= " ORDER BY price ASC";
//        $offers = DataBase::getInstance()->getOffers('price', 'ASC');
        break;
      case 'price-desc':
        $query .= " ORDER BY price DESC";
//        $offers = DataBase::getInstance()->getOffers('price', 'DESC');
        break;
      case 'date':
        $query .= " ORDER BY creation_time DESC";
//        $offers = DataBase::getInstance()->getOffers('creation_time', 'DESC');
        break;
      case 'alphabetic':
        $query .= " ORDER BY title ASC";
//        $offers = DataBase::getInstance()->getOffers('title', 'ASC');
        break;
      default:
        break;
    }
    $offers = DataBase::getInstance()->executeQuery($query);
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
    return strtok($path, '?') === static::PATH && $meth === static::METH;
  }
}
