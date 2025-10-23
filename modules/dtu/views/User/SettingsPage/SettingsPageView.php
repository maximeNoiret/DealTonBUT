<?php

namespace views\User\SettingsPage;
use core\views\AbstractView;

class SettingsPageView extends AbstractView
{
  private static self $instance;
  function path(): string
  {
    return __DIR__ . DIRECTORY_SEPARATOR . 'SettingsPage.html';
  }

  /**
   * @return array<string,mixed>
   */
  public function templateValues(): array
  {
    return [];
  }

  public static function getInstance(): self {
    if (!isset(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  function navbarText(): string
  {
    return 'Settings';
  }
}