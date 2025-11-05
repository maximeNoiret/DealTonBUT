<?php

namespace views\User\SettingsPage;
use core\views\AbstractView;

class SettingsPageView extends AbstractView
{
  /**
   * @description Method that give the path tp the corresponding .html
   * @return string
   */
  function path(): string
  {
    return __DIR__ . DIRECTORY_SEPARATOR . 'SettingsPage.html';
  }

  /**
   * @description Define value for each keys in the associated .html file
   * @return array<string,mixed> : The array that contain the real value that are associated by a key
   */
  public function templateValues(): array
  {
    return [];
  }

  /**
   * @description Contain the title of the page, that will be shown on the navbar
   * @return string
   */
  function navbarText(): string
  {
    return 'Settings';
  }
}