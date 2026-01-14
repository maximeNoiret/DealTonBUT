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
                'invalid_credentials' => '<span class="error-text">Email ou mot de passe incorrect.</span>',
                'database_error' => '<span class="error-text">Une erreur de base de données est survenue. Veuillez réessayer plus tard.</span>',
                'reset_link_expired' => '<span class="error-text">Ce lien de réinitialisation a expiré ou est invalide.</span>',
                'password_changed' => 'Votre mot de passe à été modifié. Veuillez vous connecter avec le nouveau.',
                default => 'Une erreur inconnue est survenue.'
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

  /**
   * @description Toggle that show the navbar
   * @return bool
   */
  public function showNavbar(): bool {
    return false;
  }
}