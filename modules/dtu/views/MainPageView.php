<?php

namespace views;

use views\AbstractView;

class MainPageView extends AbstractView
{

  function path(): string {
    return __DIR__ . DIRECTORY_SEPARATOR . 'MainPage.html';
  }

  function templateValues(): array {
    // TODO: Implement templateValues() method.
    $values = [];
    return $values;
  }
}