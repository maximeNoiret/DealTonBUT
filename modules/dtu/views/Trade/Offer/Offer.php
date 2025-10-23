<?php

namespace views\Trade\Offer;

use core\views\AbstractSubView;

class Offer extends AbstractSubView {
  
  const string PATH = __DIR__ . DIRECTORY_SEPARATOR . 'OfferTemplate.html';

  /**
   * @param array<string, string> $offerInfo
   */
  function __construct(private readonly array $offerInfo) {
  }

  function path(): string {
    return self::PATH;
  }

  /**
   * @return array<string, string>
   */
  function templateValues(): array {
    return $this->offerInfo;
  }


  function navbarText(): string
  {
    return '';
  }
}
