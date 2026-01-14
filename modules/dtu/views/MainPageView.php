<?php

namespace views;

use core\views\AbstractView;

class MainPageView extends AbstractView
{

  /**
   * @description Method that give the path tp the corresponding .html
   * @return string
   */
  function path(): string {
    return __DIR__ . DIRECTORY_SEPARATOR . 'MainPage.html';
  }

  /**
   * @description Replace keys value by their real value in the associated .html file
   * @return string[]
   */
  function templateValues(): array {
   $values = [
      'REGISTER_LINK' => '/user/register',
      'LOGIN_LINK' => '/user/login'
    ];
    return $values;
  }

  /**
   * @description Contain the title of the page, that will be shown on the navbar
   * @return string
   */
  function navbarText(): string {
    return '';
  }

  /**
   * @description Toggle that show the navbar
   * @return bool
   */
  public function showNavbar(): bool {
    return false;
  }
}
