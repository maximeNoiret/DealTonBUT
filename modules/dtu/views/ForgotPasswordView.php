<?php

namespace views;

use views\AbstractView;

class ForgotPasswordView extends AbstractView
{

  const string EMAIL_VALUE = 'email';

  public function __construct(private ?string $status = null )
  {
  }

  function path(): string {
    return __DIR__ . DIRECTORY_SEPARATOR . 'ForgotPassword.html';
  }

  function templateValues(): array
  {
    AbstractView::debug_to_console($this->status);
    $values = [
      'EMAIL_KEY' => self::EMAIL_VALUE,
    ];     // TODO: add values if needed
    if ($this->status !== null) {
      $statusMessage = match($this->status) {
        'message' => 'Si votre email éxiste, vous recevrez un mail.',
        'already_sent' => '<span class=error-text>Vous avez déjà une demande active.</span>',  // TODO: find better way, this is bad.
        default => $this->status
      };
      $values['MESSAGE'] = $statusMessage;
    } else {
      $values['MESSAGE'] = '';
    }
    return $values;
  }

  function navbarText(): string {
    return 'Mot de passe oublié';
  }
}
