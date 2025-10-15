<?php

namespace views;

use views\AbstractView;
use models\DataBase;  // WARN: maybe find a way to not access a model in a view?

class MarketPlaceView extends AbstractView {
  
  function path(): string {
    return __DIR__ . DIRECTORY_SEPARATOR . 'MarketPlace.html';
  }

  function getOffers(): string {
    $offers = DataBase::getInstance()->getOffers();
    if ($offers) {
      $ret = '<section class="offers-grid">' . "\n";
      foreach ($offers as $offer) {
        $ret = $ret . new Offer($offer)->render('article', 'offer');
      }
      $ret = $ret . '</section>';
      return $ret;
    }
    return '<h1 class="description-text">There are no offers!</h1>';
  }

  function templateValues(): array {

    $values = [
      'USERNAME' => $_SESSION['username'],
      'OFFERS' => $this->getOffers()
    ];
    return $values; // PS: this will be hard af to do lmao :3
  } 
}
