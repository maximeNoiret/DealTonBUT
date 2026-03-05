<?php

namespace controllers\User\AdminPanel;

use dtu\views\User\AdminPanel\AdminPanelView;
use models\AccountDB;
use views\User\AccountPage\AccountPageView;
use views\User\LoginForm\LoginFormView;

class AdminPanel
{
  const string PATH = '/admin';

  const string METH = 'GET';

  const array STYLESHEET = [
    '/_assets/styles/Account.css',
    '/_assets/styles/style.css',
    '/_assets/styles/navbar.css',
    '/_assets/styles/offer.css',
    '/_assets/styles/adminPanel.css'
  ];

  function control():void {
    // NOTE : the moment when the role of the user is written is in the LoginConfirm controller
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true || !AccountDB::getInstance()->isAdmin($_SESSION['email'])) {
      echo (new LoginFormView())->render("Login - DealTonBUT", self::STYLESHEET);
    } else {
      echo (new AdminPanelView())->render("Account - DealTonBUT", self::STYLESHEET);
    }
  }

  static function resolve(string $path, string $meth): bool
  {
    return $path === self::PATH && $meth === self::METH;
  }
}