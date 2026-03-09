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

  /**
   * @description Check if the user is logged in, if not redirect to the login page.
   * If yes, check if the user is an admin and if the email to delete is in $_POST['remove-account'],
   * if yes delete the account with the email in $_POST['remove-account'].
   * If not redirect to the login page, if the user is not an admin delete the account
   * of the logged-in user and redirect to the login page
   * @return void
   */
  function control(): void {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      header('Location: /user/login');
      exit;
    }

    if (AccountDB::getInstance()->isAdmin($_SESSION['email'])){
      if (isset($_POST['remove-account']) && is_string($_POST['remove-account'])) {
        $email = $_POST['remove-account'];
        if (AccountDB::getInstance()->isAdmin($email)) {
          header('Location: /admin');
          exit;
        }
        AccountDB::getInstance()->deleteUser($email);
        header('Location: /admin');
        exit;
      } else {
        header('Location: /login');
        exit;
      }
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
