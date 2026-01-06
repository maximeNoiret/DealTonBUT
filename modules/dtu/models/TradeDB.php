<?php

namespace dtu\models;

use core\models\DataBase;

class TradeDB extends DataBase {


  /**
   * @description Return the offers in function of the args given ( the args are MySQL operator), see MarketPlace->getOffers() for the used method
   * @param string $orderBy Type of the sort (eg : COST ( order by the cost of the offer ))
   * @param string $suffixe Supplementary information for the sort (eg : ASC ( Ascending order ))
   * @return array<mixed> The list of offers
   * @deprecated
   */
  public function getOffers(string $orderBy, string $suffixe): array {
    if ($orderBy == '') {
      $query = $this->dbConn->prepare(
        'SELECT u.username as \'username\', title, description, price, deadline
       FROM offer o
       INNER JOIN user_ u
       ON o.owner = u.email');
      $query->execute();
      return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    if ($orderBy == 'search-string' && $suffixe == '') {
      $query = $this->dbConn->prepare(
        "SELECT u.username as 'username', title, description, price, deadline
       FROM offer o
       INNER JOIN user_ u
       ON o.owner = u.email
       WHERE title LIKE CONCAT('%',$orderBy,'%')");
      $query->execute();
      return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
      $query = $this->dbConn->prepare(
        'SELECT u.username as \'username\', title, description, price, deadline
       FROM offer o
       INNER JOIN user_ u
       ON o.owner = u.email
       ORDER BY ' . $orderBy . ' ' . $suffixe);
      $query->execute();
      return $query->fetchAll(PDO::FETCH_ASSOC);
    }
  }


  /**
   * @description Retrieves a specific offer by its unique identifier.
   * @param int $ouid The unique identifier of the offer.
   * @return mixed The offer details or false if not found.
   */
  public function getOffer(int $ouid): mixed {
    // note : the method was meant to return a array<mixed>, but since fetch()
    // return mixed, it raised a phpstan error. And so the method return
    // mixed
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


  public function buyOffer(string $email, int $ouid): bool
  {
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
                WHERE email = :email'
      );
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
    } catch (\Exception $e) {
      $this->dbConn->rollBack();
      return false;
    }
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
  ): int {
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

    return (int) $this->dbConn->lastInsertId();
  }

  /**
   * @description Inserts a tag and associates it with an offer.
   * @param string $tagname The name of the tag.
   * @param int $ouid The unique identifier of the offer.
   * @return void
   */
  public function insertTag(string $tagname, int $ouid): void {
    $query1 = $this->dbConn->prepare('INSERT IGNORE INTO tag (tagname) VALUES (:tagname)');
    $query1->bindValue('tagname', $tagname);
    $query1->execute();

    $query2 = $this->dbConn->prepare('INSERT INTO tags (ouid, tagname) VALUES (:ouid, :tagname)');
    $query2->bindValue('ouid', $ouid);
    $query2->bindValue('tagname', $tagname);
    $query2->execute();
  }
}