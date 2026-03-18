<?php

namespace controllers\User\Settings;

use core\controllers\Controller;
use views\User\SettingsPage\SettingsPageView;
use models\AccountDB;
//use views\SettingsPageView;

class Settings implements Controller
{
  const string PATH = '/user/settings';
  const string METH = 'GET';

    const array STYLESHEET = [
      '/_assets/styles/settings.css',
      '/_assets/styles/style.css',
      '/_assets/styles/navbar.css'
    ];
  function control(): void {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      header('Location: /user/login');
    } else {
      echo (new SettingsPageView())->render('Paramètre - DealTonBUT', self::STYLESHEET);
    }
  }

  /**
   * @throws \Exception
   * @return void
   * @description Deletes the user's account and logs them out.
   */
  function deleteAccount(): void
  {
    $email = '';
    if (isset($_SESSION['email']) && is_string($_SESSION['email'])) {
      $email = $_SESSION['email'];
    }
    AccountDB::getInstance()->deleteUser($email);
    session_destroy();
    header('Location: /user/login');
  }

  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }

  
}
