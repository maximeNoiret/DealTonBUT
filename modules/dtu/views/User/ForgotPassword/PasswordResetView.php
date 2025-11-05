<?php

namespace views\User\ForgotPassword;

use core\views\AbstractView;

class PasswordResetView extends AbstractView {

  public function __construct()
  {
  }

  function path(): string
  {
    return __DIR__ . DIRECTORY_SEPARATOR . 'PasswordReset.html';
  }

  function templateValues(): array
  {
    $values = [
      'NEWPASS_KEY' => 'new_password'
    ];
    return $values;
  }

  function navbarText(): string
  {
    return 'Réinitialisation du mot de passe';
  }
}