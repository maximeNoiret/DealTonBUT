<?php

namespace views;

class RegisterFormView extends AbstractView {
  
  const string USERNAME_VALUE='username';
  const string EMAIL_VALUE='email';
  const string PASSWORD_VALUE='password';
  
  function path(): string {
    return __DIR__ . DIRECTORY_SEPARATOR . 'RegisterForm.html';
  }
  function templateValues(): array {
    return [
      'USERNAME_KEY'=>self::USERNAME_VALUE,
      'EMAIL_KEY'=>self::EMAIL_VALUE,
      'PASSWORD_KEY'=>self::PASSWORD_VALUE,
      'ACTION_KEY'=>'idk?'
    ];
  }
}
