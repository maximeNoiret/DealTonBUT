<?php

namespace controllers\Trade\SeeOffer;


use dtu\views\Trade\SeeOffer\SeeOfferView;
use models\DataBase;

class SeeOffer
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
      self::$offer = DataBase::getInstance()->getOffer(self::$id);
      return;
    }
    self::$offer = [];
  }

  /**
   * @return bool Returns true if the logged-in user is the offer owner, otherwise false.
   */
  public function isOwnerOfOffer(): bool {
    return isset($_SESSION['email']) && $_SESSION['email'] === self::$offer['owner'];
  }

  /**
   * @return string Returns the appropriate HTML button code based on offer ownership.
   */
  public function buttonOffer(): string{
    if ($this->isOwnerOfOffer()) {
      return '<a class="button-delete" href="/offre/delete?id=' . self::$id . '">Delete</a>';
    } else {
      return '<a class="button-buy" href="/offre/buy?id=' . self::$id . '">Buy</a>';
    }
  }
  function control(): void
  {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      header('Location: /user/login');
    } else {
      echo (new SeeOfferView())->render("Offer - DealTonBUT", self::STYLESHEET);
    }
  }

  static function resolve(string $path, string $meth): bool
  {
    return strtok($path, '?') === static::PATH && $meth === static::METH;
  }
}