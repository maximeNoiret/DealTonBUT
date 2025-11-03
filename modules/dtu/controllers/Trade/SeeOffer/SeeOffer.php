<?php

namespace controllers\Trade\SeeOffer;


use dtu\views\Trade\SeeOffer\SeeOfferView;
use models\DataBase;

class SeeOffer
{
  const string PATH = '/offre/voir';
  const string METH = 'GET';

  const array STYLESHEET = [
    '/_assets/styles/addOffer.css',
    '/_assets/styles/style.css',
    '/_assets/styles/navbar.css'
  ];

  static array $offer;

  public function __construct() {
    self::$offer = DataBase::getInstance()->getOffer($_GET['id']);
  }

  public function isOwnerOfOffer(): bool {
    return isset($_SESSION['email']) && $_SESSION['email'] === self::$offer['owner'];
  }

  public function ButtonOffer()
  {
    if ($this->isOwnerOfOffer()) {
      return '<a class="button-delete" href="/offre/delete?id=' . self::$offer['id'] . '">Delete</a>';
    } else {
      return '<a class="button-buy" href="/offre/buy?id=' . self::$offer['id'] . '">Buy</a>';
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
    return $path === self::PATH && $meth === self::METH;
  }
}