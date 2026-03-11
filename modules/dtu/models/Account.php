<?php

namespace models;
use dtu\models\AccountMailer;
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

    if (!(str_contains($email, '@etu.univ-amu.fr') || str_contains($email, '@univ-amu.fr'))) {
        return 'invalid_email';
    }

    $_SESSION['username'] = $username;

    if (str_contains($email, '@univ-amu.fr')) {
        $db->registerAccount($username, $email, 'teacher');
    } else {
        $db->registerAccount($username, $email, 'student');
    }

    $token = bin2hex(random_bytes(16));
    $hashedToken = hash('sha256', $token);
    $db->insertToken($email, $token);
    $verifyLink = 'https://' . $_SERVER['HTTP_HOST'] . //self::DOMAIN_NAME .
      '/user/register/verify?token=' . $token;
    if (!AccountMailer::sendVerificationEmail($email, $verifyLink)) {
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
          $_SESSION['profile_picture'] = $account['profile_picture'] ?? 'account_pp.webp';
          $_SESSION['role'] = $account['role'] ?? 'student';
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
      $resetLink = 'https://' . $_SERVER['HTTP_HOST'] . //self::DOMAIN_NAME .
        '/user/validate?email=' . urlencode($email) . '&token=' . $token;
      if (!AccountMailer::sendForgotPassword($email, $resetLink)) {
        return 'message';
      };
      return 'message';

    }

    /**
     * @description Show the name of the user, by using their university email
     * @param string|null $email
     * @return string
     */
  static function getName(?string $email = null): string
  {
    // Récupère l'email uniquement s'il s'agit bien d'une chaîne
    if (isset($_SESSION['email']) && is_string($_SESSION['email']) && !isset($email)) {
      $email = $_SESSION['email'];
    }
    // Extrait la partie locale avant le @
    $parts = explode('@', $email);
    $name = $parts[0];
    // Remplace les points par des espaces et capitalise
    $name = str_replace('.', ' ', $name);
    return ucwords($name);
  }
}
