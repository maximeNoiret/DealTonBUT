<?php

namespace views\User\LoginForm;

use core\views\AbstractView;

class  LoginFormView extends AbstractView {
  /**
   * @var string : Value for a html attribute "for="
   */
    const string EMAIL_KEY='email';
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
        return __DIR__ . DIRECTORY_SEPARATOR . 'LoginForm.html';
    }

  /**
   * @description Replace keys value by their real value in the associated .html file
   * @return array<string,mixed> : The array that contain the real value that are associated by a key
   */
    function templateValues(): array {
        $values = [
            'EMAIL_KEY'=>self::EMAIL_KEY,
            'PASSWORD_KEY'=>self::PASSWORD_VALUE,
            'ACTION_KEY'=>'/user/login'
        ];
        if ($this->error !== null) {
            $errorMessage = match($this->error) {
                'invalid_credentials' => 'Invalid email or password.',
                'database_error' => 'A database error occurred. Please try again.',
                'reset_link_expired' => 'The password reset link has expired.',
                'password_changed' => 'Your password has been changed successfully. Please log in with your new password.',
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
        return 'Connexion';
    }

}