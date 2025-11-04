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

  static array $offer;

  /**
   * Constructeur de la classe SeeOffer.
   * Récupère les détails de l'offre à partir de la base de données en utilisant l'ID fourni dans les paramètres GET.
   */
  public function __construct() {
      if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
          self::$offer = [];
          return;
      }
    self::$offer = DataBase::getInstance()->getOffer($_GET['id']);
  }

  /**
   * @return bool Retourne true si l'utilisateur connecté est le propriétaire de l'offre, sinon false.
   */
  public function isOwnerOfOffer(): bool {
    return isset($_SESSION['email']) && $_SESSION['email'] === self::$offer['owner'];
  }

  /**
   * @return string Retourne le code HTML du bouton approprié en fonction de la propriété de l'offre.
   */
  public function buttonOffer(): string{
    if ($this->isOwnerOfOffer()) {
      return '<a class="button-delete" href="/offre/delete?id=' . $_GET['id'] . '">Delete</a>';
    } else {
      return '<a class="button-buy" href="/offre/buy?id=' . $_GET['id'] . '">Buy</a>';
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