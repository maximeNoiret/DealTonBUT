<?php

namespace dtu\views\User\RegisterForm;

use core\views\AbstractView;

class RegisterFormPasswordView extends AbstractView {
  /**
   * @var string : Value for a html attribute "for="
   */
  const string PASSWORD_VALUE='password';

  /**
   * @description Constructor of the class LoginFormViews
   * @param string|null $email
   * @param string|null $error
   */
  public function __construct(private ?string $email = null, private ?string $error = null )
  {
  }

  /**
   * @description Method that give the path tp the corresponding .html
   * @return string
   */
  function path(): string {
    return __DIR__ . DIRECTORY_SEPARATOR . 'RegisterFormPassword.html';
  }

  /**
   * @description Define value for each keys in the associated .html file
   * @return array<string, string|null> : The array that contain the real value that are associated by a key
   */
  function templateValues(): array {
    $values = [
      'EMAIL'=>$this->email,
      'PASSWORD_KEY'=>self::PASSWORD_VALUE,
      'ACTION_KEY'=>'/user/register/verify'
    ];
    if ($this->error !== null) {
      $errorMessage = match($this->error) {
        'database_error' => '<span class="error-text">Une erreur de base de données s\'est produite.</span>',
        default => '<span class="error-text">Une erreur inconnue s\'est produite.</span>'
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
