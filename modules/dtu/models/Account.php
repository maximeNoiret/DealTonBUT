<?php

namespace models;
use exceptions\AccountAlreadyExists;
use exceptions\DatabaseNotInitiated;
use models\DataBase;
use models\Mailer;

class Account {
  private const string DOMAIN_NAME = 'dealtonbut.app';


  /**
   * @throws AccountAlreadyExists
   */
  function registerAccount(
    string $username,
    string $email,
    string $password,
  ): void {
    DataBase::getInstance()->registerAccount(
      $username,
      $email,
      $password
    );
  }

  static function validateCredentials(string $email, string $password): bool {
    // CHECK IF (email, hash(password)) IN user_
    $db = DataBase::getInstance();
    $account = $db->getAccount($email, $password);
    if ($account) {
      session_regenerate_id(true);
      $_SESSION['username'] = $account['username'];
      $_SESSION['email'] = $account['email'];
      $_SESSION['logged-in'] = true;
      return true;
    }
    return false;
  }

  static function forgotPassword(string $email): string {
    // check if account exists at all
    $db = DataBase::getInstance();
    if(!$db->accountExists($email)) {
      return 'already_exists';
    }
    // check if account already requested password reset with alive ttl
    if ($db->alreadyForgotPassword($email)) {
      return 'already_sent';
    }
    // at this point, account exists AND hasn't already requested a password reset.

    // TODO:
    // generate a random token
    $token = bin2hex(random_bytes(16));
    // hash the token for storing
    $hashedToken = hash('sha256', $token);
    // store (email, token, now+10min) into 'token' relation
    // NOTE: 'token' relation has default deadline value set to now + 10 min.
    $db->insertToken($email, $token);   
    // - [optional] encrypt (email, token) into single string
    // mail a GET link with "/user/validate?mail=:mail&token=:token" (or "/user/validate?token=:token" if encrypted)
    $mailer = new Mailer('noreply@' . SELF::DOMAIN_NAME, 'DealTonBUT');  // TODO: change domain to correct one
    $resetLink = 'https://' . SELF::DOMAIN_NAME .
      '/user/validate?email=' . urlencode($email) . '&token=' . $token;
    if (!$mailer->sendPasswordReset($email, $resetLink)) {
      return 'message';
    };
    return 'reached_end';
    // ----------------
    // Someone goes to /user/validate with GET method
    // - [optional] decrypt token from url
    // - if (email, token) in 'token' && ttl not reached: ask new password
    // - else: display "invalid link" and quit

  }
}
