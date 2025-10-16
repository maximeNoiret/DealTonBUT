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

  //offre

    private function getNextOffreId(): int {
      $query = $this->dbConn->prepare('SELECT id FROM token ORDER BY id DESC LIMIT 1');
      $query->execute();
      $result = $query->fetch(PDO::FETCH_ASSOC);
      return ($result['max_id'] ?? 0) + 1;
    }

    public function insertOffre(
        string $userEmail,
        string $title,
        float $price,
        string $description,
        string $tag,
        string $deadline
    ): void {
        // Générer un ID unique pour l'offre
        $ouid = $this->getNextOffreId();

        // Insérer l'offre
        $query = $this->dbConn->prepare('
        INSERT INTO offer(ouid, owner, title, description, price, creation_time, deadline)
        VALUES (:ouid, :owner, :title, :description, :price, :creation_time, :deadline)
    ');

        $query->bindValue('ouid', $ouid, PDO::PARAM_INT);
        $query->bindValue('owner', $userEmail);
        $query->bindValue('title', $title);
        $query->bindValue('description', $description);
        $query->bindValue('price', $price);
        $query->bindValue('creation_time', date('Y-m-d H:i:s'));
        $query->bindValue('deadline', $deadline . ' 23:59:59');
        $query->execute();

        // Insérer le tag si fourni
        if (!empty($tag)) {
            // Vérifier si le tag existe, sinon le créer
            $tagQuery = $this->dbConn->prepare('SELECT tagname FROM tag WHERE tagname = :tagname');
            $tagQuery->bindValue('tagname', $tag);
            $tagQuery->execute();

            if (!$tagQuery->fetch()) {
                $insertTag = $this->dbConn->prepare('INSERT INTO tag(tagname) VALUES (:tagname)');
                $insertTag->bindValue('tagname', $tag);
                $insertTag->execute();
            }

            // Associer le tag à l'offre
            $linkTag = $this->dbConn->prepare('INSERT INTO tags(ouid, tagname) VALUES (:ouid, :tagname)');
            $linkTag->bindValue('ouid', $ouid, PDO::PARAM_INT);
            $linkTag->bindValue('tagname', $tag);
            $linkTag->execute();
        }
    }

    public function getAllOffres(): array {
        $query = $this->dbConn->prepare('
        SELECT 
            o.ouid,
            o.owner,
            o.title,
            o.description,
            o.price,
            o.creation_time,
            o.deadline,
            GROUP_CONCAT(t.tagname SEPARATOR ",") as tags
        FROM offer o
        LEFT JOIN tags t ON o.ouid = t.ouid
        WHERE o.deadline > NOW()
        GROUP BY o.ouid
        ORDER BY o.creation_time DESC
    ');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
