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
    return 'Le BUTin';
  }
}
