<?php

namespace dtu\models;

use core\models\Mailer;
use models\Account;

class AccountMailer {
  /**
   * @param $email : Email of the user to send reset link.
   * @param $link : Reset link.
   * @return bool : Returns true if mail sent successfully, false otherwise.
   */
  public static function sendForgotPassword(string $email, string $link): bool {
    $body = '<h1>DealTonBUT - Mot de passe oublié</h1>
<p>Vous recevez ce mail suite à une requête de réinitialisation de mot de passe sur DealTonBUT.</p>
<p>Veuillez cliquer <a href="' . $link . '">ICI</a> afin de réinitialiser votre mot de passe.</p><br>
<p>Si vous ne pouvez pas cliquer sur le lien, copiez-collez celui-ci:<p>
<p style="color: #0077FF">' . $link . '</p><br><br>
<p>Si vous n\'avez pas fait cette requête, vous pouvez ignorer ce mail.</p><br>
<h5>DealTonBUT - On n\'a pas encore de slogan.</h5>';
    $altBody = 'DealTonBUT - Mot de passe oublié\n\n' .
      'Vous recevez ce mail suite à une requête de réinitialisation de mot de passe sur DealTonBUT.\n' .
      'Veuillez utiliser ce lien:\n ' . $link . '\n\n' .
      'Si vous n\'avez pas fait cette requête, vous pouvez ignorer ce mail.\n\n' .
      'DealTonBUT - On n\'a pas encore de slogan.';
    $name = Account::getName($email);

    return Mailer::sendMail('no-reply',
      $email,
      $name,
      'Forgot Password',
      $body,
      $altBody,
      true);
  }

  /**
   * @param $email : Email of the user to send verification link.
   * @param $link : Verification link.
   * @return bool : Returns true if mail sent successfully, false otherwise.
   */
  public static function sendVerificationEmail(string $email, string $link): bool {
    $body = '<h1>DealTonBUT - Vérification de l\'adresse e-mail</h1>
<p>Vous recevez ce mail suite à votre inscription sur DealTonBUT.</p>
<p>Veuillez cliquer <a href="' . $link . '">ICI</a> afin de vérifier votre adresse e-mail.</p><br>
<p>Si vous ne pouvez pas cliquer sur le lien, copiez-collez celui-ci:<p>
<p style="color: #0077FF">' . $link . '</p><br><br>
<p>Si vous n\'avez pas fait cette requête, vous pouvez ignorer ce mail.</p><br>
<h5>DealTonBUT - On n\'a pas encore de slogan.</h5>';
    $altBody = 'DealTonBUT - Vérification de l\'adresse e-mail\n\n' .
      'Vous recevez ce mail suite à votre inscription sur DealTonBUT.\n' .
      'Veuillez utiliser ce lien:\n ' . $link . '\n\n' .
      'Si vous n\'avez pas fait cette requête, vous pouvez ignorer ce mail.\n\n' .
      'DealTonBUT - On n\'a pas encore de slogan.';
    $name = Account::getName($email);

    return Mailer::sendMail('no-reply',
      $email,
      $name,
      'Mail Verification',
      $body,
      $altBody,
      true);
  }
}