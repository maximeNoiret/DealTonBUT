<?php

namespace views;

use views\AbstractView;

class ForgotPasswordView extends AbstractView
{

  const string EMAIL_VALUE = 'email';

  function path(): string {
    return __DIR__ . DIRECTORY_SEPARATOR . 'ForgotPassword.html';
  }

  function templateValues(): array
  {
    $values = [
      'EMAIL_KEY' => self::EMAIL_VALUE
    ];     // TODO: add values if needed
    return $values;
  }
}