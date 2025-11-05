<?php

namespace views\Trade\MarketPlace;

use controllers\Trade\MarketPlace\MarketPlace;
use core\views\AbstractView;

class MarketPlaceView extends AbstractView {
  /**
   * @description Method that give the path tp the corresponding .html
   * @return string
   */
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
        $ret = $ret . (new Offer($offer))->renderWithLink('article', 'offer-card', '/offre/voir?id=' . $offer['ouid']);
      }
      return $ret . '</section>';
    }
    return '<h1 class="description-text">There are no offers!</h1>';
  }

  /**
   * @description define value for each keys in the associated .html file
   * @return string[]
   */
  function templateValues(): array {
    $values = [
      'OFFERS' => MarketPlace::getOffers()
    ];
    return $values;
  }

  /**
   * @description Contain the title of the page, that will be shown on the navbar
   * @return string
   */
  function navbarText(): string {
    return 'Place De Marché';
  }
}
