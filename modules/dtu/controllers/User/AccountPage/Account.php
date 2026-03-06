<?php

namespace controllers\User\AccountPage;

use core\controllers\Controller;
use dtu\models\TradeDB;
use models\AccountDB;
use views\Trade\Offer\Offer;
use views\User\AccountPage\AccountPageView;
use views\User\LoginForm\LoginFormView;

class Account implements Controller
{
    const string PATH = '/user/account';

    const string METH = 'GET';

    const array STYLESHEET = [
        '/_assets/styles/Account.css',
        '/_assets/styles/style.css',
        '/_assets/styles/navbar.css',
        '/_assets/styles/offer.css'
    ];

  /**
   * @description Show the offer made by the user
   * @return string
   */
  static function getUserOffers(): string {
    /**
     * @var string $email : The email of the user
     */
      $email = $_SESSION['email'] ?? '';
    $offers = TradeDB::getInstance()->getUserOffers($email);
    if ($offers) {
      $ret = '<section class="offer-grid">' . "\n";
      foreach ($offers as $offer) {
        /**
         * @var array<string, string> $offer
         */
        // Add button based on ownership
        $offer['button'] = self::generateOfferButton($offer['ouid'], $offer['owner']);
        $ret = $ret . (new Offer($offer))->renderWithLink('article', 'offer-card', '/offre/voir?id=' . $offer['ouid']);
      }
      return $ret . '</section>';
    }
    return '<h1 class="description-text">There are no offers!</h1>';
  }

  /**
   * @description Show the offer brought by the user
   * @return string
   */
  static function getUserBoughtOffers(): string
  {
    /**
     * @var string $email : The email of the user
     */
    $email = $_SESSION['email'] ?? '';
    $offers = TradeDB::getInstance()->getBoughtOffers($email);
    if ($offers) {
      $ret = '<section class="offer-grid">' . "\n";
      foreach ($offers as $offer) {
        /**
         * @var array<string, string> $offer
         */
        // Add button based on ownership
          $chatUrl = '/chat?ouid=' . $offer['ouid'] . '&email=' . urlencode($email);
          $offer['button'] = '<a class="button-chat" href="' . $chatUrl . '">Contacter le vendeur</a>';

          $ret = $ret . (new Offer($offer))->renderWithLink('article', 'offer-card', '/offre/voir?id=' . $offer['ouid']);
      }
      return $ret . '</section>';
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

    if (TradeDB::getInstance()->isOfferBought($offerId)) {
        return '';
    }

    $type = $offer['type'] ?? 'offer';
    if ($type === 'request') {
        return '<a class="button-accept" href="/offre/buy?id=' . $offerId . '">Accept</a>';
    } else {
        return '<a class="button-buy" href="/offre/buy?id=' . $offerId . '">Buy</a>';
    }
  }

  /**
   * @description Retrieves the offers associated with a user email.
   * @param $email string email address of the user
   * @return string html code of the offers associated with the given email or a message if no offers are found
   */
  static function getOfferByOtherUser($email) : string {
    $offers = TradeDB::getInstance()->getUserOffers($email);
    if ($offers) {
      $ret = '<section class="offer-grid">' . "\n";
      foreach ($offers as $offer) {
        /**
         * @var array<string, string> $offer
         */
        $offer['button'] = self::generateOfferButton($offer['ouid'], $offer['owner']);
        $ret = $ret . (new Offer($offer))->renderWithLink('article', 'offer-card', '/offre/voir?id=' . $offer['ouid']);
      }
      return $ret . '</section>';
    }
    return '<h1 class="description-text">There are no offers!</h1>';
  }

  /**
   * @description Retrieves the offers bought by a user associated with the given email.
   * @param $email string email address of the user
   * @return string html code of the offers bought by the user associated with the given email or a message if no offers are found
   */
  static function getOfferBoughtByOtherUser($email): string
  {
    $offers = TradeDB::getInstance()->getBoughtOffers($email);
    if ($offers) {
      $ret = '<section class="offer-grid">' . "\n";
      foreach ($offers as $offer) {
        /**
         * @var array<string, string> $offer
         */
        $ret = $ret . (new Offer($offer))->renderWithLink('article', 'offer-card', '/offre/voir?id=' . $offer['ouid']);
      }
      return $ret . '</section>';
    }
    return '<h1 class="description-text">There are no offers!</h1>';
  }

    function control(): void
    {
        if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
            echo (new LoginFormView())->render("Login - DealTonBUT", self::STYLESHEET);
        } else {
            $flash = $_GET['error'] ?? null;
            if(isset($_GET['success'])) {
                $flash = 'update_success';
            }
            $view = new AccountPageView();
            $view->setFlash($flash);
            echo $view->render("Account - DealTonBUT", self::STYLESHEET);
        }
    }

    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }

}
