<?php

namespace controllers\User\Settings;

use views\User\SettingsPage\SettingsPageView;
//use views\SettingsPageView;

class Settings
{
  const string PATH = '/user/settings';
  const string METH = 'GET';

    const array STYLESHEET = [
      '/_assets/styles/settings.css',
      '/_assets/styles/style.css'
    ];
  function control(): void {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      header('Location: /user/login');
    } else {
      echo (new SettingsPageView())->render('Paramètre - DealTonBUT', self::STYLESHEET);
    }
  }

  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }

  
}
