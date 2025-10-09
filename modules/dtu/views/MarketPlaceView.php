<?php

namespace views;

use views\AbstractView;

class MarketPlaceView extends AbstractView {
  
   
   
  
  
  function path(): string {
    return __DIR__ . DIRECTORY_SEPARATOR . 'MarketPlace.html';
  }

  function templateValues(): array {
    $values = [
      'USERNAME' => $_SESSION['username']
    ];
    return values; // PS: this will be hard af to do lmao :3
  } 
}
