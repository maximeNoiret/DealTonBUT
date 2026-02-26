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
   * @param string $orderBy Type of the sort (eg : COST ( order by the cost of the offer ))
   * @param string $suffixe Supplementary information for the sort (eg : ASC ( Ascending order ))
   * @return array<mixed> The list of offers
   * @deprecated
   */
  public static function getOffers(): string {
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $limit = 8;

    //build the base query
    $query =
              'SELECT DISTINCT o.ouid ,u.username as \'username\', o.owner, title, description, price, deadline
       FROM offer o
       INNER JOIN user_ u
       ON o.owner = u.email
       LEFT JOIN tags t 
       ON t.ouid = o.ouid
       WHERE deadline > NOW()
       AND o.ouid NOT IN (
            SELECT ouid
            FROM transactions
       )';

    $query = self::SortOffers($query);

    // Add limit and offset
    $query .= " LIMIT $limit OFFSET $offset";

    // make the html for the offers, and the pagination
    return self::getOffersHtml($query);
  }

  /**
   * @description Add to the incomplete SQL query the order by clause in
   * function of the sort parameter, and the search string if it exists.
   * Meant to be used in the getOffers() method.
   * @param string $query the sql query that his under construction
   * @return string SQL query that include the order by clause in function of
   * the sort parameter, and the search string if it exists.
   */
  static function SortOffers( string $query): string
  {
    $sort = $_GET['sort'] ?? null;
    /**
     * @var array<string> $_GET['search-string']
     */
    if (isset($_GET['search-string']) && !empty($_GET['search-string'])) {
      $searchString = trim($_GET['search-string']);
      if(str_starts_with($searchString, '#')) {
        $tagname = substr($searchString, 1);
        $query.= " AND t.tagname LIKE '%" . $tagname . "%'";
      }
      else{
        $query.= ' AND (title LIKE "%' . $searchString . '%"';
        $query.= " OR description LIKE '%" . $searchString . "%')";
      }
    }
    switch ($sort) {
      case 'price-asc':
        $query .= " ORDER BY price ASC";
        break;
      case 'price-desc':
        $query .= " ORDER BY price DESC";
        break;
      case 'date':
        $query .= " ORDER BY creation_time DESC";
        break;
      case 'alphabetic':
        $query .= " ORDER BY title ASC";
        break;
      default:
        break;
    }
    return $query;
  }

  /**
   * @description This method is used to generate the html for the offers, and
   * the pagination. It is used in the getOffers() method.
   * @param string $query the sql query to get the offers and order them in
   * function of the sort parameter
   * @return string The html that contain the info of the offers, and the
   * pagination
   */
  static function getOffersHtml(string $query): string
  {
    $sort = $_GET['sort'] ?? null;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $limit = 8;

    $offers = DataBase::getInstance()->executeQuery($query);

    // Count total offers for pagination
    $countQuery = str_replace('SELECT DISTINCT o.ouid ,u.username as \'username\', o.owner, title, description, price, deadline', 'SELECT COUNT(*) as total', $query);
    $countResult = DataBase::getInstance()->executeQuery($countQuery);
    $totalOffers = $countResult ? (int)$countResult[0]['total'] : 0;

    // create the html for the offers
    if ($offers) {
      $ret = '<section class="offer-grid" id="offer-grid">' . "\n";
      foreach ($offers as $offer) {
        /**
         * @var array<string, string> $offer
         */
        // Add button based on ownership
        $offer['button'] = MarketPlace::generateOfferButton($offer['ouid'], $offer['owner']);
        $ret = $ret . new Offer($offer)->render('article', 'offer-card' . (AccountDB::ownsOffer($_SESSION['email'], (int)$offer['ouid']) ? ' own-offer' : '')). "\n";
      }
      $ret .= '</section>';

      // Add "Load More" button if there are more offers
      if ($totalOffers > $offset + $limit) {
        $nextOffset = $offset + $limit;
        $sortParam = $sort ? '&sort=' . urlencode($sort) : '';
        $searchParam = isset($_GET['search-string']) ? '&search-string=' . urlencode($_GET['search-string']) : '';
        $ret .= '<div class="load-more-container">';
        $ret .= '<button class="load-more-btn" onclick="loadMoreOffers(' . $nextOffset . ', \'' . htmlspecialchars($sortParam . $searchParam, ENT_QUOTES) . '\')">Plus d\'offres ?</button>';
        $ret .= '</div>';
      }

      return $ret;
    }
    return '<h1 class="description-text">There are no offers!</h1>';
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
      'SELECT owner, u.username as \'username\', title, description, price, deadline,o.type
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
                SELECT owner, price, deadline, type, quantity 
                FROM offer 
                WHERE ouid = :ouid
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

      if ($offer['type'] === 'offer') {
        $buyerQuery = $email;
        $sellerQuery = $offer['owner'];
      } else {
        $buyerQuery = $offer['owner'];
        $sellerQuery = $email;
      }

        // Si l'utilisateur n'as pas assez de sous
        $queryForBalance = AccountDB::getInstance()->getBalance($buyerQuery);
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
      $queryRemoveMoney->bindValue('email', $buyerQuery);
      $queryRemoveMoney->execute();

      $queryAddMoney = $this->dbConn->prepare('
                UPDATE user_ 
                SET balance = balance + :price 
                WHERE email = :email
            ');
      $queryAddMoney->bindValue('price', $offer['price']);
      $queryAddMoney->bindValue('email', $sellerQuery);
      $queryAddMoney->execute();

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
      'SELECT o.ouid, owner, u.username as \'username\', title, description, price, deadline, o.type
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
   * @return void
   */
  public function insertOffre(
    string $userEmail,
    string $title,
    float $price,
    string $description,
    string $deadline,
    int $quantity,
    string $type
  ): int {
    $query = $this->dbConn->prepare('
    INSERT INTO offer(owner, title, description, price, creation_time, deadline, quantity, type)
    VALUES (:owner, :title, :description, :price, :creation_time, :deadline, :quantity, :type)
');

    $query->bindValue('owner', $userEmail);
    $query->bindValue('title', $title);
    $query->bindValue('description', $description);
    $query->bindValue('price', $price);
    $query->bindValue('creation_time', date('Y-m-d H:i:s'));
    $query->bindValue('deadline', $deadline . ' 23:59:59');
    $query->bindValue('quantity', $quantity);
    $query->bindvalue('type', $type);
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
}