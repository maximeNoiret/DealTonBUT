<?php

namespace models;
use exceptions\AccountAlreadyExists;
use models\DataBase;
use core\models\Mailer;
use Random\RandomException;

class Account {
  private const string DOMAIN_NAME = 'dealtonbut.app';

  /**
   * @description Registers a new account in the database.
   * @param string $username The desired username for the new account.
   * @param string $email The email address associated with the new account.
   * @param string $password The password for the new account.
   * @return void
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

/**
   * @description Validates user credentials against the database.
   * @param string $email The email address of the user.
   * @param string $password The password provided by the user.
   * @return bool Returns true if the credentials are valid, false otherwise.
 */

  static function validateCredentials(string $email, string $password): bool {
    // CHECK IF (email, hash(password)) IN user_
    $db = DataBase::getInstance();
    $account = $db->getAccount($email, $password);
      if (is_array($account) && !empty($account)) {
          session_regenerate_id(true);
          $_SESSION['username'] = $account['username'] ?? '';
          $_SESSION['email'] = $account['email'] ?? '';
          $_SESSION['balance'] = $account['balance'] ?? 0;
          $_SESSION['logged-in'] = true;
          return true;
      }
    return false;
  }

    /**
     * @description Initiates the password reset process for a user.
     * @param string $email The email address of the user requesting a password reset.
     * @return string Status message indicating the result of the operation.
     * @throws RandomException
     */
    static function forgotPassword(string $email): string {
    // check if account exists at all
        $db = DataBase::getInstance();
        if(!$db->accountExists($email)) {
          return 'message';
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
        $resetLink = 'https://' . self::DOMAIN_NAME .
          '/user/validate?email=' . urlencode($email) . '&token=' . $token;
        if (!Mailer::sendForgotPassword($email, $resetLink)) {
          return 'message';
        };
        return 'message';

  }
}
