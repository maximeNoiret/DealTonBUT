<?php

namespace controllers;
use controllers\Controller;
use exceptions\AccountAlreadyExists;
use models\Account;

class RegisterConfirm implements Controller {
  
  const string PATH = '/user/register';
  const string METH = 'POST';

  /**
   */
  function control(): void {
    $account = new Account();
    try {
      $account->registerAccount(
        $_POST['username'],
        $_POST['email'],
        $_POST['password']);
    } catch (AccountAlreadyExists $e) {
      echo $e->getMessage();
    }
  }

  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }
}
