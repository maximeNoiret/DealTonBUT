<?php

namespace views;

use views\AbstractSubView;

class Offer extends AbstractSubView {
  
  const string PATH = 'OfferTemplate.html';

  function __construct(private readonly array $ownerInfo) {
  }

  function path(): string {
    return self::PATH;
  }

  function templateValues(): array {
    return $this->ownerInfo;
  }

  
}
