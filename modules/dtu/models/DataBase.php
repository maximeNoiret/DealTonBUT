<?php

namespace models;

use exceptions\AccountAlreadyExists;
use exceptions\DatabaseNotInitiated;
use PDO;

class DataBase {
  const string DSN = '';
//  const string DSN = 'mysql:host=' . self::DBHOST .
//    ';dbname=' . self::DBNAME . ';charset=utf8mb4';

  private PDO $dbConn;

  private static self $instance;

  private function __construct() {
    if (file_exists(__DIR__ . '/../../../.env')) {
      $env = parse_ini_file(__DIR__ . '/../../../.env');
    } else {
      $env['DB_HOSTNAME'] = getenv('DB_HOSTNAME');
      $env['DB_NAME']     = getenv('DB_NAME');
      $env['DB_USER']     = getenv('DB_USERNAME');
      $env['DB_PASSWORD'] = getenv('DB_PASSWORD');
    }
    $dbConn = new PDO(
      'mysql:host=' . $env['DB_HOSTNAME'] .
      ';dbname=' . $env['DB_NAME'] . ';charset=utf8mb4',
      $env['DB_USER'], $env['DB_PASSWORD'],
      [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  }

  public static function getInstance(): self {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  // NOTE: this is very unsafe!

  /**
   * @throws DatabaseNotInitiated
   */
  public function executeQuery(string $query): void {
    if (isset($dbConn)) {
      $dbConn->prepare($query)->execute();
    } else {
      throw new DatabaseNotInitiated();
    }
  }

  /**
   * @throws AccountAlreadyExists
   * @throws DatabaseNotInitiated
   */
  public function registerAccount (
    string $username,
    string $email,
    string $password
  ): void {
    if (!isset($dbConn)) {
      throw new DatabaseNotInitiated();
    }
    $query = $dbConn->prepare('SELECT email FROM user_ WHERE email = :email');
    $query->bindValue('email', $email);  // already uses PDO_PARAM_STR
    $query->execute();
    if ($query->fetch()) {
      throw new AccountAlreadyExists();
    }
    $hashedpwd = password_hash($password, PASSWORD_DEFAULT);
    $query = $dbConn->prepare(
      'INSERT INTO user_(email, username, hashedpwd)
      VALUES (:email, :username, :hashedpwd)');
    $query->bindValue('email', $email);
    $query->bindValue('username', $username);
    $query->bindValue('hashedpwd', $hashedpwd);
    $query->execute();
    echo 'TODO: remove this and instore actually logging in ig, but if you\'re reading this it worked check the database.';
  }
  
}
