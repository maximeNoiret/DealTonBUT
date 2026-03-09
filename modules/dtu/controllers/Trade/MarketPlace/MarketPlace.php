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
   * @description Generates the appropriate action button for an offer
   * @param int $offerId The offer ID
   * @param string $ownerEmail The email of the offer owner
   * @return string Returns the appropriate HTML button code based on offer ownership
   */
  public static function generateOfferButton(int $offerId, string $ownerEmail): string {
      if (isset($_SESSION['email']) && $_SESSION['email'] === $ownerEmail) {
          return '<a class="button-delete" href="/offre/delete?id=' . $offerId . '">Delete</a>';
      }

      $offer = TradeDB::getInstance()->getOffer($offerId);
      if (!$offer) return '';

      /** @var array<string, mixed> $offer */
      $quantity = is_numeric($offer['quantity'] ?? null) ? (int) $offer['quantity'] : 0;
      if ($quantity < 0) {
          return '';
      }
      $type = is_string($offer['type'] ?? null) ? $offer['type'] : 'offer';
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
