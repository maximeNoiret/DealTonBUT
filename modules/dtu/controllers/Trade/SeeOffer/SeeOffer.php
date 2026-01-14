<?php

namespace controllers\Trade\SeeOffer;


use core\controllers\Controller;
use dtu\models\TradeDB;
use dtu\views\Trade\SeeOffer\SeeOfferView;
use models\AccountDB;

class SeeOffer implements Controller
{
  const string PATH = '/offre/voir';
  const string METH = 'GET';

  const array STYLESHEET = [
    '/_assets/styles/offer.css',
    '/_assets/styles/seeOffer.css',
    '/_assets/styles/style.css',
    '/_assets/styles/navbar.css'
  ];

  /**
   * @var array<string, mixed> Offer details retrieved from the database.
   */
  static array $offer;
  static int $id;

  /**
   * SeeOffer class constructor.
   * Retrieves offer details from the database using the ID provided in GET parameters.
   */
  public function __construct() {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
      /**
       * @var array<string, int> $_GET
       */
      self::$id = $_GET['id'];
      /**
       * @var $offers array<mixed>
       */
      self::$offer = TradeDB::getInstance()->getOffer(self::$id);
      return;
    }
    self::$offer = [];
  }

  /**
   * Checks if the logged-in user is the offer owner.
   *
   * @return bool Returns true if the logged-in user is the offer owner, otherwise false.
   */
  public function isOwnerOfOffer(): bool {
    return isset($_SESSION['email']) && $_SESSION['email'] === self::$offer['owner'];
  }

  /**
   * Generates the appropriate action button for the offer.
   *
   * Returns a delete button if the current user is the offer owner,
   * otherwise returns a buy button.
   *
   * @return string Returns the appropriate HTML button code based on offer ownership.
   */
  public function buttonOffer(): string{
    if ($this->isOwnerOfOffer()) {
      return '<a class="button-delete" href="/offre/delete?id=' . self::$id . '">Delete</a>';
    } else if (!TradeDB::getInstance()->isOfferBought(self::$id)) {
      return '<a class="button-buy" href="/offre/buy?id=' . self::$id . '">Buy</a>';
    } else {
        return '';
    }
  }

  /**
   * Main control method executed for the offer page.
   *
   * Checks if the user is logged in before displaying the offer.
   * Redirects to the login page if the user is not authenticated.
   *
   * @return void
   */
  function control(): void
  {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      header('Location: /user/login');
    } else {
      echo (new SeeOfferView())->render("Offer - DealTonBUT", self::STYLESHEET);
    }
  }

  /**
   * Resolves whether the request matches this controller's route.
   *
   * @param string $path The request path to match against the controller's PATH constant.
   * @param string $meth The HTTP method to match against the controller's METH constant.
   * @return bool Returns true if the path and method match, false otherwise.
   */
  static function resolve(string $path, string $meth): bool
  {
    return strtok($path, '?') === static::PATH && $meth === static::METH;
  }
}