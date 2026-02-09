<?php
namespace views\Trade\SeeOtherAccount;

use core\views\AbstractView;
use dtu\models\TradeDB;
use models\Account;
use views\Trade\Offer\Offer;

class SeeOtherAccountView extends AbstractView {

  function path(): string
  {
    return __DIR__ . DIRECTORY_SEPARATOR . 'SeeOtherAccountTemplate.html';
  }

  function templateValues(): array
  {
    return [
//      'USERNAME' => $_SESSION['username'] ?? '',
      'EMAIL' => $_GET['email'] ?? '',
      'USEROFFERS' => $this->getOfferByUser($_SESSION['email'] ?? ''),
      'USERBALANCE' => 0,
      'USERBOUGHTOFFERS' => $this->getOfferBoughtByUser($_GET['email'] ?? ''),
      'NAME' => Account::getName($_GET['email'] ?? ''),
    ];
  }

  //TODO: find a way to not have to reinvent the wheel for these function
  function getOfferByUser($email) : string {
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
  function getOfferBoughtByUser($email): string
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

  function navbarText(): string
  {
    return 'Compte de ' . ($_GET['email'] ?? '');
  }

}