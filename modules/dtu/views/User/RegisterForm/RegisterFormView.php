<?php

namespace views\User\RegisterForm;

use core\views\AbstractView;
use mysql_xdevapi\SqlStatementResult;

class   RegisterFormView extends AbstractView {
  /**
   * @var string : Value for a html attribute "for="
   */
  const string USERNAME_VALUE='username';
  /**
   * @var string : Value for a html attribute "for="
   */
  const string EMAIL_VALUE='email';
  /**
   * @var string : Value for a html attribute "for="
   */
  const string PASSWORD_VALUE='password';

  /**
   * @description Constructor of the class LoginFormViews
   * @param string|null $error
   */
  public function __construct(private ?string $error = null )
  {
  }

  /**
   * @description Method that give the path tp the corresponding .html
   * @return string
   */
  function path(): string {
    return __DIR__ . DIRECTORY_SEPARATOR . 'RegisterForm.html';
  }

  /**
   * @description Define value for each keys in the associated .html file
   * @return array<string> : The array that contain the real value that are associated by a key
   */
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

  /**
   * @description Contain the title of the page, that will be shown on the navbar
   * @return string
   */
  function navbarText(): string {
    return 'Inscription';
  }
}
