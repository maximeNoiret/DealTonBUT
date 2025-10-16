<?php

namespace views;

use models\DataBase;
use views\AbstractView;

class SettingsPageView extends AbstractView
{
  private static self $instance;
  function path(): string
  {
    return __DIR__ . DIRECTORY_SEPARATOR . 'SettingsPage.html';
  }

  function templateValues(): array
  {
    return [];
  }

  function deleteAccount(): void
  {
    DataBase::getInstance()->deleteUser($_SESSION['email']);
    session_destroy();
    header('Location: /user/login');
  }

  public static function getInstance(): self {
    if (!isset(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }
}