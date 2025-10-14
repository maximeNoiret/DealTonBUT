<?php

namespace views;

use views\AbstractSubView;

class OffersGrid extends AbstractSubView {
  
  const string PATH = 'OffersGrid.html';

  function path() {
    return self::PATH;
  }

  function templateValues() {
    return $this->info;
  }

  
}
