<?php

namespace models;

use exceptions\AccountAlreadyExists;
use PDO;

class DataBase {
  const string DSN = '';
//  const string DSN = 'mysql:host=' . self::DBHOST .
//    ';dbname=' . self::DBNAME . ';charset=utf8mb4';

  private static PDO $dbConn;

  private static self $instance;

  private function __construct() {
    if (file_exists(__DIR__ . '/../../../.env')) {
      $env = parse_ini_file(__DIR__ . '/../../../.env');
    } else {
      $env[''] = getenv();  // TODO: choose names for vars
    }
  }

  public static function getInstance(): self {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  static function openDataBase(string $user, string $password): void {
    self::$dbConn = new PDO(self::DSN, $user, $password,
      [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  }

  // NOTE: this is very unsafe!
  static function executeQuery(string $query): void {
    self::$dbConn->prepare($query)->execute();
  }

  /**
   * @throws AccountAlreadyExists
   */
  static function registerAccount (
    string $username,
    string $email,
    string $password
  ): void {
    $query = self::$dbConn->prepare('SELECT email FROM user_ WHERE email = :email');
    $query->bindValue('email', $email, PDO::PARAM_STR);
    $query->execute();
    if ($query->fetch()) {
      throw new AccountAlreadyExists();
    }
    $hashedpwd = password_hash($password, PASSWORD_DEFAULT);
    $query = self::$dbConn->prepare(
      'INSERT INTO user_(email, username, hashedpwd)
      VALUES (:email, :username, :hashedpwd)');
    $query->bindValue('email', $email, PDO::PARAM_STR);
    $query->bindValue('username', $username, PDO::PARAM_STR);
    $query->bindValue('hashedpwd', $hashedpwd, PDO::PARAM_STR);
    $query->execute();
    echo 'TODO: remove this and instore actually logging in ig, but if you\'re reading this it worked check the database.';
  }
  
}
