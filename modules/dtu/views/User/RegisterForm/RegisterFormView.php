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
      'ACTION_KEY'=>'/user/register'
    ];
    if ($this->error !== null) {
      $errorMessage = match($this->error) {
        'account_already_exists' => '<span class="error-text">Cette addresse est déjà utilisé.</span>',
        'database_error' => '<span class="error-text">Une erreur de base de données s\'est produite.</span>',
        'mailer_error' => '<span class="error-text">Une erreur de l\'envoi de l\'email s\'est produite.</span>',
        'verification_mail_sent' => 'Un email de vérification a été envoyé à votre adresse.',
        'verification_link_expired' => '<span class="error-text">Le lien de vérification a expiré. Veuillez vous inscrire à nouveau.</span>',
        'already_sent' => '<span class="error-text">Un email de vérification a déjà été envoyé récemment. Veuillez vérifier votre boîte de réception.</span>',
        'invalid_email' => '<span class="error-text">L\'adresse email doit être au format @etu.univ-amu.fr ou @univ-amu.fr.</span>',
        default => '<span class="error-text">Une erreur inconnue s\'est produite.</span><br>' . $this->error
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

  /**
   * @description Toggle that show the navbar
   * @return bool
   */
  public function showNavbar(): bool {
    return false;
  }
}
