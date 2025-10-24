<?php

namespace controllers\User\AccountPage;

use models\DataBase;
use views\User\SettingsPage\SettingsPageView;
//use views\SettingsPageView;

class DeleteAccount
{
  const string PATH = '/user/delete-account';
  const string METH = 'POST';

  /**
   * Control the account deletion process.
   * @return void
   */
  function control(): void {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      header('Location: /user/login');
      exit;
    }

    $email = '';
    if (isset($_SESSION['email']) && is_string($_SESSION['email'])) {
      $email = $_SESSION['email'];
    }
    DataBase::getInstance()->deleteUser($email);
    session_destroy();
    header('Location: /user/login');
  }

  /**
   * Resolve the path and method to access the Delete Account functionality.
   * @param string $path
   * @param string $meth
   * @return bool
   */
  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }
}
