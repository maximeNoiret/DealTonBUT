<?php

namespace controllers\User\Register;

use core\controllers\Controller;
use exceptions\AccountAlreadyExists;
use models\DataBase;

class RegisterVerifyConfirm implements Controller
{
  const string PATH = '/user/register/verify';
  const string METH = 'POST';

  function control(): void
  {
    $db = DataBase::getInstance();
    $tempAccount = ['username' => $_SESSION['username'], 'email' => $_SESSION['email']];
    session_regenerate_id(true);
    $db->setRole($tempAccount['email'], 'student');  // TODO: separate teachers and students from email format
    $hashedPassword = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);
    $db->updatePassword($tempAccount['email'], $hashedPassword);
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