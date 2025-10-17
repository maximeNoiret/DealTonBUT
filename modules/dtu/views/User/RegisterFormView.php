<?php

namespace views\User;

use views\AbstractView;

class   RegisterFormView extends AbstractView {

  const string USERNAME_VALUE='username';
  const string EMAIL_VALUE='email';
  const string PASSWORD_VALUE='password';

  public function __construct(private ?string $error = null )
  {
  }


  function path(): string {
    return __DIR__ . DIRECTORY_SEPARATOR . 'RegisterForm.html';
  }
  function templateValues(): array {
    $values = [
      'USERNAME_KEY'=>self::USERNAME_VALUE,
      'EMAIL_KEY'=>self::EMAIL_VALUE,
      'PASSWORD_KEY'=>self::PASSWORD_VALUE,
      'ACTION_KEY'=>'/user/register'
    ];
    if ($this->error !== null) {
      $errorMessage = match($this->error) {
        'account_already_exists' => 'An account with this email already exists.',
        'database_error' => 'A database error occurred. Please try again.',
        default => 'An unknown error occurred.'
      };
      $values['ERROR_MESSAGE'] = $errorMessage;
    } else {
      $values['ERROR_MESSAGE'] = '';
    }

    return $values;
  }
}
