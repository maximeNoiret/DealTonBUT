<?php

namespace controllers\User\Register;

use core\controllers\Controller;
use dtu\views\User\RegisterForm\RegisterFormPasswordView;
use exceptions\AccountAlreadyExists;
use models\AccountDB;

class RegisterVerifyConfirm implements Controller
{
  const string PATH = '/user/register/verify';
  const string METH = 'POST';

  function control(): void
  {
    $db = AccountDB::getInstance();
    $tempAccount = ['username' => $_SESSION['username'], 'email' => $_SESSION['email']];
    session_regenerate_id(true);
    /**
     * @var array<string, string> $_POST
     * @var array<string, string> $_SESSION
     * @var array<string, string> $tempAccount
     */
      if (str_contains($tempAccount['email'], '@univ-amu.fr')) {
          $db->setRole($tempAccount['email'], 'teacher');
      } else {
          $db->setRole($tempAccount['email'], 'student');
      }
    $error = $this->validatePassword($_POST['password'] ?? '');
    if ($error !== null) {
        echo (new RegisterFormPasswordView(null, $error))->render("Register - DealTonBUT", Register::STYLESHEET);
        return;
    }
    $hashedPassword = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);
    $db->updatePassword($tempAccount['email'], $hashedPassword);
    $_SESSION['username'] = $tempAccount['username'];
    $_SESSION['email'] = $tempAccount['email'];
    $_SESSION['logged-in'] = true;
    $_SESSION['first-login'] = true;
    $_SESSION['role'] = $db->getRole($tempAccount['email']);

    header('Location: /marketplace');
  }

    private function validatePassword(string $password): ?string
    {
        if (strlen($password) < 12) {
            return 'Le mot de passe doit contenir au moins 12 caractères.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return 'Le mot de passe doit contenir au moins une majuscule.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            return 'Le mot de passe doit contenir au moins un chiffre.';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return 'Le mot de passe doit contenir au moins un caractère spécial.';
        }
        return null;
    }

  static function resolve(string $path, string $meth): bool
  {
    return $path === self::PATH && $meth === self::METH;
  }
}