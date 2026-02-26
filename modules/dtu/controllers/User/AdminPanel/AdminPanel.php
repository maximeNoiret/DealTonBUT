<?php

namespace controllers\User\AdminPanel;

use dtu\views\User\AdminPanel\AdminPanelView;
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
    '/_assets/styles/offer.css'
  ];

  function control():void {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
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