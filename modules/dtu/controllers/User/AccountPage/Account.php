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
            echo (new AccountPageView())->render("Account - DealTonBUT", self::STYLESHEET);
        }
    }

    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }

}
