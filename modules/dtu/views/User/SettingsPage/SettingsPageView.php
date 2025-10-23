<?php

namespace views\User\SettingsPage;

use models\DataBase;
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

  function deleteAccount(): void
  {
    $email = '';
    if (isset($_SESSION['email']) && is_string($_SESSION['email'])) {
      $email = $_SESSION['email'];
    }
    DataBase::getInstance()->deleteUser($email);
    session_destroy();
    header('Location: /user/login');
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