<?php

namespace views;

use views\AbstractSubView;

class Offer extends AbstractSubView {
  
  const string PATH = __DIR__ . DIRECTORY_SEPARATOR . 'OfferTemplate.html';

  function __construct(private readonly array $offerInfo) {
  }

  function path(): string {
    return self::PATH;
  }

  function templateValues(): array {
    return $this->offerInfo;
  }

  
}
