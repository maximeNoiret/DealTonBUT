<?php /** @noinspection PhpUndefinedConstantInspection */

namespace models;

use exceptions\AccountAlreadyExists;
use exceptions\DatabaseNotInitiated;
use PDO;

class DataBase {
  private PDO $dbConn;

  private static self $instance;

  /**
   * @description Private constructor to initialize the database connection.
   * @return void
   * @throws DatabaseNotInitiated
   */
  private function __construct() {
    if (file_exists(__DIR__ . '/../../../.env')) {
      $env = parse_ini_file(__DIR__ . '/../../../.env');
    } else {
      $env['DB_HOSTNAME'] = getenv('DB_HOSTNAME');
      $env['DB_NAME']     = getenv('DB_NAME');
      $env['DB_USER']     = getenv('DB_USERNAME');
      $env['DB_PASSWORD'] = getenv('DB_PASSWORD');
    }

    /**
     * @var array<string, string> $env
     */
    // if any of the env variables aren't set, throw
    if (!isset($env['DB_HOSTNAME']) || 
      !isset($env['DB_NAME']) ||
      !isset($env['DB_USER']) ||
      !isset($env['DB_PASSWORD'])) {
      throw new DatabaseNotInitiated();
    }
    $this->dbConn = new PDO(
      'mysql:host=' . $env['DB_HOSTNAME'] .
      ';dbname=' . $env['DB_NAME'] . ';charset=utf8mb4',
      $env['DB_USER'], $env['DB_PASSWORD'],
      [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  }

  /**
   * @description Retrieves the singleton instance of the DataBase class.
   * @return DataBase The singleton instance.
   */

  public static function getInstance(): self {
    if (!isset(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  // NOTE: this is very unsafe!
  /**
   * @description Executes a raw SQL query.
   * @param string $query The SQL query to execute.
   * @return void
   * @deprecated
   */
  public function executeQuery(string $query): void {
    $this->dbConn->prepare($query)->execute();
  }

  /**
   * @description Registers a new account in the database.
   * @param string $username The desired username for the new account.
   * @param string $email The email address associated with the new account.
   * @param string $password The password for the new account.
   * @return void
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

  /**
   * @description Checks if an account with the given email exists.
   * @param string $email The email address to check.
   * @return bool True if the account exists, false otherwise.
   */
  public function accountExists(string $email): bool {
    $query = $this->dbConn->prepare('SELECT email FROM user_ WHERE email = :email');
    $query->bindValue('email', $email);
    $query->execute();
    return $query->fetch() !== null;
  }

  /**
   * @description Retrieves account information for the given email and password.
   * @param string $email The email address of the account.
   * @param string $password The password of the account.
   * @return bool|array<string, string>
   */
  public function getAccount(string $email, string $password): bool|array {
    $query = $this->dbConn->prepare('
      SELECT username, email, hashedpwd, balance 
      FROM user_
      WHERE email = :email');
    $query->bindValue('email', $email);
    $query->execute();
    
    /**
     * @var array<string, string> $user
     */
    $user = $query->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        return false;
    }
    
    // Verify password against stored hash
    if (password_verify($password, $user['hashedpwd'])) {
        // Return user data WITHOUT the password hash
        return [
            'username' => $user['username'],
            'email' => $user['email'],
            'balance' => $user['balance']
        ];
    }
    return false;
  }

  /**
   * @description Retrieves the balance for the given email.
   * @param string $email The email address of the account.
   * @return int|string|false|null The balance amount, or false/null if not found.
   */
  public function getBalance(string $email): int|string|false|null {
    $query = $this->dbConn->prepare('
      SELECT balance FROM user_ WHERE email = :email');
    $query->bindValue('email', $email);
    $query->execute();
    return $query->fetchColumn();
  }

    /**
     * @description Checks if a password reset has already been requested for the given email.
     * @param string $email The email address to check.
     * @return bool True if a password reset has already been requested, false otherwise.
     */

  public function alreadyForgotPassword(string $email): bool {
    $query = $this->dbConn->prepare(
      'SELECT email FROM token WHERE email = :email AND deadline > CURRENT_TIMESTAMP');
    $query->bindValue('email', $email);
    $query->execute();
    return $query->fetch() !== false;
  }

    /**
     * @description Inserts a password reset token for the given email.
     * @param string $email The email address associated with the token.
     * @param string $token The password reset token.
     * @return void
     */
  public function insertToken(string $email, string $token): void {
    $query = $this->dbConn->prepare('INSERT INTO token(email, token) VALUES (:email, :token)');
    $query->bindValue('email', $email);
    $query->bindValue('token', $token);
    $query->execute();
  }

    /**
    * @description Retrieves all offers from the database.
    * @return array<mixed>
   */
  public function getOffers(): array {
    $query = $this->dbConn->prepare(
      'SELECT ouid, owner, u.username as \'username\', title, description, price, deadline
       FROM offer o
       INNER JOIN user_ u
       ON o.owner = u.email
       WHERE ouid NOT IN (
           SELECT ouid
           FROM transaction)
           ');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }

  public function offersInTransaction(int $ouid): bool {
    $query = $this->dbConn->prepare('
    SELECT ouid
    FROM offer
    WHERE ouid = :ouid
    AND ouid IN (
        SELECT ouid
        FROM transaction)
        ');
    $query->bindValue('ouid', $ouid);
    $query->execute();
    $offers = $query->fetchAll(PDO::FETCH_ASSOC);
    return !empty($offers);
  }

  /**
   * @description Retrieves a specific offer by its unique identifier.
   * @param int $ouid The unique identifier of the offer.
   * @return array<string, string>|false The offer details or false if not found.
   */
  public function getOffer(int $ouid): array|false {
    $query = $this->dbConn->prepare(
      'SELECT owner, u.username as \'username\', title, description, price, deadline
       FROM offer o
       INNER JOIN user_ u
       ON o.owner = u.email
       WHERE o.ouid = :ouid');
    $query->bindValue('ouid', $ouid);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * @description Deletes an offer from the database.
   * @param int $ouid The unique identifier of the offer to delete.
   * @return void
   */
  public function deleteOffer(int $ouid): void {
    $query = $this->dbConn->prepare('DELETE FROM offer WHERE ouid = :ouid');
    $query->bindValue('ouid', $ouid);
    $query->execute();
  }

  /**
   * @description Retrieves all offers made by a specific user.
   * @param string $email The email address of the user.
   * @return array<mixed>
   */
  public function getUserOffers(string $email): array {
    $query = $this->dbConn->prepare(
      'SELECT o.ouid, owner, u.username as \'username\', title, description, price, deadline
       FROM offer o
       INNER JOIN user_ u
       ON o.owner = u.email
       WHERE u.email = :email');
    $query->bindValue('email', $email);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * @description Retrieves all offers bought by a specific user.
   * @param string $email The email address of the user.
   * @return array<mixed>
   */
  public function getBoughtOffers(string $email): array {
    $query = $this->dbConn->prepare(
      'SELECT o.ouid, owner, u.username as \'username\', o.title, o.description, o.price, o.deadline
        FROM transaction t
        INNER JOIN offer o
        ON t.ouid = o.ouid
        JOIN user_ u
        ON o.owner = u.email
        WHERE t.email = :email');
    $query->bindValue('email', $email);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }

  public function buyOffer(string $email, int $ouid): bool {
      try {
      $this->dbConn->beginTransaction();
      $offerQuery = $this->dbConn->prepare('
        SELECT owner, price, deadline 
        FROM offer 
        WHERE ouid = :ouid
      ');
      $offerQuery->bindValue('ouid', $ouid);
      $offerQuery->execute();
      $offer = $offerQuery->fetch(PDO::FETCH_ASSOC);

      //Si l'offre n'existe pas
      if (!$offer) {
          $this->dbConn->rollBack();
          return false;
      }
      // Si l'utilisateur essaye d'acheter ça propre offre
      if ($offer['owner'] == $email) {
          $this->dbConn->rollBack();
          return false;
      }
      //Si la date max a été dépasser
      if (strtotime($offer['deadline']) < time()) {
          $this->dbConn->rollBack();
          return false;
      }
      // Si l'utilisateur n'as pas assez de sous
      $queryForBalance = $this->getBalance($email);
      if ($queryForBalance === false || $queryForBalance < $offer['price']) {
          $this->dbConn->rollBack();
          return false;
      }
      $queryRemoveMoney = $this->dbConn->prepare('
      UPDATE user_
      SET balance = balance - :price
      WHERE email = :email');
      $queryRemoveMoney->bindValue('price', $offer['price']);
      $queryRemoveMoney->bindValue('email', $email);
      $queryRemoveMoney->execute();

      $queryAddMoney = $this->dbConn->prepare('
        UPDATE user_ 
        SET balance = balance + :price 
        WHERE email = :email
      ');
      $queryAddMoney->bindValue('price', $offer['price']);
      $queryAddMoney->bindValue('email', $offer['owner']);
      $queryAddMoney->execute();

      $transactionQuery = $this->dbConn->prepare('
        INSERT INTO transaction(email, ouid, amount, transaction_time) 
        VALUES (:email, :ouid, :amount, :transaction_time)
      ');
      $transactionQuery->bindValue('email', $email);
      $transactionQuery->bindValue('ouid', $ouid);
      $transactionQuery->bindValue('amount', $offer['price']);
      $transactionQuery->bindValue('transaction_time', date('Y-m-d H:i:s'));
      $transactionQuery->execute();

      $this->dbConn->commit();
      return true;
  } catch (\execption $e) {
          $this->dbConn->rollBack();
          return false;
      }
}


    /**
     * @description Deletes a user from the database.
     * @param string $email The email address of the user to delete.
     * @return void
     */
  public function deleteUser(string $email): void {
    $query = $this->dbConn->prepare('DELETE FROM user_ WHERE email = :email');
    $query->bindValue('email', $email);
    $query->execute();
  }

    /**
     * @description Inserts a new offer into the database.
     * @param string $userEmail The email address of the offer owner.
     * @param string $title The title of the offer.
     * @param float $price The price of the offer.
     * @param string $description The description of the offer.
     * @param string $deadline The deadline for the offer in 'YYYY-MM-DD' format.
     * @return void
     */
    public function insertOffre(
        string $userEmail,
        string $title,
        float $price,
        string $description,
        string $deadline
    ): void {
        // Insérer l'offre
        $query = $this->dbConn->prepare('
        INSERT INTO offer(owner, title, description, price, creation_time, deadline)
        VALUES (:owner, :title, :description, :price, :creation_time, :deadline)
    ');

        $query->bindValue('owner', $userEmail);
        $query->bindValue('title', $title);
        $query->bindValue('description', $description);
        $query->bindValue('price', $price);
        $query->bindValue('creation_time', date('Y-m-d H:i:s'));
        $query->bindValue('deadline', $deadline . ' 23:59:59');
        $query->execute();
    }

/**
     * @description Adds a subject for a user with initial points.
     * @param string $email The email address of the user.
     * @param string $subject_name The name of the subject.
     * @param float $points The initial points for the subject.
     * @return void
     */
    public function setSubject(string $email, string $subject_name, float $points): void {
        $query = $this->dbConn->prepare('INSERT INTO points(email, subject_name, points) VALUES (:email, :subject_name, :points)');
        $query->bindValue('email', $email);
        $query->bindValue('subject_name', $subject_name);
        $query->bindValue('points', $points);
        $query->execute();
    }

    /**
     * @description Retrieves all subjects for a user.
     * @param string $email The email address of the user.
     * @return array<mixed>
     */

    public function getSubject(string $email): array {
        $query = $this->dbConn->prepare('SELECT subject_name FROM points WHERE email = :email');
        $query->bindValue('email', $email);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @description Updates the points for a specific subject of a user.
     * @param string $email The email address of the user.
     * @param float $points The new points value.
     * @param string $subject_name The name of the subject.
     * @return void
     */
    public function setPoints(string $email, float $points, string $subject_name): void {
        $query = $this->dbConn->prepare('UPDATE points SET points = :points WHERE email = :email AND subject_name = :subject_name');
        $query->bindValue('email', $email);
        $query->bindValue('points', $points);
        $query->bindValue('subject_name', $subject_name);
        $query->execute();
    }

    /**
     * @description Retrieves the points for a specific subject of a user.
     * @param string $email The email address of the user.
     * @param string $subject_name The name of the subject.
     * @return float The points for the specified subject.
     */
    public function getPoints(string $email, string $subject_name): float {
        $query = $this->dbConn->prepare('SELECT points FROM points WHERE email = :email AND subject_name = :subject_name');
        $query->bindValue('email', $email);
        $query->bindValue('subject_name', $subject_name);
        $query->execute();
        $results = (float)$query->fetchColumn();
        return $results;
    }

    /**
     * @description Transfers points between two subjects for a user.
     * @param string $email The email address of the user.
     * @param float $points The number of points to transfer.
     * @param string $from_subject The subject from which points are deducted.
     * @param string $to_subject The subject to which points are added.
     * @return void
     */
    public function transferPoints(string $email, float $points, string $from_subject, string $to_subject): void {
        try {
            $this->dbConn->beginTransaction();
            
            $query1 = $this->dbConn->prepare(
              'UPDATE points SET points = points - :points WHERE email = :email AND subject_name = :subject_name'
            );
            // Enlevage de points
            $query1->bindValue('email', $email);
            $query1->bindValue('points', $points);
            $query1->bindValue('subject_name', $from_subject);
            $query1->execute();
            
            $query2 = $this->dbConn->prepare(
              'UPDATE points SET points = points + :points WHERE email = :email AND subject_name = :subject_name'
            );

            // Ajout de points
            $query2->bindValue('email', $email);
            $query2->bindValue('points', $points);
            $query2->bindValue('subject_name', $to_subject);
            $query2->execute();
            
            $this->dbConn->commit();
        } catch (\Exception $e) {
            $this->dbConn->rollBack();
            throw $e;
        }
    }

  public function updateBalance(string $email): void {
    $balance = $this->getBalance($email);
    if ($balance) {
      $_SESSION['balance'] = $balance;
    }
  } 
}



