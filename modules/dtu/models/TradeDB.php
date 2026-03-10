<?php

namespace dtu\models;

use controllers\Trade\MarketPlace\MarketPlace;
use core\models\DataBase;
use models\AccountDB;
use PDO;
use views\Trade\Offer\Offer;

class TradeDB extends DataBase {

  protected static $instance;

    /**
     * @description Return the offers in function of the args given ( the args are MySQL operator), see MarketPlace->getOffers() for the used method
     * @return array The list of offers and the total number of offers that match the criteria (for pagination purpose)
     */
  public static function getOffers(): array {
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $limit = 8;

    //build the base query
    $query =
      'SELECT o.ouid ,u.username as \'username\', o.owner, title, description, price, deadline, style, o.quantity, u.profile_picture
       FROM offer o
       INNER JOIN user_ u 
       ON o.owner = u.email
       LEFT JOIN tags t 
       ON t.ouid = o.ouid
       WHERE deadline > NOW()
       AND o.quantity > 0';

    [$query, $params] = self::sortOffers($query);

    // Add limit and offset
    $query .= " LIMIT $limit OFFSET $offset";
    $offers = DataBase::getInstance()->executeQueryWithParams($query, $params);

    $countQuery = 'SELECT COUNT(DISTINCT o.ouid) as total
                   FROM offer o
                   INNER JOIN user_ u 
                   ON o.owner = u.email
                   LEFT JOIN tags t 
                   ON t.ouid = o.ouid
                   WHERE deadline > NOW()
                   AND o.quantity > 0';
      $countResult = DataBase::getInstance()->executeQueryWithParams($countQuery, $params);
      $totalOffers = $countResult ? (int)$countResult[0]['total'] : 0;

    return [$offers, $totalOffers];
  }

  /**
   * @description Add to the incomplete SQL query the order by clause in
   * function of the sort parameter, and the search string if it exists.
   * Meant to be used in the getOffers() method.
   * @param string $query the sql query that is under construction
   * @return array{0: string, 1: array<string, mixed>} A tuple of [SQL query, bound params]
   */
    static function sortOffers(string $query): array
    {
        $sort = $_GET['sort'] ?? '';
        $params = [];
        if ($sort === 'trending') {
            $trendingJoin = " 
                            LEFT JOIN 
                            ( SELECT tagname, COUNT(*) as popularity 
                            FROM tags 
                            GROUP BY tagname ) 
                            AS pop ON t.tagname = pop.tagname ";

            $query = str_replace("WHERE", $trendingJoin . " WHERE", $query);
        }
        if (isset($_GET['search-string']) && !empty($_GET['search-string'])) {
            $searchString = trim($_GET['search-string']);
            if (str_starts_with($searchString, '#')) {
                $query .= " AND t.tagname LIKE :tagname";
                $params[':tagname'] = '%' . substr($searchString, 1) . '%';
            } else {
                $query .= " AND (title LIKE :searchTitle OR description LIKE :searchDesc)";
                $params[':searchTitle'] = '%' . $searchString . '%';
                $params[':searchDesc']  = '%' . $searchString . '%';
            }
        }
        $query .= " GROUP BY o.ouid";
        $allowedSorts = [
            'price-asc'   => " ORDER BY price ASC",
            'price-desc'  => " ORDER BY price DESC",
            'date'        => " ORDER BY o.creation_time DESC",
            'alphabetic'  => " ORDER BY title ASC",
            'trending'    => " ORDER BY MAX(IFNULL(pop.popularity, 0)) DESC, o.ouid DESC"
        ];
        $query .= $allowedSorts[$sort] ?? " ORDER BY o.creation_time ASC";
        return [$query, $params];
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
      'SELECT owner, u.username as \'username\', title, description, price, deadline, style, o.type, u.profile_picture
       FROM offer o
       LEFT JOIN user_ u 
       ON o.owner = u.email
       WHERE o.ouid = :ouid');

      $query->bindValue('ouid', $ouid);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
  }


  public function buyOffer(string $email, int $ouid): string|bool
  {
    try {
      $this->dbConn->beginTransaction();

      $offerQuery = $this->dbConn->prepare('
                SELECT o.owner, o.price, o.deadline, o.type, o.quantity, u.role as owner_role
                FROM offer o
                JOIN user_ u ON o.owner = u.email
                WHERE o.ouid = :ouid
            ');
      $offerQuery->bindValue('ouid', $ouid);
      $offerQuery->execute();
      $offer = $offerQuery->fetch(PDO::FETCH_ASSOC);

      //Si l'offre n'existe pas
      if (!$offer|| !isset($offer['type'])) {
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

      //si l'offre n'est plus disponible
      if($offer['quantity'] <= 0) {
        $this->dbConn->rollBack();
        return false;
      }

      // Récupérer le rôle de l'acheteur
      $buyerRoleQuery = $this->dbConn->prepare('SELECT role FROM user_ WHERE email = :email');
      $buyerRoleQuery->bindValue('email', $email);
      $buyerRoleQuery->execute();
      $buyerRoleRow = $buyerRoleQuery->fetch(PDO::FETCH_ASSOC);
      $buyerRole = $buyerRoleRow ? $buyerRoleRow['role'] : 'student';

      if ($offer['type'] === 'offer') {
        // Offre classique : l'acheteur paye le vendeur
        $buyerEmail  = $email;
        $sellerEmail = $offer['owner'];
        $buyerPays   = ($buyerRole !== 'teacher'); // teacher ne paye pas
        $sellerGains = true;
      } else {
        // Demande : le créateur de la demande donne de la valeur au fulfiller (email)
        // Le créateur (owner) ne dépense rien, le fulfiller (email) reçoit le prix
        $buyerEmail  = $offer['owner']; // logiquement l'owner est le "payeur"
        $sellerEmail = $email;          // le fulfiller reçoit
        $buyerPays   = false;           // le créateur de la demande ne dépense pas
        $sellerGains = true;            // le fulfiller reçoit le prix
      }

      // Vérification de solde uniquement si l'acheteur doit vraiment payer
      if ($buyerPays) {
        $queryForBalance = AccountDB::getInstance()->getBalance($buyerEmail);
        if ($queryForBalance === false || $queryForBalance < $offer['price']) {
          $this->dbConn->rollBack();
          return false;
        }
      }

      // Déduire le montant du payeur si nécessaire
      if ($buyerPays) {
        $queryRemoveMoney = $this->dbConn->prepare('
                UPDATE user_
                SET balance = balance - :price
                WHERE email = :email'
        );
        $queryRemoveMoney->bindValue('price', $offer['price']);
        $queryRemoveMoney->bindValue('email', $buyerEmail);
        $queryRemoveMoney->execute();
      }

      // Créditer le vendeur/fulfiller
      if ($sellerGains) {
        $queryAddMoney = $this->dbConn->prepare('
                UPDATE user_ 
                SET balance = balance + :price 
                WHERE email = :email
            ');
        $queryAddMoney->bindValue('price', $offer['price']);
        $queryAddMoney->bindValue('email', $sellerEmail);
        $queryAddMoney->execute();
      }

      $transactionQuery = $this->dbConn->prepare('
                INSERT INTO transactions(email, ouid, amount, transaction_time) 
                VALUES (:email, :ouid, :amount, :transaction_time)
            ');
      $transactionQuery->bindValue('email', $email);
      $transactionQuery->bindValue('ouid', $ouid);
      $transactionQuery->bindValue('amount', $offer['price']);
      $transactionQuery->bindValue('transaction_time', date('Y-m-d H:i:s'));
      $transactionQuery->execute();

        $queryQuantity = $this->dbConn->prepare('
                update offer
                set quantity = quantity - 1
                where ouid = :ouid
            ');
        $queryQuantity->bindValue('ouid', $ouid);
        $queryQuantity->execute();

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
      'SELECT o.ouid, owner, u.username as \'username\', title, description, price, deadline, o.type, u.profile_picture
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
      'SELECT o.ouid, owner, u.username as \'username\', u.profile_picture, o.title, o.description, o.price, o.deadline
        FROM transactions t
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
   * @description Checks if an offer has been bought by any user.
   * @param int $ouid The unique identifier of the offer.
   * @return bool Returns true if the offer has been bought, false otherwise.
   */
  public function isOfferBought(int $ouid): bool {
    $query = $this->dbConn->prepare(
      'SELECT COUNT(*) as count FROM transactions WHERE ouid = :ouid');
    $query->bindValue('ouid', $ouid);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result && $result['count'] > 0;
  }

  /**
   * @description Inserts a new offer into the database.
   * @param string $userEmail The email address of the offer owner.
   * @param string $title The title of the offer.
   * @param float $price The price of the offer.
   * @param string $description The description of the offer.
   * @param string $deadline The deadline for the offer in 'YYYY-MM-DD' format.
   * @param int $quantity The quantity of items in the offer.
   * @param string $type The type of the offer (e.g., 'offer' or 'request').
   * @param string $style The visual style of the offer card.
   * @return void
   */
  public function insertOffer(
    string $userEmail,
    string $title,
    float $price,
    string $description,
    string $deadline,
    int $quantity,
    string $type,
    string $style = 'normal'
  ): int {
    $query = $this->dbConn->prepare('
    INSERT INTO offer(owner, title, description, price, style, creation_time, deadline, quantity, type)
    VALUES (:owner, :title, :description, :price, :style, :creation_time, :deadline, :quantity, :type)
');

    $query->bindValue('owner', $userEmail);
    $query->bindValue('title', $title);
    $query->bindValue('description', $description);
    $query->bindValue('price', $price);
    $query->bindValue('creation_time', date('Y-m-d H:i:s'));
    $query->bindValue('deadline', $deadline . ' 23:59:59');
    $query->bindValue('quantity', $quantity);
    $query->bindvalue('type', $type);
    $query->bindValue('style', $style);
    $query->execute();

    return (int) $this->dbConn->lastInsertId();
  }

    public function hasBoughtOffer(int $ouid, string $email): bool {
      $queryTransaction = $this->dbConn->prepare(
          'SELECT *
          FROM transactions t
          WHERE t.ouid = :ouid AND t.email = :email'
      );
        $queryTransaction->bindValue('ouid', $ouid);
        $queryTransaction->bindValue('email', $email);
        $queryTransaction->execute();
        $transaction = $queryTransaction->fetch(PDO::FETCH_ASSOC);
        return $transaction !== false;
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

  /**
   * @description Return all the offer and their associated information
   * @return array The offer and their associated information
   */
  public function getAllOffer(): array {
    //TODO : adapte the output in a more user friendly format
    $query = $this->dbConn->prepare('SELECT * FROM offer');
    $query->execute();
    return $query->fetchAll();
  }

  /**
   * @description Return all the transaction and their associated information
   * @return array The transaction and their associated information
   */
  public function getAllTransaction(): array {
    //TODO : adapte the output in a more user friendly format
    $query = $this->dbConn->prepare('SELECT * FROM transaction_');
    $query->execute();
    return $query->fetchAll();
  }
}