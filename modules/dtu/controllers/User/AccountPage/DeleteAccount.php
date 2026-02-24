<?php

namespace controllers\User\AccountPage;

use core\controllers\Controller;
use models\AccountDB;
use views\User\SettingsPage\SettingsPageView;
//use views\SettingsPageView;

class DeleteAccount implements Controller
{
  const string PATH = '/user/delete-account';
  const string METH = 'POST';

  function control(): void {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      header('Location: /user/login');
      exit;
    }

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
