<?php

namespace models;
use exceptions\AccountAlreadyExists;
use models\AccountDB;
use core\models\Mailer;
use Random\RandomException;

class Account {
  private const string DOMAIN_NAME = 'dealtonbut.app';

  /**
   * @description Registers a new account in the database.
   * @param string $username The desired username for the new account.
   * @param string $email The email address associated with the new account.
   * @return string Status message indicating the result of the operation.
   * @throws AccountAlreadyExists|RandomException
   */
  static function registerAccount(
    string $username,
    string $email,
  ): string {
    $db = AccountDB::getInstance();
    if ($db->accountExists($email) && $db->isAccountVerified($email)) {
      throw new AccountAlreadyExists();
    }
    // check if account already tried creating an account with alive ttl
    if ($db->alreadyForgotPassword($email)) {
      return 'already_sent';
    }

    $db->registerAccount($username, $email);

    $token = bin2hex(random_bytes(16));
    $hashedToken = hash('sha256', $token);
    $db->insertToken($email, $token);
    $verifyLink = 'http://' . $_SERVER['HTTP_HOST'] . //self::DOMAIN_NAME .
      '/user/register/verify?token=' . $token;
    if (!Mailer::sendVerificationEmail($email, $verifyLink)) {
      return 'mailer_error';
    };
    return 'verification_mail_sent';
  }

/**
   * @description Validates user credentials against the database.
   * @param string $email The email address of the user.
   * @param string $password The password provided by the user.
   * @return bool Returns true if the credentials are valid, false otherwise.
 */

  static function validateCredentials(string $email, string $password): bool {
    // CHECK IF (email, hash(password)) IN user_
    $account = AccountDB::getInstance()->getAccount($email, $password);
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
      $db = AccountDB::getInstance();
      if (!$db->accountExists($email)) {
        return 'message';
      }
      // check if account already requested password reset with alive ttl
      if ($db->alreadyForgotPassword($email)) {
        return 'already_sent';
      }

      $token = bin2hex(random_bytes(16));
      //$hashedToken = hash('sha256', $token);
      $db->insertToken($email, $token);  // TODO: update db
      $resetLink = 'http://' . $_SERVER['HTTP_HOST'] . //self::DOMAIN_NAME .
        '/user/validate?email=' . urlencode($email) . '&token=' . $token;
      if (!Mailer::sendForgotPassword($email, $resetLink)) {
        return 'message';
      };
      return 'message';

    }
}
