<?php

namespace models;

use exceptions\AccountAlreadyExists;
use exceptions\DatabaseNotInitiated;
use PDO;

class DataBase {
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
    $this->dbConn = new PDO(
      'mysql:host=' . $env['DB_HOSTNAME'] .
      ';dbname=' . $env['DB_NAME'] . ';charset=utf8mb4',
      $env['DB_USER'], $env['DB_PASSWORD'],
      [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  }

  public static function getInstance(): self {
    if (!isset(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  // NOTE: this is very unsafe!
  /**
   * @deprecated
   */
  public function executeQuery(string $query): void {
    $this->dbConn->prepare($query)->execute();
  }

  /**
   * @throws AccountAlreadyExists
   */
  public function registerAccount (
    string $username,
    string $email,
    string $password
  ): void {
    $query = $this->dbConn->prepare('SELECT email FROM user_ WHERE email = :email');
    $query->bindValue('email', $email);  // already uses PDO_PARAM_STR
    $query->execute();
    if ($query->fetch()) {
      throw new AccountAlreadyExists();
    }
    $hashedpwd = password_hash($password, PASSWORD_DEFAULT);
    $query = $this->dbConn->prepare(
      'INSERT INTO user_(email, username, hashedpwd)
      VALUES (:email, :username, :hashedpwd)');

    //
    $query->bindValue('email', $email);
    $query->bindValue('username', $username);
    $query->bindValue('hashedpwd', $hashedpwd);
    $query->execute();
  }
  
  public function accountExists(string $email): bool {
    $query = $this->dbConn->prepare('SELECT email FROM user_ WHERE email = :email');
    $query->bindValue('email', $email);
    $query->execute();
    return $query->fetch() !== null;
  }

  public function getAccount(string $email, string $password): bool|array {
    $query = $this->dbConn->prepare('
      SELECT username, email, hashedpwd 
      FROM user_
      WHERE email = :email');
    $query->bindValue('email', $email);
    $query->execute();
    
    $user = $query->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        return false;
    }
    
    // Verify password against stored hash
    if (password_verify($password, $user['hashedpwd'])) {
        // Return user data WITHOUT the password hash
        return [
            'username' => $user['username'],
            'email' => $user['email']
        ];
    }
    return false;
}

  public function alreadyForgotPassword(string $email): bool {
    $query = $this->dbConn->prepare(
      'SELECT email FROM token WHERE email = :email AND deadline > CURRENT_TIMESTAMP');
    $query->bindValue('email', $email);
    $query->execute();
    return $query->fetch() !== false;
  }

  public function insertToken($email, $token) {
    $query = $this->dbConn->prepare('INSERT INTO token(email, token) VALUES (:email, :token)');
    $query->bindValue('email', $email);
    $query->bindValue('token', $token);
    $query->execute();
  }

  public function getOffers(): array {
    $query = $this->dbConn->prepare(
      'SELECT u.username as \'username\', title, description, price, deadline
       FROM offer o
       INNER JOIN user_ u
       ON o.owner = u.email');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }
}
