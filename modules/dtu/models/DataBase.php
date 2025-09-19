<?php

namespace models;

use PDO;

class DataBase {
  const string DBHOST = 'localhost';
  const string DBNAME = 'DealTonBUT';
  const string DBUSER = 'guest';
  const string DBPASS = ''; // TODO: put it in a file!!!
  const string DSN = 'mysql:host=' . self::DBHOST .
    ';dbname=' . self::DBNAME . ';charset=utf8mb4';

  static PDO $dbConn;

  static function openDataBase(string $user, string $password) {
    self::$dbConn = new PDO(self::DSN, $user, $password,
      [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  }

  // NOTE: this is very unsafe!
  static function executeQuery(string $query) {
    self::$dbConn->prepare($query)->execute();
  } 

  static function registerAccount (
    string $username,
    string $email,
    string $password
  ) {
    $query = self::$dbConn->prepare('SELECT email FROM user_ WHERE email = :email');
    $query->bindValue('email', $email, PDO::PARAM_STR);
    $query->execute();
    if ($query->fetch()) {
      throw AccountAlreadyExists;
      exit(1);
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
