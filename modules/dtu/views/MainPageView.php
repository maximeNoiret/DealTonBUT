<?php

namespace views;

use views\AbstractView;

class MainPageView extends AbstractView
{

  function path(): string {
    return __DIR__ . DIRECTORY_SEPARATOR . 'MainPage.html';
  }

  function templateValues(): array {
   $values = [
      'REGISTER_LINK' => '/user/register',
      'LOGIN_LINK' => '/user/login'  
    ];
    return $values;
  }
}
