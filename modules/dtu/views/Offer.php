<?php

namespace views;

use views\AbstractSubView;

class Offer extends AbstractSubView {
  
  const string PATH = 'OfferTemplate.html';

  function __construct(array $ownerInfo) {
    $this->info = $ownerInfo;
  }

  function path() {
    return self::PATH;
  }

  function templateValues() {
    return $this->info;
  }

  
}
