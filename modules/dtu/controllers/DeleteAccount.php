<?php

namespace controllers;

use views\SettingsPageView;

class DeleteAccount
{
  const string PATH = '/user/delete-account';
  const string METH = 'POST';

  function control(): void {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      header('Location: /user/login');
      exit;
    }

    SettingsPageView::getInstance()->deleteAccount();
  }

  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }
}
