<?php

namespace controllers\Trade\MarketPlace;

use core\controllers\Controller;
use core\models\DataBase;
use dtu\models\TradeDB;
use models\AccountDB;
use views\Trade\MarketPlace\MarketPlaceView;
use views\Trade\Offer\Offer;
class MarketPlace implements Controller {
  
  public const PATH = '/marketplace';
  public const METH = 'GET';
  /**
   * @description Store all the different stylesheet used
   * @var array<string> STYLESHEET
   */
    const array STYLESHEET = [
        '/_assets/styles/MarketPlace.css',
        '/_assets/styles/style.css',
        '/_assets/styles/navbar.css',
        '/_assets/styles/offer.css'
    ];

  function control(): void {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      header('Location: /user/login');
    } else {
      echo (new MarketPlaceView())->render("Place de Marché - DealTonBUT", static::STYLESHEET);
    }
  }

  /**
   * @description Show The offers in function of the sort parameter, show all offers by default
   * @return string The HTML that contain the info of the offers
   */
  public static function getOffers(): string {
    $sort = $_GET['sort'] ?? null;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $limit = 8;

    $query =
      'SELECT DISTINCT o.ouid ,u.username as \'username\', o.owner, title, description, price, deadline, o.quantity
       FROM offer o
       INNER JOIN user_ u
       ON o.owner = u.email
       INNER JOIN tags t 
       ON t.ouid = o.ouid
       WHERE deadline > NOW()
       AND quantity >= 0';
    //  for searching a string in the title of the offers

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

    // Count total offers for pagination
    $countQuery = str_replace('SELECT DISTINCT o.ouid ,u.username as \'username\', o.owner, title, description, price, deadline', 'SELECT COUNT(*) as total', $query);
    $countResult = DataBase::getInstance()->executeQuery($countQuery);
    $totalOffers = $countResult ? (int)$countResult[0]['total'] : 0;

    // Add limit and offset
    $query .= " LIMIT $limit OFFSET $offset";

    $offers = DataBase::getInstance()->executeQuery($query);
    if ($offers) {
      $ret = '<section class="offer-grid" id="offer-grid">' . "\n";
      foreach ($offers as $offer) {
        /**
         * @var array<string, string> $offer
         */
        // Add button based on ownership
        $offer['button'] = self::generateOfferButton($offer['ouid'], $offer['owner']);
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
   * @description Generates the appropriate action button for an offer
   * @param int $offerId The offer ID
   * @param string $ownerEmail The email of the offer owner
   * @return string Returns the appropriate HTML button code based on offer ownership
   */
  private static function generateOfferButton(int $offerId, string $ownerEmail): string {
      if (isset($_SESSION['email']) && $_SESSION['email'] === $ownerEmail) {
          return '<a class="button-delete" href="/offre/delete?id=' . $offerId . '">Delete</a>';
      }

      $offer = TradeDB::getInstance()->getOffer($offerId);
      if (!$offer) return '';

      $quantity = $offer['quantity'] ?? 0;
      if ($quantity < 0) {
          return '';
      }
      $type = $offer['type'] ?? 'offer';
      if ($type === 'request') {
          return '<a class="button-accept" href="/offre/buy?id=' . $offerId . '">Accept</a>';
      } else {
          return '<a class="button-buy" href="/offre/buy?id=' . $offerId . '">Buy</a>';
      }
  }

  public static function resolve(string $path, string $meth): bool {
    return strtok($path, '?') === static::PATH && $meth === static::METH;
  }
}
