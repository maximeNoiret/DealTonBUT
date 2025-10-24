<?php

namespace controllers\User\Settings;

use core\controllers\Controller;
use views\User\SettingsPage\SettingsPageView;
use models\DataBase;
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

  /**
   * @return void
   * @description Control the settings page view rendering.
   */
  function control(): void {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      header('Location: /user/login');
    } else {
      echo (new SettingsPageView())->render('Paramètre - DealTonBUT', self::STYLESHEET);
    }
  }

  /**
   * @description Resolve the path and method to access the Settings page
   * @param string $path
   * @param string $meth
   * @return bool
   */
  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }

  
}
