<?php

namespace views\Trade\MarketPlace;

use core\views\AbstractView;
use views\Trade\Offer\Offer;
use models\DataBase;

class MarketPlaceView extends AbstractView {
  function path(): string {
    return __DIR__ . DIRECTORY_SEPARATOR . 'MarketPlace.html';
  }

  function getOffers(): string {
    $offers = DataBase::getInstance()->getOffers();
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

  function templateValues(): array {
    $values = [
      'OFFERS' => $this->getOffers()
    ];
    return $values;
  }

  function navbarText(): string {
    return 'Place De Marché';
  }
}
