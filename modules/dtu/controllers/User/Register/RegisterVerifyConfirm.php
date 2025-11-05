<?php

namespace controllers\User\Register;

use core\controllers\Controller;
use exceptions\AccountAlreadyExists;
use models\DataBase;

class RegisterVerifyConfirm implements Controller
{
  const string PATH = '/user/register/verify';
  const string METH = 'POST';

  /**
   * @throws AccountAlreadyExists
   */
  function control(): void
  {
    $db = DataBase::getInstance();
    $db->registerAccount(
      $_SESSION['username'],
      $_SESSION['email'],
      $_POST['password']);
    $tempAccount = ['username' => $_SESSION['username'], 'email' => $_SESSION['email']];
    session_regenerate_id(true);
    $_SESSION['username'] = $tempAccount['username'];
    $_SESSION['email'] = $tempAccount['email'];
    $_SESSION['logged-in'] = true;
    header('Location: /marketplace');
  }

  static function resolve(string $path, string $meth): bool
  {
    return $path === self::PATH && $meth === self::METH;
  }
}