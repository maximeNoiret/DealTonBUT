<?php

namespace views;

use views\AbstractView;

class MarketPlaceView extends AbstractView {
  
   
   
  
  
  abstract function path(): string {
    return __DIR__ . DIRECTORY_SEPARATOR . 'MarketPlace.html';
  }

  abstract function templateValues(): array {
    $values = [
      'USERNAME' => $_SESSION['username']
    ];
    return values; // PS: this will be hard af to do lmao :3
  } 
}
